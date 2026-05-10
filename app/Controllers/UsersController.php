<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class UsersController extends BaseController
{
    public function countUserByInscriptionApi()
    {
        $usersModel = new UsersModel();
        $result = $usersModel->countUsersByInscriptionMonth();
        return $this->response->setJSON($result);
    }

    public function countUserByAccoutType()
    {
        $usersModel = new UsersModel();
        $result = $usersModel->countUsersByAccountType();
        return $this->response->setJSON($result);
    }

    public function depensesParMoisEtAnneeApi()
    {
        $usersModel = new UsersModel();
        $result = $usersModel->getDepensesByMonthAndYear();

        return $this->response->setJSON($result);
    }

    public function getRepartitionClientByIMC()
    {
        $usersModel = new UsersModel();
        $result = $usersModel->getRepartitionClientByIMC();
        return $this->response->setJSON($result);
    }

    public function getRepatitionObjectifClient($annee) {
        $usersModel = new UsersModel();
        return  $this->response->setJSON($usersModel->getObjectifsParMois($annee));
    }

    public function getAnneePresente(){
        $usersModel = new UsersModel();
        return $this->response->setJSON($usersModel->getAnneePresente());
    }
}
