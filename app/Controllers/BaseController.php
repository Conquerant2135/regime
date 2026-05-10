<?php

namespace App\Controllers;

use App\Models\ClientOptionsModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $isGoldMember = false;
        $clientId = (int) (session()->get('user_id') ?? 0);

        if (session()->get('logged_in') && $clientId > 0) {
            $clientOptionsModel = new ClientOptionsModel();
            $isGoldMember = $clientOptionsModel->hasGoldOption($clientId);
            session()->set('is_gold_member', $isGoldMember);
        } else {
            session()->remove('is_gold_member');
        }

        service('renderer')->setVar('isGoldMember', $isGoldMember);
        // $this->session = service('session');
    }
}
