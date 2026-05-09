<?php

namespace App\Controllers;

use App\Models\RegimeSportModel;

class Home extends BaseController
{
    public function index(): string
    {
        $regimeSportModel = new RegimeSportModel();
        $combinaisons = $regimeSportModel->getAllCombinaisons();

        $regimesPrise = 0;
        $regimesPerte = 0;

        foreach ($combinaisons as $combinaison) {
            $impact = (float) ($combinaison['impact_journalier'] ?? 0);

            if ($impact > 0) {
                $regimesPrise++;
            } elseif ($impact < 0) {
                $regimesPerte++;
            }
        }

        return view('home/landing', [
            'title' => 'Accueil',
            'regimesPrise' => $regimesPrise,
            'regimesPerte' => $regimesPerte,
        ]);
    }

    public function calculerImc()
    {
        $poids = (float) $this->request->getPost('poids');
        $taille = (float) $this->request->getPost('taille');

        if ($poids <= 0 || $taille <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Veuillez saisir un poids et une taille valides.',
            ]);
        }

        $tailleMetres = $taille > 3 ? $taille / 100 : $taille;
        $imc = round($poids / ($tailleMetres * $tailleMetres), 2);

        $categorie = match (true) {
            $imc < 18.5 => 'Insuffisance pondérale',
            $imc < 25 => 'Corpulence normale',
            $imc < 30 => 'Surpoids',
            default => 'Obésité',
        };

        return $this->response->setJSON([
            'success' => true,
            'imc' => $imc,
            'categorie' => $categorie,
        ]);
    }
}
