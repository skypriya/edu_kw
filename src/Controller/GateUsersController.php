<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;

/**
 * GateUsers Controller
 *
 * @property \App\Model\Table\GateUsersTable $GateUsers
 *
 * @method \App\Model\Entity\GateUser[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class GateUsersController extends AppController
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
        $this->loadModel('Gates');
        
        $this->viewBuilder()->setLayout('admin');
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $gateUsers = $this->GateUsers->find('all');

        $this->set(compact('gateUsers'));
        $this->set('page_title', 'Gates');
    }

    /**
     * View method
     *
     * @param string|null $id Gate User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $gateUser = $this->GateUsers->get($id, [
            'contain' => []
        ]);
        $gate = $this->Gates->get($gateUser->gateId, [
            'contain' => []
        ]);
        $this->set('gate', $gate);

        $this->set('gateUser', $gateUser);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $gateUser = $this->GateUsers->newEntity();
        if ($this->request->is('post')) {
            $gateUser = $this->GateUsers->patchEntity($gateUser, $this->request->getData());
            $gateUser->userId = $_SESSION['Auth']['User']['id'];
            if ($this->GateUsers->save($gateUser)) {
                $this->Flash->success(__('The gate user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gate user could not be saved. Please, try again.'));
        }
        $this->set(compact('gateUser'));
        $gates = $this->Gates->find('all');
        $this->set('gates', $gates);
    }

    /**
     * Edit method
     *
     * @param string|null $id Gate User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $gateUser = $this->GateUsers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $gateUser = $this->GateUsers->patchEntity($gateUser, $this->request->getData());
            if ($this->GateUsers->save($gateUser)) {
                $this->Flash->success(__('The gate user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The gate user could not be saved. Please, try again.'));
        }
        $this->set(compact('gateUser'));
        $gates = $this->Gates->find('all');
        $this->set('gates', $gates);
    }

    /**
     * Delete method
     *
     * @param string|null $id Gate User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $gateUser = $this->GateUsers->get($id);
        if ($this->GateUsers->delete($gateUser)) {
            $this->Flash->success(__('The gate user has been deleted.'));
        } else {
            $this->Flash->error(__('The gate user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
