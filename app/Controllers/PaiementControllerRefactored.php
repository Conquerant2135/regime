<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UsersModel;
use App\Models\PurchaseModel;
use App\Models\MvtCompteModel;

class PaiementControllerRefactored extends BaseController
{
    private PurchaseModel $purchaseModel;
    private UsersModel $usersModel;
    private MvtCompteModel $mvtModel;

    public function __construct()
    {
        $this->purchaseModel = new PurchaseModel();
        $this->usersModel = new UsersModel();
        $this->mvtModel = new MvtCompteModel();
    }

    /**
     * Traite l'achat d'un couple régime-sport
     */
    public function acheterRegimeSport(): ResponseInterface
    {
        // Vérifier l'authentification
        $clientId = (int)session()->get('user_id') ?? 0;
        if (!session()->get('logged_in') || $clientId <= 0) {
            return redirect()->to('/login')->with('error', 'Authentification requise');
        }

        // Valider les données
        if (!$this->validate([
            'regime_id' => 'required|integer|greater_than[0]',
            'sport_id' => 'required|integer|greater_than[0]',
            'objectif_id' => 'required|integer|greater_than[0]',
            'duree' => 'required|integer|greater_than[0]',
            'prix' => 'required|numeric|greater_than[0]'
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Données invalides: ' . implode(', ', $this->validator->getErrors()));
        }

        // Récupérer les données validées
        $regimeId = (int)$this->request->getPost('regime_id');
        $sportId = (int)$this->request->getPost('sport_id');
        $objectifId = (int)$this->request->getPost('objectif_id');
        $duree = (int)$this->request->getPost('duree');
        $prixTotal = (float)$this->request->getPost('prix');

        // Vérifier le solde
        $soldeClient = $this->mvtModel->getSoldeClient($clientId);
        
        if ($soldeClient < $prixTotal) {
            $montantManquant = $prixTotal - $soldeClient;
            return redirect()->back()->with('error',
                "Solde insuffisant. Vous avez " . number_format($soldeClient, 2) . "€ " .
                "mais il en faut " . number_format($prixTotal, 2) . "€. " .
                "Montant manquant: " . number_format($montantManquant, 2) . "€"
            );
        }

        try {
            // Créer l'achat
            $this->purchaseModel->createPurchase(
                $clientId,
                $regimeId,
                $sportId,
                $objectifId,
                $duree,
                $prixTotal
            );

            // Mettre à jour la session
            $newBalance = $this->mvtModel->getSoldeClient($clientId);
            session()->set('solde', $newBalance);

            return redirect()->to('/portefeuille')
                ->with('success',
                    'Achat effectué avec succès! Solde actuel: ' . 
                    number_format($newBalance, 2) . '€'
                );
        } catch (\Exception $e) {
            log_message('error', 'Erreur achat: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'achat: ' . $e->getMessage());
        }
    }

    /**
     * Page de test des paiements
     */
    public function testPayement(): ResponseInterface|string
    {
        if (!session()->get('user_id')) {
            session()->set([
                'user_id' => 1,
                'role' => 'user',
                'logged_in' => true
            ]);
        }

        return view('TestPayement');
    }
}
