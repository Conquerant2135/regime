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
        // faire les validations necessaires et retourner les erreurs avec ci si necessaire
        return view("/home");
    }

    public static function inscriptionFormContact()
    {
        // affiche juste le premier formlaire
        return view("auth/contact");
    }

    public static function inscriptionFormInfoPerso()
    {
        // recolter les information et les mettres dans une session
        // on va faier un wizard pour la simplicite d'usage
        // en cas d'erreur on va juste retourner dans la page
        // question : faire une validation a chaque page ou dire que quelque chose est faux a la fin
        // je pense faire une validation a chaque changement de page est mieux
        // c'est ici qu'on va faire la validation depuis le premieer formulaire
        return view("auth/info_perso");
    }

    public static function inscription()
    {
        // on fait la validation du 2eme formulaire , si tout est ok on va
        // inscrire la personne
        return null;
    }
}
