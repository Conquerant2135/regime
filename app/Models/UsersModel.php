<?php

namespace App\Models;

use CodeIgniter\Model;
use Override;

class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nom', 'email', 'mot_de_passe', 'date_naissance', 'taille', 'poids', 'role'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = ['email' => 'required|valid_email', 'mot_de_passe' => 'required|min_length[6]'];
    protected $validationMessages   = ['email' =>
                                            ['required' => 'Un email est obligaroire',
                                            'valid_email' => 'Le format du mail est incorrect',],
                                        'mot_de_passe' =>
                                            ['required' => 'Un mot de passe est obligatoire',
                                            'min_length[6]' => 'Mot de passe trop court',]
                                        ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    #[Override]
    public function getValidationRules(array $options = []): array
    {
        return $this->validationRules;
    }

    public function getByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Récupère le solde d'un client
     * Calcul : SUM(crédit) - SUM(débit)
     * 
     * @param int $userId ID du client
     * @return float Solde du client
     */
    public function getSolde($userId): float
    {
        $db = \Config\Database::connect();

        $result = $db->query(
            "SELECT SUM(CASE WHEN type_transaction = 'credit' THEN montant ELSE -montant END) as solde
             FROM mvt_compte
             WHERE client_id = ?",
            [$userId]
        )->getRow();

        return (float)($result->solde ?? 0);
    }
}
