<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;

class AuthController extends BaseController
{
    private function getDefaultPostLoginRedirect(string $role): string
    {
        return $role === 'admin' ? '/admin/dashboard' : '/portefeuille';
    }

    private function getIntendedRedirect(): ?string
    {
        $intendedUrl = session()->get('intended_url');

        if (!is_string($intendedUrl) || $intendedUrl === '') {
            return null;
        }

        session()->remove('intended_url');

        if ($intendedUrl === '/login' || str_starts_with($intendedUrl, 'http')) {
            return null;
        }

        return $intendedUrl;
    }

    private function loginValidationRules()
    {
        return ['email' => 'required|valid_email', 'mot_de_passe' => 'required|min_length[6]'];
    }

    private function wizardFistPageValidationRules()
    {
        return [
            'nom' => 'required|min_length[2]',
            'email' => 'required|valid_email',
            'mot_de_passe' => 'required|min_length[6]',
            'naissance' => 'required',
            'sexe' => 'required|in_list[homme,femme]'
        ];
    }

    private function wizardSecondPageValidationRules()
    {
        return ['taille' => 'required|greater_than[0]|numeric', 'poid' => 'required|greater_than[0]|numeric'];
    }

    public function loginForm()
    {
        if (session()->get('logged_in')) {
            $target = $this->getIntendedRedirect() ?? $this->getDefaultPostLoginRedirect((string) session()->get('role'));
            return redirect()->to($target);
        }

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
            return view('auth/login', ['notFound' => 'Utilisateur/adresse mail introuvable']);
        }

        if ($user['mot_de_passe'] === $password) {
            // Récupérer le solde du client
            $solde = $usersModel->getSolde($user['id']);

            $intendedRedirect = $this->getIntendedRedirect();
            $defaultRedirect = $this->getDefaultPostLoginRedirect((string) $user['role']);

            // evite les attaques de session fixation
            session()->regenerate();

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

            return redirect()->to($intendedRedirect ?? $defaultRedirect)
                ->with('success', 'Connexion réussie.');
        } else {
            return view('auth/login', ['wrong' => 'Mot de passe incorrect']);
        }
    }

    public function inscriptionFormContact()
    {
        // Clear any stale wizard session if starting fresh
        session()->remove('wizard_step_1');

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

        if (!$this->validate($this->wizardFistPageValidationRules())) {
            return redirect()->to('/inscription/contact')
                ->withInput()
                ->with('validation', $this->validator);
        }

        $nom = $this->request->getPost('nom');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('mot_de_passe');
        $naissance = $this->request->getPost('naissance');
        $sexe = $this->request->getPost('sexe');

        $data = [
            'nom' => $nom,
            'email' => $email,
            'mot_de_passe' => $password,
            'date_naissance' => $naissance,
            'sexe' => $sexe,
            'wizard_step_1_timestamp' => time()
        ];
        session()->set('wizard_step_1', $data);
        return view('auth/info_perso');
    }

    public function showInscriptionForm()
    {
        // Vérifier que l'utilisateur vient du step 1
        $userData = session()->get('wizard_step_1');
        if (!is_array($userData) || empty($userData)) {
            return redirect()->to('/inscription/contact')
                ->with('error', 'Veuillez d\'abord remplir le formulaire de contact.');
        }

        return view('auth/info_perso');
    }

    public function inscriptionBackToContact()
    {
        // Récupérer les données de la session
        $userData = session()->get('wizard_step_1');
        if (!is_array($userData) || empty($userData)) {
            return redirect()->to('/inscription/contact');
        }

        // Afficher contact.php avec les données pré-remplies
        return view('auth/contact', ['userData' => $userData]);
    }

    public function inscription()
    {
        if (!$this->validate($this->wizardSecondPageValidationRules())) {
            return view('auth/info_perso', ['validation' => $this->validator]);
        }

        // Vérifier que le step 1 existe et n'est pas expiré (30 min max)
        $userData = session()->get('wizard_step_1');
        if (!is_array($userData) || empty($userData)) {
            return redirect()->to('/inscription/contact')
                ->with('error', 'Session d\'inscription expirée. Veuillez recommencer.');
        }

        $stepTimestamp = $userData['wizard_step_1_timestamp'] ?? null;
        if (!$stepTimestamp || (time() - $stepTimestamp) > 1800) {
            session()->remove('wizard_step_1');
            return redirect()->to('/inscription/contact')
                ->with('error', 'Votre session d\'inscription a expiré. Veuillez recommencer.');
        }

        $userData['taille'] = $this->request->getPost('taille');
        $userData['poids'] = $this->request->getPost('poid');
        // Remove timestamp before saving to DB (it's only for session validation)
        unset($userData['wizard_step_1_timestamp']);

        // on fait la validation du 2eme formulaire , si tout est ok on va
        // inscrire la personne
        $usersModel = new UsersModel();
        try {
            $usersModel->save($userData);
            // Supprimer la session seulement APRÈS une inscription réussie
            session()->remove('wizard_step_1');
            return redirect()->to('/')->with('success', 'Inscription réussie! Vous pouvez vous connecter.');
        } catch (\Exception $e) {
            log_message('error', 'Erreur inscription: ' . $e->getMessage());
            return view('auth/info_perso', ['validation' => $this->validator, 'error' => $e->getMessage()]);
        }
    }

    public function testFilters()
    {
        return view('test_filter');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
