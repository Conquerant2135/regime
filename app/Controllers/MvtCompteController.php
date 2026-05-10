<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MvtCompteModel;

class MvtCompteController extends BaseController
{
    public function getSoldeTotalPlateforme()
    {
        $mvtCompteModel = new MvtCompteModel();
        return $mvtCompteModel->getSoldeTotalPlateforme();
    }
}
