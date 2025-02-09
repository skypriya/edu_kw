<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * AttendanceReport Controller
 *
 * @property \App\Model\Table\ClassAttendsTable $ClassAttends
 *
 * @method \App\Model\Entity\ClassAttend[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttendanceReportController extends AppController
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
        $this->set('page_title', 'Attendance Report');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');

        $conn = ConnectionManager::get("default"); // name of your database connection
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        $session_user_role = explode(",", $role_id);

        $current_day = strtolower(date('l'));
        if(in_array('Admin',$session_user_role))
        {
            $query_data = $conn->execute("SELECT s.id, s.name AS class_name, l.name AS location_name, u.name AS teacher_name, s.days, s.openFrom, s.openTo FROM `sclasses` as s LEFT JOIN locations AS l ON l.id = s.location LEFT JOIN users AS u ON u.id = s.fk_user_id WHERE s.soft_delete = 0 AND s.label_type = 'classes' ORDER BY s.openFrom ASC");

//            $query_data = $conn->execute("SELECT s.id, s.name AS class_name, l.name AS location_name, u.name AS teacher_name, s.days, s.openFrom, s.openTo FROM `sclasses` as s LEFT JOIN locations AS l ON l.id = s.location LEFT JOIN users AS u ON u.id = s.fk_user_id WHERE FIND_IN_SET('".$current_day."',s.days) > 0 AND s.soft_delete = 0 AND s.label_type = 'classes' ORDER BY s.openFrom ASC");
        }
        else {
            $query_data = $conn->execute("SELECT s.id, s.name AS class_name, l.name AS location_name, u.name AS teacher_name, s.days, s.openFrom, s.openTo FROM `sclasses` as s LEFT JOIN locations AS l ON l.id = s.location LEFT JOIN users AS u ON u.id = s.fk_user_id WHERE FIND_IN_SET('".$current_day."',s.days) > 0 AND s.soft_delete = 0 AND s.label_type = 'classes' AND s.fk_user_id = ".$user_id." ORDER BY s.openFrom ASC");
        }
        $res_data = $query_data->fetchAll('assoc');

        $res = array();
        $today_class_res = array();
        if(!empty($res_data))
        {
            $open_class_ids = array();
            $all_class_ids  = array();
            foreach($res_data as $r)
            {
                $days_array = explode(',',$r['days']);
                $index_of_current_day = array_search($current_day, $days_array);

                $openFrom_array = explode(',',$r['openFrom']);
                $oFrom = isset($openFrom_array[$index_of_current_day]) && $openFrom_array[$index_of_current_day] ? date('H:i',strtotime($openFrom_array[$index_of_current_day])) : '';

                $openTo_array = explode(',',$r['openTo']);
                $oTo = isset($openTo_array[$index_of_current_day]) && $openTo_array[$index_of_current_day] ? date('H:i',strtotime($openTo_array[$index_of_current_day])) : '';

                $class_is_open = 0;
                if(in_array($current_day,$days_array) && $oFrom <= date('H:i') && $oTo >= date('H:i'))
                {
                    $class_is_open = 1;
                    $open_class_ids[] = $r['id'];
                }
                $all_class_ids[] = $r['id'];


                $r['total_user']     = 0;
                $r['class_is_open']  = $class_is_open;
                $res[$r['id']] = $r;


                if(in_array($current_day,$days_array))
                {
                    $today_class_res[$r['id']] = $r;
                }

            }

            if(!empty($open_class_ids))
            {
                $class_ids = implode(',',$open_class_ids);
                $cStart_date = date('Y-m-d').'T00:00:00.00';
                $cEnd_date = date('Y-m-d').'T23:59:59.999';
                $query_data1 = $conn->execute("SELECT class_id, COUNT(class_id) AS total_user FROM `attendance` WHERE checkin = 1 AND class_id IN (".$class_ids.") AND attendance_date_time BETWEEN '".$cStart_date."' AND '".$cEnd_date."' GROUP BY class_id");
                $attendance_res_data = $query_data1->fetchAll('assoc');

                if(!empty($attendance_res_data))
                {
                    foreach($attendance_res_data as $a)
                    {
                        if(isset($res[$a['class_id']]))
                        {
                            $res[$a['class_id']]['total_user'] = $a['total_user'];
                        }
                        if(isset($today_class_res[$a['class_id']]))
                        {
                            $today_class_res[$a['class_id']]['total_user'] = $a['total_user'];
                        }
                    }
                }
            }



            if(!empty($all_class_ids))
            {
                $class_ids = implode(',',$all_class_ids);
                $cStart_date = date('Y-m-d',strtotime('-1 day')).'T00:00:00.00';
                $cEnd_date = date('Y-m-d',strtotime('-1 day')).'T23:59:59.999';
                $query_data1 = $conn->execute("SELECT class_id, COUNT(class_id) AS total_user FROM `attendance` WHERE checkin = 1 AND class_id IN (".$class_ids.") AND attendance_date_time BETWEEN '".$cStart_date."' AND '".$cEnd_date."' GROUP BY class_id");
                $attendance_res_data = $query_data1->fetchAll('assoc');

                if(!empty($attendance_res_data))
                {
                    foreach($attendance_res_data as $a)
                    {
                        if(isset($res[$a['class_id']]))
                        {
                            $res[$a['class_id']]['total_user'] = $a['total_user'];
                        }
                        if(isset($today_class_res[$a['class_id']]))
                        {
                            $today_class_res[$a['class_id']]['total_user'] = $a['total_user'];
                        }
                    }
                }
            }

        }
        $this->set(compact('res','today_class_res'));
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

    public function reports()
    {
        $conn = ConnectionManager::get("default"); // name of your database connection

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance as a LEFT JOIN sclasses as s ON s.id = a.class_id ORDER BY a.id DESC');
        } else if(in_array('Teacher',$session_user_role)) {
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance as a LEFT JOIN sclasses as s ON s.id = a.class_id WHERE s.fk_user_id = "'.$user_id.'" ORDER BY a.id DESC');
        } else {
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance  as a LEFT JOIN sclasses as s ON s.id = a.class_id WHERE s.fk_user_id = "'.$user_id.'"  ORDER BY id DESC');
        }

        $response = $query_data->fetchAll('assoc');

        $auditArray = array();

        $this->loadModel('Users');

        $this->loadModel('Sclasses');


        foreach($response as $audits){

            $class_id = isset($audits['class_id']) ? $audits['class_id'] : '';
            $fk_user_id = isset($audits['fk_user_id']) ? $audits['fk_user_id'] : 0;
            $id = isset($audits['id']) ? $audits['id'] : '';
            $attendance_date_time = isset($audits['attendance_date_time']) ? $audits['attendance_date_time'] : '';
            $checkin_date_time = isset($audits['checkin_date_time']) ? $audits['checkin_date_time'] : '';
            $checkout_date_time = isset($audits['checkout_date_time']) ? $audits['checkout_date_time'] : '';
            $checkin = (isset($audits['checkin']) && $audits['checkin'] == 1) ? 'Yes' : 'No';
            $checkout = (isset($audits['checkout']) && $audits['checkout'] == 1) ? 'Yes' : 'No';
            $label_type = isset($audits['label_type']) ? $audits['label_type'] : '';

            $username = '';

            if($fk_user_id != 0) {
                $userTable = TableRegistry::get('Users');
                $exists = $userTable->exists(['id' => $fk_user_id]);
                if (empty($exists)) {
                    continue;
                }
                if($exists) {
                    $stmt = $conn->execute('SELECT * FROM users where id="'.$fk_user_id.'"');
                    $results = $stmt->fetch('assoc');
                    $username = $results['name'];
                }
            }

            $classname = '';

            if($class_id != 0) {
                $classTable = TableRegistry::get('Sclasses');
                $exists = $classTable->exists(['id' => $class_id]);
                if (empty($exists)) {
                    continue;
                }
                if($exists) {
                    $stmt = $conn->execute('SELECT * FROM sclasses where id="'.$class_id.'"');
                    $results = $stmt->fetch('assoc');
                    $classname = $results['name'];
                }
            }

            if(!empty($label_type) && $label_type == 'classes') {
                $label_type = 'Classes';
            } else if(!empty($label_type) && $label_type == 'accesscontrol') {
                $label_type = 'Access Control';
            }


            if($id) {
                $auditArray[] = array(
                    'id' => $id,
                    'classname' => $classname,
                    'username' => $username,
                    'checkout' => $checkout,
                    'checkin' => $checkin,
                    'checkout_date_time' => $checkout_date_time,
                    'checkin_date_time' => $checkin_date_time,
                    'attendance_date_time' => $attendance_date_time,
                    'label_type' => $label_type
                );
            }
        }


        $this->set(compact('auditArray'));

        $this->set('page_title', 'Attendance Report');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );



    }

    public function reportsList()
    {
        $conn = ConnectionManager::get("default"); // name of your database connection

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $session_user_role = explode(",", $role_id);

        if(in_array('Admin',$session_user_role)) {
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance as a LEFT JOIN sclasses as s ON s.id = a.class_id ORDER BY a.id DESC');
        } else if(in_array('Teacher',$session_user_role)) {
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance as a LEFT JOIN sclasses as s ON s.id = a.class_id WHERE s.fk_user_id = "'.$user_id.'" ORDER BY a.id DESC');
        } else {
            $query_data = $conn->execute('SELECT a.*, s.label_type FROM attendance  as a LEFT JOIN sclasses as s ON s.id = a.class_id WHERE s.fk_user_id = "'.$user_id.'"  ORDER BY id DESC');
        }

        $response = $query_data->fetchAll('assoc');

        $auditArray = array();

        $this->loadModel('Users');

        $this->loadModel('Sclasses');


        foreach($response as $audits){

            $class_id = isset($audits['class_id']) ? $audits['class_id'] : '';
            $fk_user_id = isset($audits['fk_user_id']) ? $audits['fk_user_id'] : 0;
            $id = isset($audits['id']) ? $audits['id'] : '';
            $attendance_date_time = isset($audits['attendance_date_time']) ? $audits['attendance_date_time'] : '';
            $checkin_date_time = isset($audits['checkin_date_time']) ? $audits['checkin_date_time'] : '';
            $checkout_date_time = isset($audits['checkout_date_time']) ? $audits['checkout_date_time'] : '';
            $checkin = (isset($audits['checkin']) && $audits['checkin'] == 1) ? 'Yes' : 'No';
            $checkout = (isset($audits['checkout']) && $audits['checkout'] == 1) ? 'Yes' : 'No';
            $label_type = isset($audits['label_type']) ? $audits['label_type'] : '';

            $username = '';

            if($fk_user_id != 0) {
                $userTable = TableRegistry::get('Users');
                $exists = $userTable->exists(['id' => $fk_user_id]);
                if (empty($exists)) {
                    continue;
                }
                if($exists) {
                    $stmt = $conn->execute('SELECT * FROM users where id="'.$fk_user_id.'"');
                    $results = $stmt->fetch('assoc');
                    $username = $results['name'];
                }
            }

            $classname = '';

            if($class_id != 0) {
                $classTable = TableRegistry::get('Sclasses');
                $exists = $classTable->exists(['id' => $class_id]);
                if (empty($exists)) {
                    continue;
                }
                if($exists) {
                    $stmt = $conn->execute('SELECT * FROM sclasses where id="'.$class_id.'"');
                    $results = $stmt->fetch('assoc');
                    $classname = $results['name'];
                }
            }

            if(!empty($label_type) && $label_type == 'classes') {
                $label_type = 'Classes';
            } else if(!empty($label_type) && $label_type == 'accesscontrol') {
                $label_type = 'Access Control';
            }


            if($id) {
                $auditArray[] = array(
                    'id' => $id,
                    'classname' => $classname,
                    'username' => $username,
                    'checkout' => $checkout,
                    'checkin' => $checkin,
                    'checkout_date_time' => $checkout_date_time,
                    'checkin_date_time' => $checkin_date_time,
                    'attendance_date_time' => $attendance_date_time,
                    'label_type' => $label_type
                );
            }
        }


        $this->set(compact('auditArray'));

        $this->set('page_title', 'Attendance Report');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');

        $this->loadModel('Users');

        $this->set('count_students', $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => ['usertype' => 'Teacher', 'status' => 1, 'soft_delete' => 0] ))->count() );

        $this->set('count_staff', $this->Users->find('all',array('conditions' => ['usertype' => 'Staff', 'status' => 1, 'soft_delete' => 0] ))->count() );



    }

    public function classAttendance($idEncode = null,$date=null)
    {
        $class_id = $this->Global->userIdDecode($idEncode);

        $conn = ConnectionManager::get("default"); // name of your database connection

        $this->set('page_title', 'Class Attendance Report');

        $this->set('page_icon', '<i class="fal fa-clipboard-user mr-1"></i>');

        $user_id = $this->Auth->user( 'id' );

        $role_id = $this->Auth->user( 'usertype' );

        $attendance_date = date('Y-m-d');
        if($date)
        {
            $attendance_date = date('Y-m-d',$date);
        }
        $session_user_role = explode(",", $role_id);
        $cStart_date = $attendance_date.'T00:00:00.00';
        $cEnd_date = $attendance_date.'T23:59:59.999';

        if(in_array('Admin',$session_user_role))
        {

            $query_data = $conn->execute("SELECT a.id, u.name as student_name, u.akcessId as akcess_id, a.checkin_date_time, a.checkout_date_time FROM attendance AS a LEFT JOIN users AS u ON u.id = a.fk_user_id WHERE a.checkin = 1 AND a.class_id = ".$class_id." AND a.soft_delete = 0 AND a.attendance_date_time BETWEEN '".$cStart_date."' AND '".$cEnd_date."' ORDER BY a.id DESC");
        }
        else
        {
            $query_data = $conn->execute("SELECT a.id, u.name as student_name, u.akcessId as akcess_id, a.checkin_date_time, a.checkout_date_time FROM attendance AS a LEFT JOIN users AS u ON u.id = a.fk_user_id WHERE a.checkin = 1 AND a.class_id = ".$class_id." AND s.fk_user_id = ".$user_id." AND a.soft_delete = 0 AND a.attendance_date_time BETWEEN '".$cStart_date."' AND '".$cEnd_date."' ORDER BY a.id DESC");
        }
        $res = $query_data->fetchAll('assoc');


//        $cStart_date1 = date('Y-m-d',strtotime("-1 days")).'T00:00:00.00';
//        $cEnd_date1 = date('Y-m-d',strtotime("-1 days")).'T23:59:59.999';
//
//        $query_data2 = $conn->execute("SELECT COUNT(a.id) as total_checkin_student FROM attendance AS a WHERE a.checkin = 1 AND a.class_id = ".$class_id." AND a.soft_delete = 0 AND a.attendance_date_time BETWEEN '".$cStart_date1."' AND '".$cEnd_date1."' ORDER BY a.id DESC");
//        $res2 = $query_data2->fetchAll('assoc');
//        $last_class_atten_total_student = isset($res2[0]['total_checkin_student']) && $res2[0]['total_checkin_student'] ? $res2[0]['total_checkin_student'] : 0;

        $this->loadModel('ClassAttends');
        $this->set('last_class_atten_total_student', $this->ClassAttends->find('all',array('conditions' => ['classId' => $class_id, 'soft_delete' => 0] ))->count() );


        $attendance_date = date('d/m/Y');
        if($date)
        {
            $attendance_date = date('d/m/Y',$date);
        }

        $this->set(compact('res','idEncode','attendance_date'));

        $query_data1 = $conn->execute("SELECT s.id, s.name AS class_name, l.name AS location_name, u.name AS teacher_name, s.userallow as no_of_students FROM `sclasses` as s LEFT JOIN locations AS l ON l.id = s.location LEFT JOIN users AS u ON u.id = s.fk_user_id WHERE s.id = ".$class_id." ORDER BY s.openFrom ASC");
        $res_data = $query_data1->fetchAll('assoc');
        $this->set('class_data', isset($res_data[0]) ? $res_data[0] : array());
    }

}
