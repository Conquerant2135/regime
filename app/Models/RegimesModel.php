<?php
namespace App\Models;

use CodeIgniter\Model;

class RegimesModel extends Model
{
    protected $table      = 'regimes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pourcentage_viande', 'pourcentage_volaille', 'pourcentage_poisson', 'prix_par_jour', 'impact_journalier'];
    protected $returnType = 'object';
    protected $useTimestamps = false;

    public function search($keyword){
        if ($keyword) {
            return $this->groupStart()
                ->like('pourcentage_viande', $keyword)
                ->orLike('pourcentage_volaille', $keyword)
                ->orLike('pourcentage_poisson', $keyword)
                ->orLike('prix_par_jour', $keyword)
                ->orLike('impact_journalier', $keyword)
                ->groupEnd()
                ->orderBy('id', 'ASC')
                ->findAll();
        }
        return $this->orderBy('id', 'ASC')->findAll();
    }

    public function getRegimeWithDetails($id)
    {
        return $this->where('id', $id)
            ->first();
    }
}