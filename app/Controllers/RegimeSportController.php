<?php
namespace App\Controllers;

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

    public function __construct()
    {
        $this->regimeSportModel = new RegimeSportModel();
        $this->clientObjectifsModel = new ClientObjectifsModel();
        $this->objectifsModel = new ObjectifsModel();
        $this->usersModel = new UsersModel();
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

        if ($selectedUserId) {
            $selectedUser = $this->usersModel->find((int) $selectedUserId);

            if ($selectedUser) {
                // Récupère le dernier objectif enregistré pour l'utilisateur sélectionné
                $userObjectifRow = $this->clientObjectifsModel->getLatestObjectifByClient((int) $selectedUserId);

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
                        } else {
                            $duree = (int) ceil(abs($actionPoids) / max(abs($impact), 0.00001));
                            $row['duree_jours'] = $duree;

                            if ($rowPrixJournalier > 0) {
                                $row['cout_total'] = round($rowPrixJournalier * $duree, 2);
                            } else {
                                $row['cout_total'] = null;
                            }
                        }

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
            'selectedUser' => $selectedUser
        ];

        return view('regime_sport/liste', $data);
    }
}

