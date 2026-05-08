<?php

namespace App\Controllers;

use App\Models\UsersModel;
use CodeIgniter\Database\Exceptions\DataException;

class PortefeuilleController extends BaseController
{
    /**
     * Affiche la page du portefeuille avec l'historique
     */
    public function index()
    {
        // Vérifier la connexion
        if (!session()->get('logged_in') || !session()->get('user_id')) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        $clientId = session()->get('user_id');
        $usersModel = new UsersModel();

        // Récupérer le solde
        $solde = $usersModel->getSolde($clientId);

        // Récupérer l'historique des transactions
        $db = \Config\Database::connect();
        $historique = $db->table('mvt_compte')
            ->where('client_id', $clientId)
            ->orderBy('date_mouvement', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        // Formater les types de transaction
        foreach ($historique as &$transaction) {
            $transaction['type_transaction'] = $transaction['type_transaction'] === 'credit' ? 'crédit' : 'débit';
        }

        return view('portefeuille/index', [
            'solde' => $solde,
            'historique' => $historique
        ]);
    }

    /**
     * Valide et utilise un code promo (AJAX)
     */
    public function utiliserCode()
    {
        // Vérifier la connexion
        if (!session()->get('logged_in') || !session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentification requise'
            ]);
        }

        // Vérifier que c'est une requête POST et qu'on reçoit du JSON
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requête invalide (POST required)'
            ]);
        }

        $jsonData = $this->request->getJSON();
        if (!$jsonData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requête invalide (JSON required)'
            ]);
        }

        $code = $jsonData->code ?? null;
        $clientId = session()->get('user_id');

        // Validation du code
        if (!$code || strlen($code) < 3) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code invalide'
            ]);
        }

        $db = \Config\Database::connect();

        // Chercher le code en BD
        $codeRecord = $db->table('codes')
            ->where('valeur', strtoupper($code))
            ->get()
            ->getRow();

        if (!$codeRecord) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code non trouvé'
            ]);
        }

        if ($codeRecord->is_used) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ce code a déjà été utilisé'
            ]);
        }

        // Commencer la transaction
        $db->transStart();

        try {
            // Insérer le mouvement de compte (crédit)
            $db->table('mvt_compte')->insert([
                'client_id' => $clientId,
                'type_transaction' => 'credit',
                'date_mouvement' => date('Y-m-d H:i:s'),
                'montant' => $codeRecord->gain,
                'raison_id' => null
            ]);

            // Marquer le code comme utilisé
            $db->table('codes')
                ->where('id', $codeRecord->id)
                ->update(['is_used' => 1]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de la transaction'
                ]);
            }

            // Récupérer le nouveau solde
            $usersModel = new UsersModel();
            $nouveauSolde = $usersModel->getSolde($clientId);
            
            // Mettre à jour la session
            session()->set('solde', $nouveauSolde);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Code utilisé avec succès! Vous avez reçu ' . number_format($codeRecord->gain, 2) . '€',
                'montant' => (float)$codeRecord->gain,
                'nouveau_solde' => (float)$nouveauSolde
            ]);

        } catch (DataException $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }
}
