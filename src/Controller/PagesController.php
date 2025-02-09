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

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController
{

    public function isAuthorized($user)
    {
        parent::isAuthorized($user);
        $this->Auth->allow();
            return true;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Displays a view
     *
     * @param $page_title .
     * @return \Cake\Http\Response|null
     * @throws \Cake\Http\Exception\ForbiddenException When a directory traversal attempt.
     * @throws \Cake\Http\Exception\NotFoundException When the view file could not
     *   be found or \Cake\View\Exception\MissingTemplateException in debug mode.
     */
    public function index($page_title = 'Setting')
    {
        $conn = ConnectionManager::get("default"); // name of your database connection   

        $this->loadModel('Settings');

        $settings = $this->Settings->newEntity();

        if ($this->request->is('post')) {
            foreach($this->request->getData('data') as $key => $value) {
                $conn->execute("DELETE FROM settings WHERE key_name = '".$key."'");
                $settings->key_name = $key;
                $settings->key_value = $value;            
                $settings = $this->Settings->patchEntity($settings, $this->request->getData());
                if ($this->Settings->save($settings)) {
                    $this->Flash->success(__('Application settings successfully updated.'));
                }
            }
            return $this->redirect(['action' => 'index']);
        }

        $settings = $this->Settings->find('all', array('conditions' => ['soft_delete' => 0]));
      
        $timezoneTable = array(
            "Pacific/Kwajalein" => "(GMT -12:00) Eniwetok, Kwajalein",
            "Pacific/Samoa" => "(GMT -11:00) Midway Island, Samoa",
            "Pacific/Honolulu" => "(GMT -10:00) Hawaii",
            "America/Anchorage" => "(GMT -9:00) Alaska",
            "America/Los_Angeles" => "(GMT -8:00) Pacific Time (US &amp; Canada)",
            "America/Denver" => "(GMT -7:00) Mountain Time (US &amp; Canada)",
            "America/Chicago" => "(GMT -6:00) Central Time (US &amp; Canada), Mexico City",
            "America/New_York" => "(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima",
            "Atlantic/Bermuda" => "(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz",
            "Canada/Newfoundland" => "(GMT -3:30) Newfoundland",
            "Brazil/East" => "(GMT -3:00) Brazil, Buenos Aires, Georgetown",
            "Atlantic/Azores" => "(GMT -2:00) Mid-Atlantic",
            "Atlantic/Cape_Verde" => "(GMT -1:00 hour) Azores, Cape Verde Islands",
            "Europe/London" => "(GMT) Western Europe Time, London, Lisbon, Casablanca",
            "Europe/Brussels" => "(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris",
            "Europe/Helsinki" => "(GMT +2:00) Kaliningrad, South Africa",
            "Asia/Baghdad" => "(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg",
            "Asia/Tehran" => "(GMT +3:30) Tehran",
            "Asia/Baku" => "(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi",
            "Asia/Kabul" => "(GMT +4:30) Kabul",
            "Asia/Karachi" => "(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent",
            "Asia/Calcutta" => "(GMT +5:30) Bombay, Calcutta, Madras, New Delhi",
            "Asia/Dhaka" => "(GMT +6:00) Almaty, Dhaka, Colombo",
            "Asia/Bangkok" => "(GMT +7:00) Bangkok, Hanoi, Jakarta",
            "Asia/Hong_Kong" => "(GMT +8:00) Beijing, Perth, Singapore, Hong Kong",
            "Asia/Tokyo" => "(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk",
            "Australia/Adelaide" => "(GMT +9:30) Adelaide, Darwin",
            "Pacific/Guam" => "(GMT +10:00) Eastern Australia, Guam, Vladivostok",
            "Asia/Magadan" => "(GMT +11:00) Magadan, Solomon Islands, New Caledonia",
            "Pacific/Fiji" => "(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka"
        );

        $this->set('page_title', 'Settings');

        $this->set('settings', $settings);
    
        $this->set('timezone', $timezoneTable);
    }
}
