<?php
namespace App\Controllers;

use App\Models\ClientOptionsModel;
use App\Models\OptionModel;
use App\Models\PurchaseModel;
use App\Models\RegimeSportModel;
use App\Models\ClientObjectifsModel;
use App\Models\ObjectifsModel;
use App\Models\UsersModel;

class RegimeSportController extends BaseController
{
    protected $regimeSportModel;
    protected $clientObjectifsModel;
    protected $objectifsModel;
    protected $usersModel;
    protected $clientOptionsModel;
    protected $optionModel;
    protected $purchaseModel;

    public function __construct()
    {
        $this->regimeSportModel = new RegimeSportModel();
        $this->clientObjectifsModel = new ClientObjectifsModel();
        $this->objectifsModel = new ObjectifsModel();
        $this->usersModel = new UsersModel();
        $this->clientOptionsModel = new ClientOptionsModel();
        $this->optionModel = new OptionModel();
        $this->purchaseModel = new PurchaseModel();
    }

    /**
     * Affiche la liste des couples régimes et sports.
     * Si l'utilisateur est connecté, ajoute les informations personnalisées.
     */
    public function getRegimeSport()
    {
        $selectedUserId = session()->get('user_id');
        $regimesSports = $this->regimeSportModel->getAllCombinaisons();
        $userObjectif = null;
        $selectedUser = null;
        $clientOption = null;
        $goldOption = $this->optionModel->getByLibelle('gold');
        $goldRemisePreview = (float) ($goldOption['remise'] ?? 0);
        $goldOptionPrice = (float) ($goldOption['prix_option'] ?? 0);
        $hasGold = false;

        $priseCount = 0;
        $perteCount = 0;

        foreach ($regimesSports as $combinaison) {
            $impact = (float) ($combinaison['impact_journalier'] ?? 0);

            if ($impact > 0) {
                $priseCount++;
            } elseif ($impact < 0) {
                $perteCount++;
            }
        }

        if ($selectedUserId) {
            $selectedUserId = (int) $selectedUserId;
            $selectedUser = $this->usersModel->find($selectedUserId);
            $clientOption = $this->clientOptionsModel->getLatestOptionByClient($selectedUserId);
            $goldRemiseClient = $this->clientOptionsModel->getGoldRemiseForClient($selectedUserId);
            $hasGold = $goldRemiseClient > 0;

            if ($selectedUser) {
                // Récupère le dernier objectif enregistré pour l'utilisateur sélectionné
                $userObjectifRow = $this->clientObjectifsModel->getLatestObjectifByClient($selectedUserId);

                if (!empty($userObjectifRow)) {
                    $objectif = $this->objectifsModel->find($userObjectifRow['objectif_id']);

                    if ($objectif) {
                        $userObjectif = $objectif['libelle'];
                        // Filtre les régimes selon l'objectif
                        $regimesSports = $this->regimeSportModel->suggestByObjectif($userObjectif);
                    }
                }
                // Si on a un objectif et un action_poids, calculer la durée (jours) par combinaison
                if (!empty($userObjectifRow) && isset($userObjectifRow['action_poids'])) {
                    $actionPoids = (float) $userObjectifRow['action_poids'];
                    $enhanced = [];

                    foreach ($regimesSports as $rs) {
                        // normalize to array for easier manipulation
                        $row = is_array($rs) ? $rs : (array) $rs;

                        // impact journalier
                        $impact = null;
                        if (isset($row['impact_journalier'])) {
                            $impact = (float) $row['impact_journalier'];
                        }

                        // déterminer le prix journalier à utiliser : uniquement depuis le régime
                        $rowPrixJournalier = 0.0;
                        if (isset($row['prix_par_jour']) && $row['prix_par_jour'] !== '') {
                            $rowPrixJournalier = (float) $row['prix_par_jour'];
                        } elseif (isset($row['prix_journalier']) && $row['prix_journalier'] !== '') {
                            $rowPrixJournalier = (float) $row['prix_journalier'];
                        }

                        if ($impact === 0.0 || $impact === null) {
                            $row['duree_jours'] = null;
                            $row['cout_total'] = null;
                            $row['cout_total_original'] = null;
                            $row['cout_total_gold'] = null;
                            $row['cout_total_gold_with_option'] = null;
                        } else {
                            $duree = (int) ceil(abs($actionPoids) / max(abs($impact), 0.00001));
                            $row['duree_jours'] = $duree;

                            if ($rowPrixJournalier > 0) {
                                $coutOriginal = round($rowPrixJournalier * $duree, 2);
                                $coutGold = $goldRemisePreview > 0
                                    ? round($coutOriginal * (1 - ($goldRemisePreview / 100)), 2)
                                    : $coutOriginal;
                                $coutGoldWithOption = $goldOptionPrice > 0
                                    ? round($coutGold + $goldOptionPrice, 2)
                                    : $coutGold;

                                $row['cout_total_original'] = $coutOriginal;
                                $row['cout_total_gold'] = $coutGold;
                                $row['cout_total_gold_with_option'] = $coutGoldWithOption;
                                $row['cout_total'] = $hasGold ? $coutGold : $coutOriginal;
                            } else {
                                $row['cout_total'] = null;
                                $row['cout_total_original'] = null;
                                $row['cout_total_gold'] = null;
                                $row['cout_total_gold_with_option'] = null;
                            }
                        }

                        $row['gold_active'] = $hasGold;
                        $row['gold_remise'] = $goldRemisePreview;
                        $row['gold_remise_client'] = $goldRemiseClient;
                        $row['gold_option_price'] = $goldOptionPrice;
                        $row['buy_enabled'] = true;

                        $enhanced[] = $row;
                    }

                    $regimesSports = $enhanced;
                }
            } else {
                $clientOption = null;
            }
        }

        if (!$selectedUserId) {
            $enhanced = [];

            foreach ($regimesSports as $rs) {
                $row = is_array($rs) ? $rs : (array) $rs;
                $impact = isset($row['impact_journalier']) ? (float) $row['impact_journalier'] : null;
                $rowPrixJournalier = 0.0;

                if (isset($row['prix_par_jour']) && $row['prix_par_jour'] !== '') {
                    $rowPrixJournalier = (float) $row['prix_par_jour'];
                } elseif (isset($row['prix_journalier']) && $row['prix_journalier'] !== '') {
                    $rowPrixJournalier = (float) $row['prix_journalier'];
                }

                $row['duree_jours'] = null;
                $row['cout_total'] = null;
                $row['cout_total_original'] = null;
                $row['cout_total_gold'] = null;
                $row['cout_total_gold_with_option'] = null;

                if ($impact !== null && $rowPrixJournalier > 0) {
                    $row['duree_jours'] = 0;
                    $row['cout_total'] = $rowPrixJournalier;
                    $row['cout_total_original'] = $rowPrixJournalier;
                    $row['cout_total_gold'] = $goldRemisePreview > 0
                        ? round($rowPrixJournalier * (1 - ($goldRemisePreview / 100)), 2)
                        : $rowPrixJournalier;
                    $row['cout_total_gold_with_option'] = $goldOptionPrice > 0
                        ? round($row['cout_total_gold'] + $goldOptionPrice, 2)
                        : $row['cout_total_gold'];
                }

                $row['gold_active'] = $hasGold;
                $row['gold_remise'] = $goldRemisePreview;
                $row['gold_remise_client'] = $hasGold ? $goldRemisePreview : 0;
                $row['gold_option_price'] = $goldOptionPrice;
                $row['buy_enabled'] = false;

                $enhanced[] = $row;
            }

            $regimesSports = $enhanced;
        }

        // Récupère tous les objectifs pour la sélection si l'utilisateur n'en a pas
        $allObjectives = $this->objectifsModel->findAll();
        $showObjectiveSelector = false;

        // Affiche le sélecteur d'objectif si : pas connecté OU connecté sans objectif
        if (!$selectedUserId || ($selectedUserId && empty($userObjectif))) {
            $showObjectiveSelector = true;
        }

        $data = [
            'title' => 'Couples régimes - sports',
            'regimesSports' => $regimesSports,
            'userObjectif' => $userObjectif,
            'selectedUserId' => $selectedUserId,
            'selectedUser' => $selectedUser,
            'clientOption' => $clientOption,
            'goldOption' => $goldOption,
            'goldRemisePreview' => $goldRemisePreview,
            'goldOptionPrice' => $goldOptionPrice,
            'isLoggedIn' => (bool) session()->get('logged_in'),
            'priseCount' => $priseCount,
            'perteCount' => $perteCount,
            'totalCombinaisons' => count($regimesSports),
            'allObjectives' => $allObjectives,
            'showObjectiveSelector' => $showObjectiveSelector,
        ];

        return view('regime_sport/liste', $data);
    }

    /**
     * Sauvegarde l'objectif sélectionné par l'utilisateur.
     */
    public function setObjectif()
    {
        $objectifId = (int) $this->request->getPost('objectif_id');
        $actionPoids = (float) ($this->request->getPost('action_poids') ?? 0);
        $clientId = (int) session()->get('user_id');

        if (!$clientId) {
            return redirect()->to('/login')->with('error', 'Vous devez être connecté.');
        }

        if (!$objectifId || !$this->objectifsModel->find($objectifId)) {
            return redirect()->to('/regime-sport')->with('error', 'Objectif invalide.');
        }

        // Sauvegarde le nouvel objectif
        $data = [
            'client_id' => $clientId,
            'objectif_id' => $objectifId,
            'date_choix' => date('Y-m-d H:i:s'),
            'action_poids' => $actionPoids > 0 ? $actionPoids : null,
        ];

        if ($this->clientObjectifsModel->insert($data, false)) {
            // Succès - redirection
            return redirect()->to('/regime-sport')->with('success', 'Objectif défini avec succès. La page va se rafraîchir avec votre objectif.');
        } else {
            // Erreur avec détails de validation
            $errors = $this->clientObjectifsModel->errors();
            $errorMsg = !empty($errors) ? implode(', ', $errors) : 'Erreur lors de la sauvegarde de l\'objectif.';
            log_message('error', 'Erreur ClientObjectifsModel: ' . json_encode($errors) . ' | Data: ' . json_encode($data));
            return redirect()->to('/regime-sport')->with('error', $errorMsg);
        }
    }

    /**
     * Affiche les régimes déjà achetés par l'utilisateur connecté.
     */
    public function monRegime()
    {
        $clientId = (int) session()->get('user_id');

        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        $selectedUser = $this->usersModel->find($clientId);
        $purchasedRegimes = $this->purchaseModel->getPurchasedDetailsByClient($clientId);

        $totalPackages = count($purchasedRegimes);
        $totalDays = 0;
        $estimatedTotal = 0.0;

        foreach ($purchasedRegimes as $purchase) {
            $duree = (int) ($purchase['duree'] ?? 0);
            $prixParJour = (float) ($purchase['prix_par_jour'] ?? 0);

            $totalDays += $duree;
            $estimatedTotal += $prixParJour * $duree;
        }

        $data = [
            'title' => 'Mon Régime',
            'selectedUser' => $selectedUser,
            'purchasedRegimes' => $purchasedRegimes,
            'totalPackages' => $totalPackages,
            'totalDays' => $totalDays,
            'estimatedTotal' => $estimatedTotal,
        ];

        return view('regime_sport/mon_regime', $data);
    }

    private function pdfText(string $value): string
    {
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', $value);

        return $converted !== false ? $converted : utf8_decode($value);
    }

    private function limitPdfText(string $value, int $maxLength): string
    {
        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            return mb_strlen($value) > $maxLength
                ? mb_substr($value, 0, $maxLength - 1) . '…'
                : $value;
        }

        return strlen($value) > $maxLength ? substr($value, 0, $maxLength - 1) . '…' : $value;
    }

    public function exportMonRegimePdf()
    {
        $clientId = (int) session()->get('user_id');

        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        $selectedUser = $this->usersModel->find($clientId);
        $purchasedRegimes = $this->purchaseModel->getPurchasedDetailsByClient($clientId);

        $totalPackages = count($purchasedRegimes);
        $totalDays = 0;
        $estimatedTotal = 0.0;

        foreach ($purchasedRegimes as $purchase) {
            $duree = (int) ($purchase['duree'] ?? 0);
            $prixParJour = (float) ($purchase['prix_par_jour'] ?? 0);
            $totalDays += $duree;
            $estimatedTotal += $prixParJour * $duree;
        }

        require_once ROOTPATH . 'fpdf186/fpdf.php';

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        $pdf->SetMargins(12, 12, 12);
        $pdf->SetTitle($this->pdfText('Mon régime'));

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $this->pdfText('Fitness Régime - Mon régime'), 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 7, $this->pdfText('Utilisateur : ' . ($selectedUser['email'] ?? $selectedUser['nom'] ?? '—')), 0, 1);
        $pdf->Cell(0, 7, $this->pdfText('Packages achetés : ' . $totalPackages), 0, 1);
        $pdf->Cell(0, 7, $this->pdfText('Jours cumulés : ' . $totalDays), 0, 1);
        $pdf->Cell(0, 7, $this->pdfText('Coût estimé : ' . number_format($estimatedTotal, 2, ',', ' ') . ' €'), 0, 1);
        $pdf->Ln(4);

        $headers = ['Date', 'Régime', 'Sport', 'Objectif', 'Durée', 'Prix/jour', 'Impact', 'Coût estimé'];
        $widths = [32, 34, 30, 24, 16, 22, 18, 24];

        $pdf->SetFont('Arial', 'B', 8);
        foreach ($headers as $index => $header) {
            $pdf->Cell($widths[$index], 8, $this->pdfText($header), 1, 0, 'C');
        }
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);

        if (empty($purchasedRegimes)) {
            $pdf->Cell(array_sum($widths), 10, $this->pdfText('Aucun régime acheté pour le moment.'), 1, 1, 'C');
        } else {
            foreach ($purchasedRegimes as $purchase) {
                $pourcentageViande = (float) ($purchase['pourcentage_viande'] ?? 0);
                $pourcentageVolaille = (float) ($purchase['pourcentage_volaille'] ?? 0);
                $pourcentagePoisson = (float) ($purchase['pourcentage_poisson'] ?? 0);
                $prixParJour = (float) ($purchase['prix_par_jour'] ?? 0);
                $duree = (int) ($purchase['duree'] ?? 0);
                $impact = $purchase['impact_journalier'] ?? null;
                $coutEstime = $prixParJour > 0 && $duree > 0 ? $prixParJour * $duree : null;
                $regimeLabel = trim(
                    number_format($pourcentageViande, 0) . '% viande, ' .
                    number_format($pourcentageVolaille, 0) . '% volaille, ' .
                    number_format($pourcentagePoisson, 0) . '% poisson'
                );

                $row = [
                    (string) ($purchase['date_choix'] ?? '—'),
                    $regimeLabel ?: '—',
                    (string) ($purchase['sport_libelle'] ?? '—'),
                    (string) ($purchase['objectif_libelle'] ?? '—'),
                    $duree . ' j',
                    number_format($prixParJour, 2, ',', ' ') . ' €',
                    $impact === null ? '—' : number_format((float) $impact, 3, ',', ' ') . ' kg',
                    $coutEstime === null ? '—' : number_format((float) $coutEstime, 2, ',', ' ') . ' €',
                ];

                if ($pdf->GetY() > 275) {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial', 'B', 8);
                    foreach ($headers as $index => $header) {
                        $pdf->Cell($widths[$index], 8, $this->pdfText($header), 1, 0, 'C');
                    }
                    $pdf->Ln();
                    $pdf->SetFont('Arial', '', 8);
                }

                foreach ($row as $index => $cell) {
                    $display = $this->limitPdfText($cell, max(8, (int) ($widths[$index] / 2)));
                    $pdf->Cell($widths[$index], 8, $this->pdfText($display), 1, 0, 'L');
                }
                $pdf->Ln();
            }
        }

        $filename = 'mon_regime_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output('D', $filename);
        exit;
    }
}

