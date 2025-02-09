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
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link https://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class DashboardController extends AppController
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

    public function index()
    {
        $this->loadModel('Users');

        $conn = ConnectionManager::get("default"); // name of your database connection   
        
        $role_id = $this->Auth->user( 'usertype' );
       
        $this->set('count_students', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Student%'),
            'status' => 1,
            'soft_delete' => 0
        ]))->count() );

        $this->set('count_teacher', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Teacher%'), 
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );
        
        $this->set('count_staff', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Staff%'),  
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );

        $this->set('count_admin', $this->Users->find('all',array('conditions' => [
            array('usertype LIKE'=>'%Admin%'),  
            'status' => 1, 
            'soft_delete' => 0
        ] ))->count() );

        // Staff check in / check out start
        $date = date('Y-m-d');
        $query_response_staff_checkin = "SELECT count(`a`.`id`) as count_staff_checkin_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkin = 1 AND usertype like '%Staff%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
           
        $results_staff_checkin = $conn->execute($query_response_staff_checkin);

        $data_staff_checkin = $results_staff_checkin->fetch('assoc');

        $count_staff_checkin_today = $data_staff_checkin['count_staff_checkin_today'];

        $this->set('count_staff_checkin_today', $count_staff_checkin_today);

        $query_response_staff_checkout = "SELECT count(`a`.`id`) as count_staff_checkout_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkout = 1 AND usertype like '%Staff%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
           
        $results_staff_checkout = $conn->execute($query_response_staff_checkout);

        $data_staff_checkout = $results_staff_checkout->fetch('assoc');

        $count_staff_checkout_today = $data_staff_checkout['count_staff_checkout_today'];

        $this->set('count_staff_checkout_today', $count_staff_checkout_today);

        // Staff check in / check out end

        // Admin check in / check out start
        $query_response_admin_checkin = "SELECT count(`a`.`id`) as count_admin_checkin_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkin = 1 AND usertype like '%Admin%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
           
        $results_admin_checkin = $conn->execute($query_response_admin_checkin);

        $data_admin_checkin = $results_admin_checkin->fetch('assoc');

        $count_admin_checkin_today = $data_admin_checkin['count_admin_checkin_today'];

        $this->set('count_admin_checkin_today', $count_admin_checkin_today);

        $query_response_admin_checkout = "SELECT count(`a`.`id`) as count_admin_checkout_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkout = 1 AND usertype like '%Admin%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
           
        $results_admin_checkout = $conn->execute($query_response_admin_checkout);

        $data_admin_checkout = $results_admin_checkout->fetch('assoc');

        $count_admin_checkout_today = $data_admin_checkout['count_admin_checkout_today'];

        $this->set('count_admin_checkout_today', $count_admin_checkout_today);

        // Admin check in / check out end

        // Teacher check in / check out start
        $query_response_teacher_checkin = "SELECT count(`a`.`id`) as count_teacher_checkin_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkin = 1 AND usertype like '%Teacher%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
           
        $results_teacher_checkin = $conn->execute($query_response_teacher_checkin);

        $data_teacher_checkin = $results_teacher_checkin->fetch('assoc');

        $count_teacher_checkin_today = $data_teacher_checkin['count_teacher_checkin_today'];

        $this->set('count_teacher_checkin_today', $count_teacher_checkin_today);

        $query_response_teacher_checkout = "SELECT count(`a`.`id`) as count_teacher_checkout_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkout = 1 AND usertype like '%Teacher%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
           
        $results_teacher_checkout = $conn->execute($query_response_teacher_checkout);

        $data_teacher_checkout = $results_teacher_checkout->fetch('assoc');

        $count_teacher_checkout_today = $data_teacher_checkout['count_teacher_checkout_today'];

        $this->set('count_teacher_checkout_today', $count_teacher_checkout_today);

        // Teacher check in / check out end

        // Students check in / check out start

        $query_response_students_checkin = "SELECT count(`a`.`id`) as count_students_checkin_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkin = 1 AND usertype like '%Student%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
            
        $results_students_checkin = $conn->execute($query_response_students_checkin);

        $data_students_checkin = $results_students_checkin->fetch('assoc');

        $count_students_checkin_today = $data_students_checkin['count_students_checkin_today'];

        $this->set('count_students_checkin_today', $count_students_checkin_today);

        $query_response_students_checkout = "SELECT count(`a`.`id`) as count_students_checkout_today FROM `attendance` as a
            LEFT JOIN users as u ON u.id = a.fk_user_id
            WHERE checkout = 1 AND usertype like '%Student%'
            AND (date(a.created) BETWEEN CONCAT('".$date." 00:00:00') AND CONCAT('".$date." 23:59:59'))";
           
        $results_students_checkout = $conn->execute($query_response_students_checkout);

        $data_students_checkout = $results_students_checkout->fetch('assoc');

        $count_students_checkout_today = $data_students_checkout['count_students_checkout_today'];

        $this->set('count_students_checkout_today', $count_students_checkout_today);

        // Students check in / check out start

        $query_response = "SELECT count(`id`) as count_eform_sent_today FROM `sendData` 
                    WHERE ackessID != '' 
                    AND date(createdDate) = CURRENT_DATE()
                    AND recievedType='eform'";
           
        $results = $conn->execute($query_response);

        $data = $results->fetch('assoc');

        $count_eform_sent_today = $data['count_eform_sent_today'];

        $this->set('count_eform_sent_today', $count_eform_sent_today);

        $query_eform_response = "SELECT count(`id`) as count_eform_received_today FROM `eformresponse` 
                    WHERE akcessId != '' 
                    AND date(created) = CURRENT_DATE()";
           
        $results_response_received = $conn->execute($query_eform_response);

        $data_response_received = $results_response_received->fetch('assoc');

        $count_eform_received_today = $data_response_received['count_eform_received_today'];

        $this->set('count_eform_received_today', $count_eform_received_today);
        
        $this->loadModel('Activity');
        
        $this->set('countActivity', $this->Activity->find('all')->count());
        
        $users = $this->Users->find('all',array('conditions' => ['usertype' => 'Student', 'status' => 0, 'soft_delete' => 0] ));
        
        $this->set(compact('users', 'role_id'));

        if($role_id == 'Admin'){ 
        
            $this->set('page_title', 'Registration Requests');

            $this->set('page_icon', '<i class="fal fa-columns mr-1"></i>');

        } else if($role_id == 'Student'){ 

            $this->set('page_title', 'Home');

            $this->set('page_icon', '<i class="fal fa-home mr-1"></i>');

        }else{
            $this->set('page_title', 'Dashboard');
            $this->set('page_icon', '<i class="fal fa-columns mr-1"></i>');
        }

    }
}
