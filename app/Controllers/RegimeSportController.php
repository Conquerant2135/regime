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
     * Affiche la liste des couples régimes et sports avec un filtre par utilisateur
     */
    public function getRegimeSport()
    {
        $users = $this->usersModel->findAll();
        $selectedUserId = $this->request->getGet('user_id');
        $regimesSports = $this->regimeSportModel->getAllCombinaisons();
        $userObjectif = null;
        $selectedUser = null;

        if ($selectedUserId) {
            $selectedUser = $this->usersModel->find($selectedUserId);

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
            }
        }

        $data = [
            'title' => 'Couples régimes - sports',
            'regimesSports' => $regimesSports,
            'userObjectif' => $userObjectif,
            'users' => $users,
            'selectedUserId' => $selectedUserId,
            'selectedUser' => $selectedUser
        ];

        return view('regime_sport/liste', $data);
    }
}

