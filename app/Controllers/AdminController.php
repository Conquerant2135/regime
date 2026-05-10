<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MvtCompteModel;
use App\Models\UsersModel;

class AdminController extends BaseController
{
    public function dashboard(){
        $mvtCompteModel = new MvtCompteModel();
        $usersModel = new UsersModel();
        $data = [
                'totalCA' => $mvtCompteModel->getSoldePlateforme(),
                'totalClient' => $usersModel->getNombreTotalClients(),
                'imcMedian' => $usersModel->getIMCMedian(),
                'revenuMoyen' => $mvtCompteModel->getRevenuMoyenParClient(),
                ];
        return view("admin/dashboard" , $data);
    }
}
