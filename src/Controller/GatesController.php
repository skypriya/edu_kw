<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 * Gates Controller
 *
 * @property \App\Model\Table\GatesTable $Gates
 *
 * @method \App\Model\Entity\Gate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GatesController extends AppController
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
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $gates = $this->Gates->find('all');

        $this->set(compact('gates'));
        $this->set('page_title', 'All Gates');
    }

    /**
     * View method
     *
     * @param string|null $id Gate id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gate = $this->Gates->get($id, [
            'contain' => []
        ]);

        $this->set('gate', $gate);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gate = $this->Gates->newEntity();
        if ($this->request->is('post')) {
            $gate = $this->Gates->patchEntity($gate, $this->request->getData());
            $gate->qrno = rand();
            if ($this->Gates->save($gate)) {
                $this->Flash->success(__('The gate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gate could not be saved. Please, try again.'));
        }
        $this->set(compact('gate'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Gate id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gate = $this->Gates->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gate = $this->Gates->patchEntity($gate, $this->request->getData());
            if ($this->Gates->save($gate)) {
                $this->Flash->success(__('The gate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gate could not be saved. Please, try again.'));
        }
        $this->set(compact('gate'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Gate id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gate = $this->Gates->get($id);
        if ($this->Gates->delete($gate)) {
            $this->Flash->success(__('The gate has been deleted.'));
        } else {
            $this->Flash->error(__('The gate could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }



    /**
     * Add/Edit/Delete method
     *
     * @param string|null $id Sclass id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function manageStudents($id = null)
    {
        
        
        $this->loadModel('GateUsers');

        $gate = $this->Gates->get($id, [
            'contain' => []
        ]);

        /*$classStudents = $this->Users->find()->join(['table' => 'class_attends','alias' => 'c','type' => 'LEFT', 'conditions' => 'c.userId = Users.id',])->where(['c.classId' => $id])->all(); */

        $all = $this->GateUsers->find('all',array('conditions' => ['gateId' => $id] ));
        $allcount = $this->GateUsers->find()->where(['gateId' => $id])->count();
       // print_r($allcount); die;

        $this->set(compact('gate'));
        
        $this->set('all', $all);
        $this->set('allcount', $allcount);
        $this->set('page_title', $gate->name. '&nbsp; Students');
    }



    /**
     * Add/Edit/Delete method
     *
     * @param string|null $id Sclass id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function addStudents($id = null)
    {
        
        $this->loadModel('Users');
        $users = $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1] ));

        if ($this->request->is(['patch', 'post', 'put'])) {
            $this->loadModel('GateUsers');
            $allcount = $this->GateUsers->find()->where(['gateId' => $id])->count();
            $gate = $this->Gates->get($id, [
                'contain' => []
            ]);
            $students = $this->request->getData('users');

            $t = $allcount + count($students);
            
            if($gate->userAllow < $t)
                {
                    $resultJ = json_encode(array('result' => 'error','msg' => 'Number of students exceeded the Maximum number of students allowed for the '.$gate->name));
                    $this->response->type('json');
                    $this->response->body($resultJ);
                    return $this->response;
                }
            
            
            foreach ($students as $key => $value) {
                $gateUser = $this->GateUsers->newEntity();
                $gateUser->userId = $value;
                $gateUser->gateId = $id;
                $this->GateUsers->save($gateUser);
            }
                
                $resultJ = json_encode(array('result' => 'success','msg' => 'The students has been added to gate.'));
                $this->response->type('json');
                $this->response->body($resultJ);
                return $this->response;
             
        }
        $this->set('users', $users);
        $this->viewBuilder()->setLayout('ajax');
        
    }


    /**
     * Delete method
     *
     * @param string|null $id Sclass id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function deleteStudent($id = null, $gateId = null)
    {
        $this->loadModel('GateUsers');
        $this->request->allowMethod(['post', 'delete']);
        $sclass = $this->GateUsers->get($id);
        if ($this->GateUsers->delete($sclass)) {
            $this->Flash->success(__('The student has been removed.'));
        } else {
            $this->Flash->error(__('The student could not be removed. Please, try again.'));
        }

        return $this->redirect(['action' => 'manageStudents', $gateId]);
    }

}
