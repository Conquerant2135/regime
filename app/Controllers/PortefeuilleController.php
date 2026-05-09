<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientObjectifsModel;
use App\Models\ClientOptionsModel;
use App\Models\MvtCompteModel;
use App\Models\CodeModel;
use App\Models\ObjectifsModel;
use App\Models\PurchaseModel;
use App\Models\UsersModel;

class PortefeuilleController extends BaseController
{
    private MvtCompteModel $mvtModel;
    private CodeModel $codeModel;
    private UsersModel $usersModel;
    private ClientObjectifsModel $clientObjectifsModel;
    private ObjectifsModel $objectifsModel;
    private ClientOptionsModel $clientOptionsModel;
    private PurchaseModel $purchaseModel;

    public function __construct()
    {
        $this->mvtModel = new MvtCompteModel();
        $this->codeModel = new CodeModel();
        $this->usersModel = new UsersModel();
        $this->clientObjectifsModel = new ClientObjectifsModel();
        $this->objectifsModel = new ObjectifsModel();
        $this->clientOptionsModel = new ClientOptionsModel();
        $this->purchaseModel = new PurchaseModel();
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

        $solde = $this->mvtModel->getSoldeClient($clientId);
        $historique = $this->mvtModel->getHistorique($clientId);

        // Formater les types de transaction
        $historique = array_map(function ($t) {
            $t['type_transaction'] = $t['type_transaction'] === 'credit' ? 'crédit' : 'débit';
            return $t;
        }, $historique);

        return view('portefeuille/index', [
            'solde' => $solde,
            'historique' => $historique,
        ]);
    }

    /**
     * Affiche le profil du compte utilisateur.
     */
    public function compte(): ResponseInterface|string
    {
        $clientId = (int) session()->get('user_id') ?? 0;
        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        $user = $this->usersModel->find($clientId);
        $latestObjectifRow = $this->clientObjectifsModel->getLatestObjectifByClient($clientId);
        $latestObjectif = null;
        if (!empty($latestObjectifRow)) {
            $objectif = $this->objectifsModel->find($latestObjectifRow['objectif_id']);
            $latestObjectif = $objectif['libelle'] ?? null;
        }

        $latestOption = $this->clientOptionsModel->getLatestOptionWithDetails($clientId);
        $purchasedRegimes = $this->purchaseModel->getPurchasedDetailsByClient($clientId);

        $memberSince = null;
        if (!empty($user['created_at'])) {
            $memberSince = date('d/m/Y', strtotime((string) $user['created_at']));
        }

        $birthDate = null;
        $age = null;
        if (!empty($user['date_naissance'])) {
            $birthDate = date('d/m/Y', strtotime((string) $user['date_naissance']));
            try {
                $age = (new \DateTimeImmutable((string) $user['date_naissance']))->diff(new \DateTimeImmutable('now'))->y;
            } catch (\Throwable $throwable) {
                $age = null;
            }
        }

        $userInitials = '';
        if (!empty($user['nom'])) {
            $parts = preg_split('/\s+/', trim((string) $user['nom'])) ?: [];
            $userInitials = strtoupper(substr((string) ($parts[0] ?? ''), 0, 1) . substr((string) ($parts[1] ?? $parts[0] ?? ''), 0, 1));
        }

        return view('compte/index', [
            'user' => $user,
            'latestObjectif' => $latestObjectif,
            'latestOption' => $latestOption,
            'purchasedCount' => count($purchasedRegimes),
            'memberSince' => $memberSince,
            'birthDate' => $birthDate,
            'age' => $age,
            'userInitials' => $userInitials,
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
