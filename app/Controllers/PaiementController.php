<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use App\Models\ClientOptionsModel;
use App\Models\OptionModel;
use App\Models\RegimesModel;
use App\Models\UsersModel;
use App\Models\PurchaseModel;
use App\Models\MvtCompteModel;

class PaiementController extends BaseController
{
    private PurchaseModel $purchaseModel;
    private UsersModel $usersModel;
    private MvtCompteModel $mvtModel;
    private ClientOptionsModel $clientOptionsModel;
    private OptionModel $optionModel;
    private RegimesModel $regimesModel;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->usersModel = new UsersModel();
        $this->mvtModel = new MvtCompteModel();
        $this->clientOptionsModel = new ClientOptionsModel();
        $this->optionModel = new OptionModel();
        $this->regimesModel = new RegimesModel();
    }

    private function getPricingContext(float $prixOriginal, bool $withGold): array
    {
        $goldRemise = $this->optionModel->getGoldRemise();
        $goldOptionPrice = $this->optionModel->getGoldPrixOption();
        $prixRegimeGold = $this->optionModel->getDiscountedPrice($prixOriginal, $goldRemise);

        return [
            'gold_remise' => $goldRemise,
            'gold_option_price' => $goldOptionPrice,
            'prix_regime_gold' => $prixRegimeGold,
            'prix_total' => $withGold ? round($prixRegimeGold + $goldOptionPrice, 2) : round($prixOriginal, 2),
        ];
    }

    private function calculateRegimePrice(int $regimeId, int $duree): ?float
    {
        $regime = $this->regimesModel->getRegimeWithDetails($regimeId);

        if (!$regime || $duree <= 0) {
            return null;
        }

        return round(((float) $regime->prix_par_jour) * $duree, 2);
    }

    public function acheterRegimeSport(): ResponseInterface
    {
        $clientId = (int) (session()->get('user_id') ?? 0);
        $userRole = (string) (session()->get('role') ?? '');

        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        if ($userRole !== 'user') {
            return redirect()->back()->with('error', 'Seuls les utilisateurs clients peuvent acheter des régimes');
        }

        if (
            !$this->validate([
                'regime_id' => 'required|integer|greater_than[0]',
                'sport_id' => 'required|integer|greater_than[0]',
                'objectif_id' => 'required|integer|greater_than[0]',
                'duree' => 'required|integer|greater_than[0]',
                'mode_achat' => 'required|in_list[normal,gold]',
            ])
        ) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Données invalides: ' . implode(', ', $this->validator->getErrors()));
        }

        $regimeId = (int) $this->request->getPost('regime_id');
        $sportId = (int) $this->request->getPost('sport_id');
        $objectifId = (int) $this->request->getPost('objectif_id');
        $duree = (int) $this->request->getPost('duree');
        $modeAchat = (string) $this->request->getPost('mode_achat');

        if ($duree <= 0) {
            return redirect()->back()->with('error', 'Durée invalide');
        }

        $basePrice = $this->calculateRegimePrice($regimeId, $duree);
        if ($basePrice === null) {
            return redirect()->back()->with('error', 'Régime introuvable ou durée invalide.');
        }

        $db = Database::connect();
        $goldOption = $this->optionModel->getGoldOption();
        $goldRemise = $this->optionModel->getGoldRemise();
        $goldOptionPrice = $this->optionModel->getGoldPrixOption();
        $hasGold = $this->clientOptionsModel->hasGoldOption($clientId);

        $pricing = $this->getPricingContext($basePrice, $modeAchat === 'gold');
        $prixRegimeGold = $pricing['prix_regime_gold'];
        $prixRegimeAchat = $modeAchat === 'gold' || $hasGold ? $prixRegimeGold : $basePrice;
        $totalADelever = $modeAchat === 'gold' && !$hasGold ? $pricing['prix_total'] : $prixRegimeAchat;

        $soldeClient = $this->mvtModel->getSoldeClient($clientId);
        if ($soldeClient < $totalADelever) {
            $montantManquant = $totalADelever - $soldeClient;

            return redirect()->back()->with(
                'error',
                'Solde insuffisant. Vous avez ' . number_format($soldeClient, 2) . '€ mais il en faut ' . number_format($totalADelever, 2) . '€. Montant manquant: ' . number_format($montantManquant, 2) . '€'
            );
        }

        $dateAchat = date('Y-m-d');
        $existingPurchase = $this->purchaseModel->findExistingPurchase($clientId, $regimeId, $sportId, $objectifId, $dateAchat);
        if ($existingPurchase !== null) {
            $existingDuree = (int) ($existingPurchase['duree'] ?? $duree);
            $existingLabel = 'Vous avez déjà acheté ce régime aujourd\'hui';
            $existingDetails = 'Régime #' . $regimeId . ', sport #' . $sportId . ', objectif #' . $objectifId . ', durée ' . $existingDuree . ' jour(s), date ' . $dateAchat;

            log_message('info', 'Achat déjà existant détecté pour client ' . $clientId . ' : ' . $existingDetails);

            return redirect()->back()->with(
                'error',
                $existingLabel . ' — ' . $existingDetails . '.'
            );
        }

        try {
            $db->transStart();

            if ($modeAchat === 'gold' && !$hasGold) {
                if (empty($goldOption['id']) || $goldOptionPrice <= 0) {
                    throw new \Exception('Option Gold introuvable');
                }

                $optionInserted = $this->clientOptionsModel->activateGoldSubscription($clientId, (int) $goldOption['id']);
                if (!$optionInserted) {
                    throw new \Exception('Impossible d\'activer Gold');
                }

                $goldMovement = $this->mvtModel->recordTransactionFull(
                    $clientId,
                    'debit',
                    $goldOptionPrice,
                    'souscription_gold',
                    null,
                    null,
                    'Souscription à l\'option Gold'
                );
                if (!$goldMovement) {
                    throw new \Exception('Impossible d\'enregistrer le paiement Gold');
                }
            }

            $purchaseOk = $this->purchaseModel->createPurchaseOnConnection($db, $clientId, $regimeId, $sportId, $objectifId, $duree, $prixRegimeAchat);
            if (!$purchaseOk) {
                throw new \Exception('Impossible d\'enregistrer l\'achat du régime pour le moment');
            }

            $regimeMovement = $db->table('mvt_compte')->insert([
                'client_id' => $clientId,
                'type_transaction' => 'debit',
                'date_mouvement' => date('Y-m-d H:i:s'),
                'montant' => $prixRegimeAchat,
                'mouvement_type' => 'achat_regime',
                'regime_id' => $regimeId,
                'sport_id' => $sportId,
                'description' => 'Achat régime: ' . $duree . ' jour(s)'
            ]);

            if (!$regimeMovement) {
                throw new \Exception('Impossible d\'enregistrer le paiement du régime');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de la transaction');
            }

            $newBalance = $this->mvtModel->getSoldeClient($clientId);
            session()->set('solde', $newBalance);

            $message = $modeAchat === 'gold' || $hasGold
                ? 'Achat effectué avec succès! Remise Gold appliquée (' . number_format($goldRemise, 2) . '%). Solde actuel: '
                : 'Achat effectué avec succès! Solde actuel: ';

            return redirect()->back()->with('purchase_success', $message . number_format($newBalance, 2) . '€');
        } catch (\Exception $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }

            $message = $this->formatPurchaseExceptionMessage($e, $clientId, $regimeId, $sportId, $objectifId, $duree, $prixRegimeAchat);

            log_message('error', 'Erreur achat: ' . $message);
            return redirect()->back()->with('purchase_error', $message);
        }
    }

    private function formatPurchaseExceptionMessage(\Throwable $exception, int $clientId, int $regimeId, int $sportId, int $objectifId, int $duree, float $prix): string
    {
        $rawMessage = $exception->getMessage();

        if (stripos($rawMessage, 'Duplicate entry') !== false || stripos($rawMessage, 'unique_regime_sport_client') !== false) {
            return 'Achat déjà enregistré : ce client a déjà acheté ce régime aujourd\'hui. Détails: client #' . $clientId . ', régime #' . $regimeId . ', sport #' . $sportId . ', objectif #' . $objectifId . ', durée ' . $duree . ' jour(s), montant ' . number_format($prix, 2) . '€.';
        }

        return 'Erreur lors de l\'achat: ' . $rawMessage;
    }

    public function testPayement(): ResponseInterface|string
    {
        if (!session()->get('user_id')) {
            session()->set([
                'user_id' => 1,
                'role' => 'user',
                'logged_in' => true
            ]);
        }

        return view('TestPayement');
    }

    public function souscrireGold(): ResponseInterface
    {
        $clientId = (int) session()->get('user_id') ?? 0;
        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        $goldOption = $this->optionModel->getGoldOption();
        if (!$goldOption) {
            return redirect()->back()->with('error', 'Option Gold introuvable');
        }

        if ($this->clientOptionsModel->hasGoldOption($clientId)) {
            return redirect()->back()->with('purchase_success', 'Vous êtes déjà abonné à Gold');
        }

        $goldOptionPrice = (float) ($goldOption['prix_option'] ?? 0);
        $soldeClient = $this->mvtModel->getSoldeClient($clientId);

        if ($soldeClient < $goldOptionPrice) {
            return redirect()->back()->with('error', 'Solde insuffisant pour souscrire à Gold. Montant requis: ' . number_format($goldOptionPrice, 2) . '€');
        }

        $db = Database::connect();

        try {
            $db->transStart();

            $optionInserted = $this->clientOptionsModel->activateGoldSubscription($clientId, (int) $goldOption['id']);
            if (!$optionInserted) {
                throw new \Exception('Impossible d\'activer Gold');
            }

            // Enregistre le mouvement GOLD avec traçabilité
            $movementOk = $this->mvtModel->recordTransactionFull(
                $clientId,
                'debit',
                $goldOptionPrice,
                'souscription_gold',
                null,
                null,
                'Souscription à l\'option Gold'
            );
            if (!$movementOk) {
                throw new \Exception('Impossible d\'enregistrer le paiement Gold');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de l\'activation Gold');
            }

            $newBalance = $this->mvtModel->getSoldeClient($clientId);
            session()->set('solde', $newBalance);

            return redirect()->back()->with('purchase_success', 'Gold activé avec succès. Solde actuel: ' . number_format($newBalance, 2) . '€');
        } catch (\Exception $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }

            log_message('error', 'Erreur souscription Gold: ' . $e->getMessage());
            return redirect()->back()->with('purchase_error', 'Erreur lors de la souscription Gold: ' . $e->getMessage());
        }
    }
}
