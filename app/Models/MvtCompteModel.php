<?php

namespace App\Models;

use CodeIgniter\Model;

class MvtCompteModel extends Model
{
    protected $table            = 'mvt_compte';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['client_id', 'type_transaction', 'date_mouvement', 'montant', 'raison_id'];

    protected $useTimestamps = false;

    /**
     * Enregistre une transaction
     */
    public function recordTransaction(int $clientId, string $type, float $montant): bool
    {
        return (bool)$this->insert([
            'client_id' => $clientId,
            'type_transaction' => $type,
            'date_mouvement' => date('Y-m-d H:i:s'),
            'montant' => $montant,
            'raison_id' => null
        ]);
    }

    /**
     * Récupère l'historique d'un client
     */
    public function getHistorique(int $clientId, int $limit = 50): array
    {
        return $this->where('client_id', $clientId)
            ->orderBy('date_mouvement', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Calcule le solde d'un client
     */
    public function getSoldeClient(int $clientId): float
    {
        $result = $this->selectSum('montant', 'total_credit')
            ->where('client_id', $clientId)
            ->where('type_transaction', 'credit')
            ->first();

        $credits = (float)($result['total_credit'] ?? 0);

        $result = $this->selectSum('montant', 'total_debit')
            ->where('client_id', $clientId)
            ->where('type_transaction', 'debit')
            ->first();

        $debits = (float)($result['total_debit'] ?? 0);

        return $credits - $debits;
    }
}
