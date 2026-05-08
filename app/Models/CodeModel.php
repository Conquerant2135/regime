<?php

namespace App\Models;

use CodeIgniter\Model;

class CodeModel extends Model
{
    protected $table            = 'codes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['valeur', 'gain', 'is_used', 'date_creation'];

    protected $useTimestamps = false;

    /**
     * Cherche un code valide (non utilisé)
     */
    public function findValidCode(string $code): ?array
    {
        return $this->where('valeur', strtoupper($code))
            ->where('is_used', 0)
            ->first();
    }

    /**
     * Marque un code comme utilisé
     */
    public function markAsUsed(int $codeId): bool
    {
        return (bool)$this->update($codeId, ['is_used' => 1]);
    }

    /**
     * Applique un code promo à un client
     */
    public function redeemCode(string $code, int $clientId): array
    {
        $codeRecord = $this->findValidCode($code);

        if (!$codeRecord) {
            return ['success' => false, 'message' => 'Code non trouvé ou déjà utilisé'];
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Créditer le client
            $mvtModel = new MvtCompteModel();
            $credited = $mvtModel->recordTransaction($clientId, 'credit', (float)$codeRecord['gain']);

            if (!$credited) {
                throw new \Exception('Impossible d\'enregistrer le crédit');
            }

            // Marquer le code comme utilisé
            $marked = $this->markAsUsed($codeRecord['id']);

            if (!$marked) {
                throw new \Exception('Impossible de marquer le code comme utilisé');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de la transaction');
            }

            // Récupérer le nouveau solde
            $newBalance = $mvtModel->getSoldeClient($clientId);

            return [
                'success' => true,
                'message' => 'Code utilisé avec succès! Vous avez reçu ' . number_format((float)$codeRecord['gain'], 2) . '€',
                'montant' => (float)$codeRecord['gain'],
                'nouveau_solde' => $newBalance
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ];
        }
    }
}
