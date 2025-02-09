<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;

/**
 * ClassAttends Controller
 *
 * @property \App\Model\Table\ClassAttendsTable $ClassAttends
 *
 * @method \App\Model\Entity\ClassAttend[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClassAttendsController extends AppController
{
    public $components = ['Amazon'];
    
    public function initialize() {
                
        parent::initialize();

        $this->loadComponent('Global');
    }

    public function isAuthorized($user)
    {
        parent::isAuthorized($user);
        $this->Auth->allow();
            return true;
    }

     

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->loadModel('Sclasses');

        $this->viewBuilder()->setLayout('admin');
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) { 
            $classAttends = $this->ClassAttends->find('all', array('conditions' => ['soft_delete' => 0]));
        } else {
            $classAttends = $this->ClassAttends->find('all', array('conditions' => ['userId' => $user_id, 'soft_delete' => 0]));
        }

        $this->set(compact('classAttends'));

        $this->set('page_title', 'Classes');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');
        

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
    }

    /**
     * View method
     *
     * @param string|null $id Class Attend id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($idEncode = null)
    {
        $id = $this->Global->userIdDecode($idEncode);

        $classAttend = $this->ClassAttends->get($id, [
            'contain' => []
        ]);

        $class = $this->Sclasses->get($classAttend->classId, [
            'contain' => []
        ]);
        $this->set('class', $class);

        $this->set('classAttend', $classAttend);

        $this->set('page_title', $class->name . " Class");

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');

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
        $classAttend = $this->ClassAttends->newEntity();
        if ($this->request->is('post')) {
            $classAttend = $this->ClassAttends->patchEntity($classAttend, $this->request->getData());
            $classAttend->userId = $_SESSION['Auth']['User']['id'];
            if ($this->ClassAttends->save($classAttend)) {
                $this->Flash->success(__('The class attend has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class attend could not be saved. Please, try again.'));
        }

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $classes = $this->Sclasses->find('all');

        $this->set(compact('classAttend'));

        $this->set('classes', $classes);

        $this->set('page_title', 'Add Class');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');
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
        
        $classAttend = $this->ClassAttends->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {

            $classAttend = $this->ClassAttends->patchEntity($classAttend, $this->request->getData());

            if ($this->ClassAttends->save($classAttend)) {

                $this->Flash->success(__('The class attend has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The class attend could not be saved. Please, try again.'));
        }

        $this->set('page_title', 'Edit Class Attend');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');

        $this->set(compact('classAttend'));

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );
    }

    /**
     * Delete method
     *
     * @param string|null $id Class Attend id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $classAttend = $this->ClassAttends->get($id);
        if ($this->ClassAttends->delete($classAttend)) {
            $this->Flash->success(__('The class attend has been deleted.'));
        } else {
            $this->Flash->error(__('The class attend could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
