<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientOptionsModel extends Model
{
    protected $table            = 'client_options';
    protected $primaryKey       = 'client_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = ['client_id', 'option_id', 'date_option'];

    protected $useTimestamps = false;

    public function getLatestOptionByClient(int $clientId): ?array
    {
        return $this->select('client_options.client_id, client_options.option_id, client_options.date_option, options.libelle, options.remise')
            ->join('options', 'options.id = client_options.option_id')
            ->where('client_options.client_id', $clientId)
            ->orderBy('client_options.date_option', 'DESC')
            ->first();
    }

    public function getLatestOptionWithDetails(int $clientId): ?array
    {
        return $this->select('client_options.client_id, client_options.option_id, client_options.date_option, options.libelle, options.remise, options.prix_option')
            ->join('options', 'options.id = client_options.option_id')
            ->where('client_options.client_id', $clientId)
            ->orderBy('client_options.date_option', 'DESC')
            ->first();
    }

    public function hasGoldOption(int $clientId): bool
    {
        $option = $this->getLatestOptionByClient($clientId);

        return is_array($option)
            && strtolower((string) ($option['libelle'] ?? '')) === 'gold';
    }
    
    public function getGoldRemiseForClient(int $clientId): float
    {
        $option = $this->getLatestOptionByClient($clientId);

        if (!is_array($option)) {
            return 0.0;
        }

        if (strtolower((string) ($option['libelle'] ?? '')) !== 'gold') {
            return 0.0;
        }

        return (float) ($option['remise'] ?? 0);
    }

    public function activateGoldSubscription(int $clientId, int $goldOptionId): bool
    {
        return (bool) $this->insert([
            'client_id' => $clientId,
            'option_id' => $goldOptionId,
            'date_option' => date('Y-m-d'),
        ]);
    }

    public function addOptionToClient(int $clientId, int $optionId, string $dateOption): bool
    {
        return (bool) $this->insert([
            'client_id' => $clientId,
            'option_id' => $optionId,
            'date_option' => $dateOption,
        ]);
    }
}