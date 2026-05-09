<?php
namespace App\Controllers;

use App\Models\ClientOptionsModel;
use App\Models\OptionModel;
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

    public function __construct()
    {
        $this->regimeSportModel = new RegimeSportModel();
        $this->clientObjectifsModel = new ClientObjectifsModel();
        $this->objectifsModel = new ObjectifsModel();
        $this->usersModel = new UsersModel();
        $this->clientOptionsModel = new ClientOptionsModel();
        $this->optionModel = new OptionModel();
    }

    /**
     * Affiche la liste des couples régimes et sports pour l'utilisateur connecté
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

        if ($selectedUserId) {
            $selectedUserId = (int) $selectedUserId;
            $selectedUser = $this->usersModel->find($selectedUserId);
            $clientOption = $this->clientOptionsModel->getLatestOptionByClient($selectedUserId);
            $goldRemiseClient = $this->clientOptionsModel->getGoldRemiseForClient($selectedUserId);

            if ($selectedUser) {
                // Récupère le dernier objectif enregistré pour l'utilisateur sélectionné
                $userObjectifRow = $this->clientObjectifsModel->getLatestObjectifByClient($selectedUserId);
                $hasGold = $goldRemiseClient > 0;

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

                        $enhanced[] = $row;
                    }

                    $regimesSports = $enhanced;
                }
            }
        }
        else {
            $regimesSports = [];
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
            'goldOptionPrice' => $goldOptionPrice
        ];

        return view('regime_sport/liste', $data);
    }
}

