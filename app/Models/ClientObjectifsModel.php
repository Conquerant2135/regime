<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientObjectifsModel extends Model
{
    protected $table = 'client_objectifs';
    protected $primaryKey = ['client_id', 'objectif_id', 'date_choix'];
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['client_id', 'objectif_id', 'date_choix', 'action_poids'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'client_id' => 'required|integer',
        'objectif_id' => 'required|integer',
        'date_choix' => 'required|valid_date',
        'action_poids' => 'numeric'
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Récupère les objectifs d'un client à une date donnée
     */
    public function getObjectifsByClient(int $clientId, string $date = null)
    {
        $query = $this->where('client_id', $clientId);

        if ($date) {
            $query->where('date_choix', $date);
        }

        return $query->findAll();
    }

    /**
     * Récupère le dernier objectif enregistré pour un client
     */
    public function getLatestObjectifByClient(int $clientId)
    {
        return $this->where('client_id', $clientId)
            ->orderBy('date_choix', 'DESC')
            ->first();
    }

    /**
     * Récupère tous les clients ayant un objectif spécifique
     */
    public function getClientsByObjectif(int $objectifId)
    {
        return $this->where('objectif_id', $objectifId)->findAll();
    }

    /**
     * Ajoute un objectif à un client
     */
    public function addObjectifToClient(int $clientId, int $objectifId, string $dateChoix, float $actionPoids = 0)
    {
        return $this->insert([
            'client_id' => $clientId,
            'objectif_id' => $objectifId,
            'date_choix' => $dateChoix,
            'action_poids' => $actionPoids
        ]);
    }

    /**
     * Supprime un objectif d'un client
     */
    public function removeObjectifFromClient(int $clientId, int $objectifId, string $dateChoix)
    {
        return $this->delete([
            'client_id' => $clientId,
            'objectif_id' => $objectifId,
            'date_choix' => $dateChoix
        ]);
    }
}
