<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class AuthController extends BaseController
{
    private function loginValidationRules()
    {
        return ['email' => 'required|valid_email', 'mot_de_passe' => 'required|min_length[6]'];
    }

    private function wizardFistPageValidationRules()
    {
        return ['nom' => 'required|min_length[2]' , 'email' => 'required|valid_email', 'mot_de_passe' => 'required|min_length[6]' , 'naissance' => 'required'];
    }

    private function wizardSecondPageValidationRules()
    {
        return ['taille' => 'required|greater_than[0]|numeric' , 'poid' => 'required|greater_than[0]|numeric'];
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

        if (!$this->validate($this->loginValidationRules())) {
            return view('auth/login', [
                'validation' => $this->validator
            ]);
        }

        $user = $usersModel->getByEmail($email);

        if (!$user) {
            return view('auth/login' , ['notFound' => 'Utilisateur/adresse mail introuvable'] );
        }

        if ($user['mot_de_passe'] === $password) {
            // Récupérer le solde du client
            $solde = $usersModel->getSolde($user['id']);
            
            session()->set([
                'user_id' => $user['id'],
                'userEmail' => $user['email'],
                'userNom' => $user['nom'],
                'role' => $user['role'],
                'logged_in' => true,
                'solde' => $solde
            ]);
            if ($user['role'] === 'admin') {
                return redirect()->to('/admin/dashboard');
            }
            return view('test_login');
        } else {
            return view('auth/login', [ 'wrong' => 'Mot de passe incorrect']);
            return redirect()->to('/regime-sport');
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

        if ( ! $this->validate($this->wizardFistPageValidationRules())) {
            return view('auth/info_perso', ['validation' => $this->validator]);
        }

        $nom = $this->request->getPost('nom');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('mot_de_passe');
        $naissance = $this->request->getPost('naissance');

        $data = ['nom' => $nom, 'email' => $email, 'mot_de_passe' => $password, 'date_naissance' => $naissance];
        session()->set('wizard_step_1', $data);
        return view('auth/info_perso');
    }

    public function inscription()
    {
        if ( ! $this->validate($this->wizardSecondPageValidationRules())) {
            return view('auth/info_perso', ['validation' => $this->validator]);
        }

        $userData = session()->get('wizard_step_1');
        session()->remove('wizard_step_1');
        $userData['taille'] = $this->request->getPost('taille');
        $userData['poids'] = $this->request->getPost('poid');
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
