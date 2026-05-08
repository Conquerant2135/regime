<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseModel extends Model
{
    protected $table            = 'regime_sports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['regime_id', 'sport_id', 'client_id', 'objectif_id', 'date_choix', 'duree'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Crée un achat avec transaction de débit
     */
    public function createPurchase(int $clientId, int $regimeId, int $sportId, int $objectifId, int $duree, float $price): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insérer l'achat
            $purchaseInserted = $this->insert([
                'regime_id' => $regimeId,
                'sport_id' => $sportId,
                'client_id' => $clientId,
                'objectif_id' => $objectifId,
                'date_choix' => date('Y-m-d'),
                'duree' => $duree
            ]);

            if (!$purchaseInserted) {
                throw new \Exception('Impossible d\'enregistrer l\'achat');
            }

            // Enregistrer la transaction
            $mvtModel = new MvtCompteModel();
            $transactionInserted = $mvtModel->recordTransaction($clientId, 'debit', $price);

            if (!$transactionInserted) {
                throw new \Exception('Impossible d\'enregistrer la transaction');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de la transaction');
            }

            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Récupère les achats d'un client
     */
    public function getPurchasesByClient(int $clientId): array
    {
        return $this->where('client_id', $clientId)
            ->orderBy('date_choix', 'DESC')
            ->findAll();
    }
}
