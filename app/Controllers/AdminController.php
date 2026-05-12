<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MvtCompteModel;
use App\Models\OptionModel;
use App\Models\RegimesModel;
use App\Models\SportsModel;
use App\Models\UsersModel;
use App\Models\CodeModel;

class AdminController extends BaseController
{
    private const REGIMES_ROUTE = 'admin/regimes';
    private const SPORTS_ROUTE = 'admin/sports';
    private const OPTIONS_ROUTE = 'admin/options';
    private const CODES_ROUTE = 'admin/codes';

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

    private function getSportValidationRules(): array
    {
        return [
            'libelle' => 'required|min_length[2]|max_length[120]',
        ];
    }

    private function getOptionValidationRules(): array
    {
        return [
            'libelle' => 'required|min_length[2]|max_length[120]',
            'remise' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
            'prix_option' => 'required|decimal|greater_than_equal_to[0]',
        ];
    }

    public function dashboard()
    {
        $mvtCompteModel = new MvtCompteModel();
        $usersModel = new UsersModel();
        $data = [
            'totalCA' => $mvtCompteModel->getSoldePlateforme(),
            'totalClient' => $usersModel->getNombreTotalClients(),
            'imcMedian' => $usersModel->getIMCMedian(),
            'revenuMoyen' => $mvtCompteModel->getRevenuMoyenParClient()
        ];
        return view("admin/dashboard", $data);
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

        if (!$this->validate($rules)) {
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

        if (!$regime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/regime/update', ['regime' => $regime]);
    }

    public function updateRegime(int $id)
    {
        $rules = $this->getRegimeValidationRules();

        if (!$this->validate($rules)) {
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
        if (!$regimesModel->find($id)) {
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

        if (!$regime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $regimesModel->delete($id);

        return redirect()->to(site_url(self::REGIMES_ROUTE))
            ->with('success', 'Regime supprime avec succes.');
    }

    public function sports()
    {
        $sportsModel = new SportsModel();
        $keyword = trim((string) $this->request->getGet('q'));

        $data = [
            'keyword' => $keyword,
            'sports' => $sportsModel->search($keyword),
        ];

        return view('admin/sport/crud', $data);
    }

    public function options()
    {
        $optionsModel = new OptionModel();
        $keyword = trim((string) $this->request->getGet('q'));

        if ($keyword !== '') {
            $optionsModel->like('libelle', $keyword);
        }

        return view('admin/options/crud', [
            'keyword' => $keyword,
            'options' => $optionsModel->orderBy('libelle', 'ASC')->findAll(),
        ]);
    }

    public function storeOption()
    {
        if (!$this->validate($this->getOptionValidationRules())) {
            return redirect()->to(site_url(self::OPTIONS_ROUTE))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $optionsModel = new OptionModel();
        $optionsModel->insert([
            'libelle' => trim((string) $this->request->getPost('libelle')),
            'remise' => (float) $this->request->getPost('remise'),
            'prix_option' => (float) $this->request->getPost('prix_option'),
        ]);

        return redirect()->to(site_url(self::OPTIONS_ROUTE))
            ->with('success', 'Option creee avec succes.');
    }

    public function updateOption(int $id)
    {
        if (!$this->validate($this->getOptionValidationRules())) {
            return redirect()->to(site_url(self::OPTIONS_ROUTE))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $optionsModel = new OptionModel();
        if (!$optionsModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $optionsModel->update($id, [
            'libelle' => trim((string) $this->request->getPost('libelle')),
            'remise' => (float) $this->request->getPost('remise'),
            'prix_option' => (float) $this->request->getPost('prix_option'),
        ]);

        return redirect()->to(site_url(self::OPTIONS_ROUTE))
            ->with('success', 'Option mise a jour avec succes.');
    }

    public function deleteOption(int $id)
    {
        $optionsModel = new OptionModel();
        $option = $optionsModel->find($id);

        if (!$option) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $optionsModel->delete($id);

        return redirect()->to(site_url(self::OPTIONS_ROUTE))
            ->with('success', 'Option supprimee avec succes.');
    }

    public function storeSport()
    {
        $rules = $this->getSportValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->to(site_url(self::SPORTS_ROUTE))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $sportsModel = new SportsModel();
        $sportsModel->insert([
            'libelle' => trim((string) $this->request->getPost('libelle')),
        ]);

        return redirect()->to(site_url(self::SPORTS_ROUTE))
            ->with('success', 'Sport cree avec succes.');
    }

    public function editSport(int $id)
    {
        $sportsModel = new SportsModel();
        $sport = $sportsModel->find($id);

        if (!$sport) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/sport/update', ['sport' => $sport]);
    }

    public function updateSport(int $id)
    {
        $rules = $this->getSportValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->to(site_url('admin/sports/update/' . $id))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $sportsModel = new SportsModel();
        if (!$sportsModel->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $sportsModel->update($id, [
            'libelle' => trim((string) $this->request->getPost('libelle')),
        ]);

        return redirect()->to(site_url(self::SPORTS_ROUTE))
            ->with('success', 'Sport mis a jour avec succes.');
    }

    public function deleteSport(int $id)
    {
        $sportsModel = new SportsModel();
        $sport = $sportsModel->find($id);

        if (!$sport) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $sportsModel->delete($id);

        return redirect()->to(site_url(self::SPORTS_ROUTE))
            ->with('success', 'Sport supprime avec succes.');
    }

    // ======================================================
    // CRUD - CODES PROMO
    // ======================================================

    public function codes()
    {
        $codeModel = new CodeModel();
        $codes = $codeModel->findAll();

        return view('admin/codes/list', ['codes' => $codes]);
    }

    public function storeCode()
    {
        $rules = [
            'valeur' => 'required|min_length[3]|max_length[50]|is_unique[codes.valeur]',
            'gain' => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(site_url(self::CODES_ROUTE))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $codeModel = new CodeModel();
        $codeModel->insert([
            'valeur' => strtoupper(trim((string) $this->request->getPost('valeur'))),
            'gain' => (float) $this->request->getPost('gain'),
            'is_used' => 0,
        ]);

        return redirect()->to(site_url(self::CODES_ROUTE))
            ->with('success', 'Code promo créé avec succès.');
    }

    public function editCode(int $id)
    {
        $codeModel = new CodeModel();
        $code = $codeModel->find($id);

        if (!$code) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('admin/codes/update', ['code' => $code]);
    }

    public function updateCode(int $id)
    {
        $codeModel = new CodeModel();
        $code = $codeModel->find($id);

        if (!$code) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'valeur' => 'required|min_length[3]|max_length[50]',
            'gain' => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to(site_url('admin/codes/update/' . $id))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $codeModel->update($id, [
            'valeur' => strtoupper(trim((string) $this->request->getPost('valeur'))),
            'gain' => (float) $this->request->getPost('gain'),
        ]);

        return redirect()->to(site_url(self::CODES_ROUTE))
            ->with('success', 'Code promo mis à jour avec succès.');
    }

    public function deleteCode(int $id)
    {
        $codeModel = new CodeModel();
        $code = $codeModel->find($id);

        if (!$code) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $codeModel->delete($id);

        return redirect()->to(site_url(self::CODES_ROUTE))
            ->with('success', 'Code promo supprimé avec succès.');
    }
}
