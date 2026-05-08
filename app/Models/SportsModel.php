<?php
namespace App\Models;

use CodeIgniter\Model;

class SportsModel extends Model
{
    protected $table = 'sports';
    protected $primaryKey = 'id';
    protected $allowedFields = ['libelle'];
    protected $returnType = 'object';
    protected $useTimestamps = false;

    public function search($keyword){
        if ($keyword) {
            return $this->like('libelle', $keyword)
                ->orderBy('id', 'ASC')
                ->findAll();
        }
        return $this->orderBy('id', 'ASC')->findAll();
    }

    public function getSportWithDetails($id)
    {
        return $this->where('id', $id)
            ->first();
    }
}
