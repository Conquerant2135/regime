<?php

namespace App\Models;

use CodeIgniter\Model;

class MvtCompteModel extends Model
{
    protected $table = 'mvt_compte';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['client_id', 'type_transaction', 'date_mouvement', 'montant'];

    protected $useTimestamps = false;

    /**
     * Enregistre une transaction
     */
    public function recordTransaction(int $clientId, string $type, float $montant): bool
    {
        return $this->recordTransactionFull($clientId, $type, $montant, null, 'autre');
    }

    /**
     * Enregistre une transaction avec tous les détails
     * 
     * @param int $clientId ID du client
     * @param string $type 'debit' ou 'credit'
     * @param float $montant Montant de la transaction
     * @param string|null $mouvementType 'achat_regime', 'souscription_gold', 'recharge_code', 'autre'
     * @param int|null $regimeId ID du régime acheté (optionnel)
     * @param int|null $sportId ID du sport acheté (optionnel)
     * @param string|null $description Description textuelle
     * @return bool
     */
    public function recordTransactionFull(
        int $clientId,
        string $type,
        float $montant,
        ?string $mouvementType = 'autre',
        ?int $regimeId = null,
        ?int $sportId = null,
        ?string $description = null
    ): bool {
        return (bool) $this->insert([
            'client_id' => $clientId,
            'type_transaction' => $type,
            'date_mouvement' => date('Y-m-d H:i:s'),
            'montant' => $montant,
            // 'type_transaction' => $mouvementType ?? 'autre'
            // 'regime_id' => $regimeId,
            // 'sport_id' => $sportId,
            // 'description' => $description
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

        $credits = (float) ($result['total_credit'] ?? 0);

        $result = $this->selectSum('montant', 'total_debit')
            ->where('client_id', $clientId)
            ->where('type_transaction', 'debit')
            ->first();

        $debits = (float) ($result['total_debit'] ?? 0);

        return $credits - $debits;
    }

    public function getSoldePlateforme()
    {
        $montant = $this
            ->selectSum('montant')
            ->where('type_transaction', 'debit')
            ->first();

        return $montant['montant'];
    }

    public function getRevenuMoyenParClient()
    {
        $result = $this->builder()
            ->select('SUM(montant) / COUNT(DISTINCT client_id) as revenu_moyen')
            ->where('type_transaction', 'debit')
            ->get()
            ->getRowArray();

        return $result['revenu_moyen'] ?? 0;
    }
}
