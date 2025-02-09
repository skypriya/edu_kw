<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Dompdf\Dompdf;
use Dompdf\Options;
use Helper;
use HtmlHelper;
use Cake\Datasource\ConnectionManager;
use DateTime;


/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotifyTable $notify
 *
 * @method \App\Model\Entity\Notify[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AuditController extends AppController
{


    public function isAuthorized($user)
    {
        parent::isAuthorized($user);
        
        $this->loadComponent('Global');
        
        $this->Auth->allow();
            return true;
            
        
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        
        $this->loadModel('Audit');
        
        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        
        $conn = ConnectionManager::get("default"); // name of your database connection            
        
        $user_id = $this->Auth->user( 'id' );
        
        $query_data = $conn->execute('SELECT * FROM audit_trail WHERE by_user_id = "'.$user_id.'"  ORDER BY ID DESC');

        $audit = $query_data->fetchAll('assoc');
       
        $auditArray = array();
        
        $this->loadModel('Users');
        
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
       
        foreach($audit as $audits){
            
            $table_name = isset($audits['table_name']) ? $audits['table_name'] : '';
            $action = isset($audits['action']) ? $audits['action'] : '';
            $id = isset($audits['id']) ? $audits['id'] : '';
            
            $array_activity = ARRAY_ACTIVITY;
            $transaction = $array_activity[$table_name][$action];
                        
            $username = '';
            $user_id = isset($audits['user_id']) ? $audits['user_id'] : 0;
            if($user_id != 0) {                
                $userTable = TableRegistry::get('Users');
                $exists = $userTable->exists(['id' => $user_id]);
                if (empty($exists)) {
                    continue;
                }
                if($exists) {
                    $stmt = $conn->execute('SELECT * FROM users where id="'.$user_id.'"');
                    $results = $stmt->fetch('assoc');
                    $username = $results['name'];    
                }
                
            } else {
                if($action == 'insert' && $table_name == 'users') {
                    $after = isset($audits['after']) ? $audits['after'] : "";                    
                } else {
                    $after = isset($audits['after']) ? json_decode($audits['after']) : "";
                }
                
                if(isset($after->ackessID) && $after->ackessID != "") {
                    $username = $after->ackessID; 
                } else if(isset($after->name) && $after->name != "") {
                    $username = $after->name; 
                }
            }
            
            $byusername = '';
            $by_user_id = isset($audits['by_user_id']) ? $audits['by_user_id'] : '';
            if(isset($by_user_id) && $by_user_id != '') {
                $byUserDetails = $this->Users->get($by_user_id, array('conditions' => ['id' => $by_user_id]));
                $byusername = $byUserDetails->name;
            }
            
            $by_user_id = isset($audits['by_user_id']) ? $audits['by_user_id'] : '';
            $created_on = isset($audits['created_on']) ? $audits['created_on'] : '';
            
            if($id) {
                $auditArray[] = array(
                    'id' => $id,
                    'table_name' => $transaction,
                    'action' => $action,
                    'user_id' => $username,
                    'by_user_id' => $byusername,
                    'created_on' => $created_on
                );
            }
            
        }
       
        $this->set(compact('auditArray'));
        
        $this->set('page_title', 'Logs');

        $this->set('page_icon', '<i class="fal fa-chart-line mr-1"></i>');
    }    
}
