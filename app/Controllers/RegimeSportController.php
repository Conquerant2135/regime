<?php
namespace App\Controllers;

use App\Models\RegimeSportModel;

class RegimeSportController extends BaseController
{
    protected $regimeSportModel;

    public function __construct()
    {
        $this->regimeSportModel = new RegimeSportModel();
    }

    /**
     * Affiche la liste des couples régimes et sports
     */
    public function getRegimeSport()
    {

        $regimesSports = $this->regimeSportModel->getAllCombinaisons();
        
        $data = [
            'title' => 'Couples régimes - sports',
            'regimesSports' => $regimesSports
        ];
        
        return view('regime_sport/liste', $data);
    }
}

