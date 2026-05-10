<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MvtCompteModel;
use App\Models\RegimesModel;
use App\Models\UsersModel;

class AdminController extends BaseController
{
    private const REGIMES_ROUTE = 'admin/regimes';

    private function getRegimeValidationRules(): array
    {
        $percentageRule = 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]';

        return [
            'nom' => 'required|min_length[2]|max_length[255]',
            'pourcentage_viande' => $percentageRule,
            'pourcentage_volaille' => $percentageRule,
            'pourcentage_poisson' => $percentageRule,
            'prix_par_jour' => 'required|decimal|greater_than_equal_to[0]',
            'impact_journalier' => 'required|decimal',
        ];
    }

    public function dashboard(){
        $mvtCompteModel = new MvtCompteModel();
        $usersModel = new UsersModel();
        $data = [
                'totalCA' => $mvtCompteModel->getSoldePlateforme(),
                'totalClient' => $usersModel->getNombreTotalClients(),
                'imcMedian' => $usersModel->getIMCMedian(),
                'revenuMoyen' => $mvtCompteModel->getRevenuMoyenParClient()
                ];
        return view("admin/dashboard" , $data);
    }

    public function regimes()
    {
        $regimesModel = new RegimesModel();
        $keyword = trim((string) $this->request->getGet('q'));

        $data = [
            'keyword' => $keyword,
            'regimes' => $regimesModel->search($keyword),
        ];

        return view('admin/regime/crud', $data);
    }

    public function storeRegime()
    {
        $rules = $this->getRegimeValidationRules();

        if (! $this->validate($rules)) {
            return redirect()->to(site_url(self::REGIMES_ROUTE))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $viande = (float) $this->request->getPost('pourcentage_viande');
        $volaille = (float) $this->request->getPost('pourcentage_volaille');
        $poisson = (float) $this->request->getPost('pourcentage_poisson');

        if (($viande + $volaille + $poisson) > 100) {
            return redirect()->to(site_url(self::REGIMES_ROUTE))
                ->withInput()
                ->with('error', 'La somme des pourcentages ne doit pas depasser 100.');
        }

        $regimesModel = new RegimesModel();
        $regimesModel->insert([
            'nom' => trim((string) $this->request->getPost('nom')),
            'pourcentage_viande' => $viande,
            'pourcentage_volaille' => $volaille,
            'pourcentage_poisson' => $poisson,
            'prix_par_jour' => (float) $this->request->getPost('prix_par_jour'),
            'impact_journalier' => (float) $this->request->getPost('impact_journalier'),
        ]);

        return redirect()->to(site_url(self::REGIMES_ROUTE))
            ->with('success', 'Regime cree avec succes.');
    }

    public function editRegime(int $id)
    {
        $regimesModel = new RegimesModel();
        $regime = $regimesModel->find($id);

        if (! $regime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/regime/update', ['regime' => $regime]);
    }

    public function updateRegime(int $id)
    {
        $rules = $this->getRegimeValidationRules();

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/regimes/update/' . $id))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $viande = (float) $this->request->getPost('pourcentage_viande');
        $volaille = (float) $this->request->getPost('pourcentage_volaille');
        $poisson = (float) $this->request->getPost('pourcentage_poisson');

        if (($viande + $volaille + $poisson) > 100) {
            return redirect()->to(site_url('admin/regimes/update/' . $id))
                ->withInput()
                ->with('error', 'La somme des pourcentages ne doit pas depasser 100.');
        }

        $regimesModel = new RegimesModel();
        if (! $regimesModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $regimesModel->update($id, [
            'nom' => trim((string) $this->request->getPost('nom')),
            'pourcentage_viande' => $viande,
            'pourcentage_volaille' => $volaille,
            'pourcentage_poisson' => $poisson,
            'prix_par_jour' => (float) $this->request->getPost('prix_par_jour'),
            'impact_journalier' => (float) $this->request->getPost('impact_journalier'),
        ]);

        return redirect()->to(site_url(self::REGIMES_ROUTE))
            ->with('success', 'Regime mis a jour avec succes.');
    }

    public function deleteRegime(int $id)
    {
        $regimesModel = new RegimesModel();
        $regime = $regimesModel->find($id);

        if (! $regime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $regimesModel->delete($id);

        return redirect()->to(site_url(self::REGIMES_ROUTE))
            ->with('success', 'Regime supprime avec succes.');
    }
}
