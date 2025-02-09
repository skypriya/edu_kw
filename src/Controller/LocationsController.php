<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;


/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotifyTable $notify
 *
 * @method \App\Model\Entity\Notify[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class LocationsController extends AppController
{

    public $components = ['Amazon'];
    
    public function initialize() {
                
        parent::initialize();

        $this->loadComponent('Global');
    }
    

    public function isAuthorized($user) {
        parent::isAuthorized($user);
        $this->Auth->allow();
        return true;
    }

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);

        $this->viewBuilder()->setLayout('admin');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index() {
        
        $this->loadModel('Locations');

        $conn = ConnectionManager::get("default"); // name of your database connection      

        $query = 'SELECT l.id, l.name as lname, l.address, l.openFrom, l.openTo, ua.name as uname FROM locations as l LEFT JOIN user_allow as ua ON ua.id = l.userAllow where l.soft_delete = 0 ORDER BY l.created ASC';

        $querySql = $conn->execute($query);

        $query_response = $querySql->fetchAll('assoc');
        
        $locations = array();

        foreach($query_response as $key => $value) {  
            
            $locations[] = array(
                'id' => $value['id'],
                'lname' => $value['lname'],
                'address' => $value['address'],
                'openFrom' => $value['openFrom'],
                'openTo' => $value['openTo'],
                'uname' => $value['uname'],
            );
        }
        
        $this->set(compact('locations'));
        
        $this->set('page_title', 'Locations');

        $this->set('page_icon', '<i class="fal fa-location-circle mr-1"></i>');

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $location = $this->Locations->newEntity();

        if ($this->request->is('post')) {

            $location = $this->Locations->patchEntity($location, $this->request->getData());  
            
            $name = $this->request->data('name');
            $openFrom = $this->request->data('openFrom');
            $openTo = $this->request->data('openTo');
            $address = $this->request->data('address');
            $userAllow = $this->request->data('userAllow');

            $location->name = $name;
            $location->openFrom = $openFrom;
            $location->openTo = $openTo;
            $location->address = $address;
            $location->userAllow = $userAllow;
            
            $result = $this->Locations->save($location);

            if ($result) { 

                $user_id = $this->Auth->user( 'id' );
                $role_id = $this->Auth->user( 'usertype' );

                $insertedId = $result->id;

                $after = array(       
                    'user_id' => $user_id,
                    'role_id' => $role_id, 
                    'fk_user_id' => $user_id,                    
                    'name' => $name,
                    'openFrom' => $openFrom,
                    'openTo' => $openTo,
                    'address' => $address,            
                    'userAllow' => $userAllow
                );
                
                $this->Global->auditTrailApi($insertedId, 'locations', 'insert', null, $after);

                $this->Flash->success(__('The location has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The location could not be saved. Please, try again.'));
        }
        $this->loadModel('Userallow');

        $userAllow = $this->Userallow->find('all' , array('conditions' => ['soft_delete' => 0]));    

        $this->set(compact('location','userAllow'));

        $this->loadModel('Users');

//        $this->set('page_title', 'Add Location');
//
//        $this->set('page_icon', '<i class="fal fa-location-circle mr-1"></i>');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
    }

    /**
     * Edit method
     *
     * @param string|null $id Locations id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($idEncode = null)
    {
        $id = $this->Global->userIdDecode($idEncode);
        
        $location = $this->Locations->get($id, [
            'contain' => []
        ]);

        $address_view = $location->address;

        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );

        $before = array(                     
            'name' => $location->name,
            'openFrom' => $location->openFrom,
            'openTo' => $location->openTo,
            'address' => $location->address,
            'userAllow' => $location->userAllow
        );

        if ($this->request->is(['patch', 'post', 'put'])) {

            $location = $this->Locations->patchEntity($location, $this->request->getData());

            $name = $this->request->data('name');
            $openFrom = $this->request->data('openFrom');
            $openTo = $this->request->data('openTo');
            $address = $this->request->data('address');
            $userAllow = $this->request->data('userAllow');
            
            $location->name = $name;
            $location->openFrom = $openFrom;
            $location->openTo = $openTo;
            $location->address = $address;
            $location->userAllow = $userAllow;
            
            $after = array(
                'name' => $name,
                'user_id' => $user_id,
                'role_id' => $role_id,
                'fk_user_id' => $user_id,        
                'id' => $id,                
                'openFrom' => $openFrom,
                'openTo' => $openTo,
                'address' => $address,
                'userAllow' => $userAllow
            );
            
            $lastInsertedId = $this->Global->auditTrailApi($id, 'locations', 'update', $before, $after);

            if ($this->Locations->save($location)) {

                $this->Global->auditTrailApiSuccess($lastInsertedId, 1);

                $this->Flash->success(__('The location has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The location could not be saved. Please, try again.'));
        }

        $this->loadModel('Userallow');

//        $this->set('page_title', 'Edit Location');
//
//        $this->set('page_icon', '<i class="fal fa-location-circle mr-1"></i>');

        $userAllow = $this->Userallow->find('all' , array('conditions' => ['soft_delete' => 0]));

        $this->set(compact('location','userAllow','address_view'));

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($idEncode = null) {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $this->request->allowMethod(['post', 'delete']);

        $this->loadModel('Locations');

        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'id' => $id,     
            'soft_delete' => 1
        );
        
        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'locations', 'delete', $before, $after);
        
        $updateLocations = $this->Locations->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
        
        if ($updateLocations) {
            $this->Flash->success(__('The locations has been deleted.'));
        } else {
            $this->Flash->error(__('The locations could not be deleted. Please, try again.'));
        }
       
        return $this->redirect(['action' => 'index']);
        
    }
}
