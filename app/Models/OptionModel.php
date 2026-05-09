<?php

namespace App\Models;

use CodeIgniter\Model;

class OptionModel extends Model
{
    protected $table            = 'options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['libelle', 'remise', 'prix_option'];

    protected $useTimestamps = false;

    public function getByLibelle(string $libelle): ?array
    {
        return $this->where('LOWER(libelle)', strtolower(trim($libelle)))->first();
    }

    public function getGoldOption(): ?array
    {
        return $this->getByLibelle('gold');
    }

    public function getGoldRemise(): float
    {
        $goldOption = $this->getGoldOption();

        return (float) ($goldOption['remise'] ?? 0);
    }

    public function getGoldPrixOption(): float
    {
        $goldOption = $this->getGoldOption();

        return (float) ($goldOption['prix_option'] ?? 0);
    }

    public function getDiscountedPrice(float $basePrice, float $discountPercent): float
    {
        if ($discountPercent <= 0) {
            return round($basePrice, 2);
        }

        return round($basePrice * (1 - ($discountPercent / 100)), 2);
    }
}