<?php

namespace App\Controllers;

use App\Models\ClientObjectifsModel;
use App\Models\ObjectifsModel;

class DebugController extends BaseController
{
    /**
     * Affiche les enregistrements dans client_objectifs
     */
    public function objectives()
    {
        $clientObjectifsModel = new ClientObjectifsModel();
        $objectifsModel = new ObjectifsModel();
        
        // Récupère tous les enregistrements
        $allRecords = $clientObjectifsModel->findAll();
        $allObjectives = $objectifsModel->findAll();
        
        // Enrichit avec les libellés
        foreach ($allRecords as &$record) {
            $obj = $objectifsModel->find($record['objectif_id']);
            $record['objectif_libelle'] = $obj ? $obj['libelle'] : 'N/A';
        }
        
        return view('debug/objectives', [
            'records' => $allRecords,
            'objectives' => $allObjectives,
        ]);
    }
}
