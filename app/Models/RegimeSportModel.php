<?php

namespace App\Models;

use CodeIgniter\Model;

class RegimeSportModel extends Model
{
    protected $table = 'v_regime_sport_possible';
    protected $primaryKey = null; // Vue sans clé primaire unique
    protected $allowedFields = []; // Lecture seule, pas d'insertion
    protected $useTimestamps = false;
    
    // Pas de constructeur nécessaire en CI4, la DB est automatiquement disponible
    
    /**
     * Récupère toutes les combinaisons régime/sport
     */
    public function getAllCombinaisons()
    {
        return $this->findAll();
    }
    
    /**
     * Récupère les combinaisons par régime
     */
    public function getByRegime($regimeId)
    {
        return $this->where('regime_id', $regimeId)->findAll();
    }
    
    /**
     * Récupère les combinaisons par sport
     */
    public function getBySport($sportId)
    {
        return $this->where('sport_id', $sportId)->findAll();
    }
    
    /**
     * Filtre par effet (prise/perte/maintien)
     */
    public function getByEffet($effet)
    {
        switch ($effet) {
            case 'prise':
                return $this->where('impact_journalier >', 0)->findAll();
            case 'perte':
                return $this->where('impact_journalier <', 0)->findAll();
            case 'maintien':
                return $this->where('impact_journalier', 0)->findAll();
            default:
                return $this->findAll();
        }
    }
    
    /**
     * Pour suggérer des régimes selon l'objectif du client
     */
    public function suggestByObjectif($objectifLibelle)
    {
        switch ($objectifLibelle) {
            case 'perte de poids':
                return $this->where('impact_journalier <', 0)->findAll();
            case 'gain':
                return $this->where('impact_journalier >', 0)->findAll();
            case 'Atteindre son IMC idéal':
                return $this->orderBy('ABS(impact_journalier)', 'ASC')->findAll();
            default:
                return $this->findAll();
        }
    }
    
    /**
     * Vérifie si une combinaison existe
     */
    public function combinaisonExists($regimeId, $sportId)
    {
        return $this->where('regime_id', $regimeId)
                    ->where('sport_id', $sportId)
                    ->countAllResults() > 0;
    }
}