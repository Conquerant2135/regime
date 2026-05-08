<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class AuthController extends BaseController
{
    private function modelValidationRules()
    {
        return ['email' => 'required|valid_email', 'mot_de_passe' => 'required|min_length[6]'];
    }

    public function loginForm()
    {
        return view('auth/login');
    }

    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('mot_de_passe');
        $usersModel = new UsersModel();

        if (!$this->validate($this->modelValidationRules())) {
            return view('auth/login', [
                'validation' => $this->validator
            ]);
        }

        $user = $usersModel->getByEmail($email);

        if (!$user) {
            return view('auth/login' , ['notFound' => 'Utilisateur/adresse mail introuvable'] );
        }

        if ($user['mot_de_passe'] === $password) {
            session()->set([
                'user_id' => $user['id'],
                'role' => $user['role'],
                'logged_in' => true
            ]);
            if ($user['role'] === 'admin') {
                return view('admin/dashboard');
            }
            return view('test_login');
        } else {
            return view('auth/login', [ 'wrong' => 'Mot de passe incorrect']);
        }
    }

    public function inscriptionFormContact()
    {
        // affiche juste le premier formlaire
        return view('auth/contact');
    }

    public function inscriptionFormInfoPerso()
    {
        // recolter les information et les mettres dans une session
        // on va faier un wizard pour la simplicite d'usage
        // en cas d'erreur on va juste retourner dans la page
        // question : faire une validation a chaque page ou dire que quelque chose est faux a la fin
        // je pense faire une validation a chaque changement de page est mieux
        // c'est ici qu'on va faire la validation depuis le premieer formulaire

        $nom = $this->request->getPost('nom');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $naissance = $this->request->getPost('naissance');

        $data = ['nom' => $nom, 'email' => $email, 'mot_de_passe' => $password, 'date_naissance' => $naissance];
        session()->set('inscription', $data);
        return view('auth/info_perso');
    }

    public function inscription()
    {
        $userData = session()->get('inscription');
        session()->remove('inscription');
        $userData['taille'] = $this->request->getPost('taille');
        $userData['poids'] = $this->request->getPost('poid');
        $userData[''] = $this->request->getPost('');
        // on fait la validation du 2eme formulaire , si tout est ok on va
        // inscrire la personne
        $usersModel = new UsersModel();
        $usersModel->save($userData);
        return view('auth/login');
    }

    public function testFilters()
    {
        return view('test_filter');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
