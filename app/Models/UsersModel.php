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
    protected $validationMessages   = [
        'email' =>
        [
            'required' => 'Un email est obligaroire',
            'valid_email' => 'Le format du mail est incorrect',
        ],
        'mot_de_passe' =>
        [
            'required' => 'Un mot de passe est obligatoire',
            'min_length[6]' => 'Mot de passe trop court',
        ]
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

    public function countUsersByInscriptionMonth()
    {
        return $this->builder()
            ->select('MONTH(created_at) as mois, YEAR(created_at) as annee, COUNT(*) as total')
            ->where('role !=', 'admin')
            ->groupBy('YEAR(created_at), MONTH(created_at)')
            ->orderBy('YEAR(created_at)', 'ASC')
            ->orderBy('MONTH(created_at)', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function countUsersByAccountType(): array
    {
        $db = \Config\Database::connect();

        return $db->table('users u')
            ->select([
                'COALESCE(opt.libelle, "basique") as option_type',
                'COUNT(DISTINCT u.id) as total',
            ])
            ->join('client_options co', 'co.client_id = u.id', 'left')
            ->join('options opt', 'opt.id = co.option_id', 'left')
            ->where('u.role !=', 'admin')
            ->groupBy('COALESCE(opt.libelle, "basique")')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }


    /**
     * Récupère les dépenses des utilisateurs groupées par année et par mois.
     *
     * @return array<int, array{annee:string, mois:string, total_depenses:string, nombre_transactions:string}>
     */
    public function getDepensesByMonthAndYear(): array
    {
        $db = \Config\Database::connect();

        return $db->table('mvt_compte')
            ->select([
                'YEAR(date_mouvement) as annee',
                'MONTH(date_mouvement) as mois',
                'SUM(montant) as total_depenses',
                'COUNT(*) as nombre_transactions',
            ])
            ->where('type_transaction', 'debit')
            ->groupBy('YEAR(date_mouvement), MONTH(date_mouvement)')
            ->orderBy('YEAR(date_mouvement)', 'ASC')
            ->orderBy('MONTH(date_mouvement)', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getRepartitionClientByIMC()
    {
        // pour calculer l'imc on a besoin de la taille en cm , on divise par 100
        // afin d'avoir la bonne conversion de taille
        return $this->builder()
            ->select('
                CASE
                    WHEN poids / POW(taille / 100 , 2) < 18.5 THEN "Insuffisance ponderale"
                    WHEN poids / POW(taille / 100 , 2) < 25 THEN "Normal"
                    WHEN poids / POW(taille / 100 , 2) < 30 THEN "Surpoid"
                    ELSE "Obese"
                END AS categorie_imc , COUNT(*) as total ')
            ->where('role !=', 'admin')
            ->groupBy('categorie_imc')
            ->get()
            ->getResultArray();
    }

    public function getNombreTotalClients()
    {
        $total = $this->builder()
            ->select('COUNT(*) as total')
            ->where('role !=', 'admin')
            ->get()
            ->getResultArray();

        return $total[0]['total'];
    }

    public function getIMCMedian()
    {
        $allIMC = $this->builder()
            ->select('poids, taille')
            ->where('role !=', 'admin')
            ->get()
            ->getResultArray();

        $imcs = array_map(function ($u) {
            return $u['poids'] / pow($u['taille'] / 100, 2);
        }, $allIMC);

        return $this->median($imcs);
    }

    private function median($imcs)
    {
        sort($imcs);
        $count = count($imcs);
        $milieu = floor($count / 2);

        if ($count % 2) {
            return $imcs[$milieu];
        }

        return ($imcs[$milieu - 1] + $imcs[$milieu]) / 2;
    }

    public function getObjectifsParMois($annee)
    {
        $db = \Config\Database::connect();

        return $db->table('client_objectifs co')
            ->select('
            o.libelle AS objectif,
            MONTH(co.date_choix) AS mois,
            YEAR(co.date_choix) AS annee,
            COUNT(*) AS total
        ')
            ->join('objectifs o', 'o.id = co.objectif_id')
            ->join('users u', 'u.id = co.client_id')
            ->where('u.role !=', 'admin')
            ->where('YEAR(co.date_choix)', $annee)
            ->groupBy('o.libelle, MONTH(co.date_choix), YEAR(co.date_choix)')
            ->orderBy('o.libelle', 'ASC')
            ->orderBy('mois', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getAnneePresente()
    {
        $db = \Config\Database::connect();
        return $db->table('client_objectifs co')
                ->select('YEAR(date_choix) AS annee')
                ->distinct()
                ->orderBy('annee','DESC')
                ->get()
                ->getResultArray();
    }
}
