<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class UsersController extends BaseController
{
    public function countUserByInscriptionApi(){
        $usersModel = new UsersModel();
        $result = $usersModel->countUsersByInscriptionMonth();
        return $this->response->setJSON($result);
    }
}
