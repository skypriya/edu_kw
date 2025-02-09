<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->set('publickeycaptcha',Configure::read('google_recatpcha_settings.site_key'));

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],

            'loginRedirect' => [
                'controller' => 'Dashboard',
                'action' => 'index'
            ],

            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            //use isAuthorized in Controllers
            'authorize' => 'Controller',
             // If unauthorized, return them to page they were just on
            'unauthorizedRedirect' => $this->referer()
        ]);
        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        
        $this->Auth->allow(['login', 'register', 'forgotPassword', 'resetPassword']);
        
    }

    public function isAuthorized($user)
    {
        if (isset($user['usertype'])) {
          $this->set('session_user', $user);
        } 
         
        
    }

    public function verifyRecatpcha($aData)
        {   return true;
            if(!$aData)
            {
                return true;
            } 
            if(isset($aData['g-recaptcha-response']))
            {
                $recaptcha_secret = Configure::read('google_recatpcha_settings.secret_key');
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$recaptcha_secret."&response=".$aData['g-recaptcha-response']; 
                $response = json_decode(@file_get_contents($url));   
                    
                if($response->success == true)
                {
                    return true;
                }
                else
                {
                    return false; 
                } 
            }
            else
            {
                return false;
            }
        }  
        
    
}
