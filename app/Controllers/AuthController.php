<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public static function loginForm()
    {
        return view("auth/login");
    }

    public static function login()
    {
        return view("");
    }

    public static function inscriptionForm(){
        return view("auth/contact");
    }
}
