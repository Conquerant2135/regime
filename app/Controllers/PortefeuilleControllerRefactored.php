<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MvtCompteModel;
use App\Models\CodeModel;

class PortefeuilleControllerRefactored extends BaseController
{
    private MvtCompteModel $mvtModel;
    private CodeModel $codeModel;

    public function __construct()
    {
        $this->mvtModel = new MvtCompteModel();
        $this->codeModel = new CodeModel();
    }

    /**
     * Affiche le portefeuille
     */
    public function index(): ResponseInterface|string
    {
        // Vérifier l'authentification
        $clientId = (int)session()->get('user_id') ?? 0;
        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        // Récupérer les données
        $solde = $this->mvtModel->getSoldeClient($clientId);
        $historique = $this->mvtModel->getHistorique($clientId);

        // Formater les types de transaction
        $historique = array_map(function ($t) {
            $t['type_transaction'] = $t['type_transaction'] === 'credit' ? 'crédit' : 'débit';
            return $t;
        }, $historique);

        return view('portefeuille/index', [
            'solde' => $solde,
            'historique' => $historique
        ]);
    }

    /**
     * Valide et utilise un code promo (AJAX)
     */
    public function utiliserCode(): ResponseInterface
    {
        // Vérifier l'authentification
        $clientId = (int)session()->get('user_id') ?? 0;
        if (!session()->get('logged_in') || $clientId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Authentification requise'
            ])->setStatusCode(401);
        }

        // Vérifier que c'est une requête POST et qu'on reçoit du JSON
        if (strtoupper($this->request->getMethod()) !== 'POST') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requête invalide (POST required)'
            ])->setStatusCode(400);
        }

        $jsonData = $this->request->getJSON();
        if (!$jsonData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requête invalide (JSON required)'
            ])->setStatusCode(400);
        }

        $code = $jsonData->code ?? null;

        // Valider le code
        if (!$code || strlen($code) < 3) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Code invalide'
            ]);
        }

        // Utiliser le code
        $result = $this->codeModel->redeemCode($code, $clientId);

        if ($result['success']) {
            // Mettre à jour la session
            session()->set('solde', $result['nouveau_solde']);
        }

        return $this->response->setJSON($result);
    }
}
