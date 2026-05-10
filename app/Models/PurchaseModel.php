<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseModel extends Model
{
    protected $table = 'regime_sports';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['regime_id', 'sport_id', 'client_id', 'objectif_id', 'date_choix', 'duree'];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

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

    /**
     * Récupère les achats d'un client avec les libellés régime, sport et objectif.
     */
    public function getPurchasedDetailsByClient(int $clientId): array
    {
        return $this->select([
            'regime_sports.regime_id',
            'regime_sports.sport_id',
            'regime_sports.client_id',
            'regime_sports.objectif_id',
            'regime_sports.date_choix',
            'regime_sports.duree',
            'regimes.pourcentage_viande',
            'regimes.pourcentage_volaille',
            'regimes.pourcentage_poisson',
            'regimes.prix_par_jour',
            'regimes.impact_journalier',
            'sports.libelle AS sport_libelle',
            'objectifs.libelle AS objectif_libelle',
        ])
            ->join('regimes', 'regimes.id = regime_sports.regime_id', 'left')
            ->join('sports', 'sports.id = regime_sports.sport_id', 'left')
            ->join('objectifs', 'objectifs.id = regime_sports.objectif_id', 'left')
            ->where('regime_sports.client_id', $clientId)
            ->orderBy('regime_sports.date_choix', 'DESC')
            ->findAll();
    }

    /**
     * Vérifie si le client a déjà acheté exactement ce couple aujourd'hui.
     */
    public function findExistingPurchase(int $clientId, int $regimeId, int $sportId, int $objectifId, string $dateChoix): ?array
    {
        $purchase = $this->where([
            'client_id' => $clientId,
            'regime_id' => $regimeId,
            'sport_id' => $sportId,
            'objectif_id' => $objectifId,
            'date_choix' => $dateChoix,
        ])->first();

        return $purchase ?: null;
    }

    /**
     * Crée un achat en réutilisant une connexion existante déjà dans une transaction.
     * 
     * ⚠️ IMPORTANT: Ce method N'INSÈRE que l'achat (regime_sports)
     * Le mouvement compte doit être géré séparément avec traçabilité
     */
    public function createPurchaseOnConnection($db, int $clientId, int $regimeId, int $sportId, int $objectifId, int $duree, float $price): bool
    {
        $purchaseInserted = $db->table($this->table)->insert([
            'regime_id' => $regimeId,
            'sport_id' => $sportId,
            'client_id' => $clientId,
            'objectif_id' => $objectifId,
            'date_choix' => date('Y-m-d'),
            'duree' => $duree,
        ]);

        // ✅ Le mouvement compte est géré par le contrôleur avec traçabilité complète
        return (bool) $purchaseInserted;
    }
}
