<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;


/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotifyTable $notify
 *
 * @method \App\Model\Entity\Notify[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ActivityController extends AppController
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
        
        $this->loadModel('Activity');
        
        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $activity = $this->Activity->find('all');

        $this->set(compact('activity'));
        
        $this->set('page_title', 'Activities');
    }    
}
