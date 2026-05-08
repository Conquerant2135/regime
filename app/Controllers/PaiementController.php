<?php 
namespace App\Controllers;

use App\Models\UsersModel;

class PaiementController extends BaseController
{
public function acheterRegimeSport()
{
    if (!session()->get('logged_in') || !session()->get('user_id')) {
        return redirect()->to('/login')->with('error', 'Authentification requise');
    }

    $clientId = session()->get('user_id');
    $regimeId = $this->request->getPost('regime_id');
    $sportId = $this->request->getPost('sport_id');
    $objectifId = $this->request->getPost('objectif_id');
    $duree = $this->request->getPost('duree');
    $prixTotal = $this->request->getPost('prix');

    if($objectifId == "gain"){
        $objectifId = 1;
    } elseif($objectifId == "imc ideal"){
        $objectifId = 2;
    }
    elseif($objectifId == "perte de poids"){
        $objectifId = 3;
    }
    elseif($objectifId == ""){
        return redirect()->back()->with('error', 'Objectif invalide avec '. $objectifId);
    }


    if (!$regimeId || !$sportId || !$objectifId || !$duree || !$prixTotal) {
        return redirect()->back()->with('error', 'Données manquantes');
    }

    // Vérifier le solde du client
    $usersModel = new UsersModel();
    $soldeClient = $usersModel->getSolde($clientId);

    if ($soldeClient < $prixTotal) {
        $montantManquant = $prixTotal - $soldeClient;
        return redirect()->back()->with('error', 
            "Solde insuffisant. Vous avez " . number_format($soldeClient, 2) . "€ mais il en faut " . number_format($prixTotal, 2) . "€. 
            Montant manquant: " . number_format($montantManquant, 2) . "€"
        );
    }

    $db = \Config\Database::connect();
    $db->transStart();

    try {
        $db->table('regime_sports')->insert([
            'regime_id' => $regimeId,
            'sport_id' => $sportId,
            'client_id' => $clientId,
            'objectif_id' => $objectifId,
            'date_choix' => date('Y-m-d'),
            'duree' => $duree
        ]);

        $db->table('mvt_compte')->insert([
            'client_id' => $clientId,
            'type_transaction' => 'debit',
            'date_mouvement' => date('Y-m-d H:i:s'),
            'montant' => $prixTotal,
            'raison_id' => null
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Erreur lors de la transaction');
        }

        $nouveauSolde = $soldeClient - $prixTotal;
        session()->set('solde', $nouveauSolde);

        return redirect()->to('/portefeuille')->with('success', 'Achat effectué avec succès! Solde actuel : ' . number_format($nouveauSolde, 2) . '€');
    } catch (\Exception $e) {
        $db->transRollback();
        return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
    }
}
public function testPayement()
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