<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use Config\Database;
use App\Models\ClientOptionsModel;
use App\Models\OptionModel;
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

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->usersModel = new UsersModel();
        $this->mvtModel = new MvtCompteModel();
        $this->clientOptionsModel = new ClientOptionsModel();
        $this->optionModel = new OptionModel();
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

    public function acheterRegimeSport(): ResponseInterface
    {
        $clientId = (int)session()->get('user_id') ?? 0;
        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        if (!$this->validate([
            'regime_id' => 'required|integer|greater_than[0]',
            'sport_id' => 'required|integer|greater_than[0]',
            'objectif_id' => 'required|integer|greater_than[0]',
            'duree' => 'required|integer|greater_than[0]',
            'prix' => 'required|numeric|greater_than[0]',
            'mode_achat' => 'required|in_list[normal,gold]'
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Données invalides: ' . implode(', ', $this->validator->getErrors()));
        }

        $regimeId = (int)$this->request->getPost('regime_id');
        $sportId = (int)$this->request->getPost('sport_id');
        $objectifId = (int)$this->request->getPost('objectif_id');
        $duree = (int)$this->request->getPost('duree');
        $prix = (float)$this->request->getPost('prix');
        $modeAchat = (string) $this->request->getPost('mode_achat');

        $goldOption = $this->optionModel->getGoldOption();
        $goldRemise = $this->optionModel->getGoldRemise();
        $goldOptionPrice = $this->optionModel->getGoldPrixOption();
        $hasGold = $this->clientOptionsModel->hasGoldOption($clientId);

        $pricing = $this->getPricingContext($prix, $modeAchat === 'gold');
        $prixRegimeGold = $pricing['prix_regime_gold'];
        $prixRegimeAchat = $modeAchat === 'gold' || $hasGold ? $prixRegimeGold : $prix;
        $totalADelever = $modeAchat === 'gold' && !$hasGold ? $pricing['prix_total'] : $prixRegimeAchat;

        $soldeClient = $this->mvtModel->getSoldeClient($clientId);
        if ($soldeClient < $totalADelever) {
            $montantManquant = $totalADelever - $soldeClient;
            return redirect()->back()->with('error',
                "Solde insuffisant. Vous avez " . number_format($soldeClient, 2) . "€ mais il en faut " . number_format($totalADelever, 2) . "€. Montant manquant: " . number_format($montantManquant, 2) . "€"
            );
        }

        $db = Database::connect();

        try {
            $db->transStart();

            if ($modeAchat === 'gold' && !$hasGold) {
                if (empty($goldOption['id']) || $goldOptionPrice <= 0) {
                    throw new \Exception('Option Gold introuvable');
                }

                $optionInserted = $this->clientOptionsModel->activateGoldSubscription($clientId, (int)$goldOption['id']);
                if (!$optionInserted) {
                    throw new \Exception('Impossible d\'activer Gold');
                }

                $goldMovement = $this->mvtModel->recordTransaction($clientId, 'debit', $goldOptionPrice);
                if (!$goldMovement) {
                    throw new \Exception('Impossible d\'enregistrer le paiement Gold');
                }
            }

            $purchaseOk = $this->purchaseModel->createPurchaseOnConnection($db, $clientId, $regimeId, $sportId, $objectifId, $duree, $prixRegimeAchat);
            if (!$purchaseOk) {
                throw new \Exception('Impossible d\'enregistrer l\'achat du régime');
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

            return redirect()->to('/portefeuille')->with('success', $message . number_format($newBalance, 2) . '€');
        } catch (\Exception $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }
            log_message('error', 'Erreur achat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'achat: ' . $e->getMessage());
        }
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
            return redirect()->back()->with('success', 'Vous êtes déjà abonné à Gold');
        }

        $goldOptionPrice = (float) ($goldOption['prix_option'] ?? 0);
        $soldeClient = $this->mvtModel->getSoldeClient($clientId);

        if ($soldeClient < $goldOptionPrice) {
            return redirect()->back()->with('error', 'Solde insuffisant pour souscrire à Gold. Montant requis: ' . number_format($goldOptionPrice, 2) . '€');
        }

        $db = Database::connect();

        try {
            $db->transStart();

            $optionInserted = $this->clientOptionsModel->activateGoldSubscription($clientId, (int)$goldOption['id']);
            if (!$optionInserted) {
                throw new \Exception('Impossible d\'activer Gold');
            }

            $movementOk = $this->mvtModel->recordTransaction($clientId, 'debit', $goldOptionPrice);
            if (!$movementOk) {
                throw new \Exception('Impossible d\'enregistrer le paiement Gold');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de l\'activation Gold');
            }

            $newBalance = $this->mvtModel->getSoldeClient($clientId);
            session()->set('solde', $newBalance);

            return redirect()->back()->with('success', 'Gold activé avec succès. Solde actuel: ' . number_format($newBalance, 2) . '€');
        } catch (\Exception $e) {
            if (isset($db) && $db->transStatus() !== false) {
                $db->transRollback();
            }

            log_message('error', 'Erreur souscription Gold: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la souscription Gold: ' . $e->getMessage());
        }
    }
}
