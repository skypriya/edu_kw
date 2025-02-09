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
class InvitationlistController extends AppController
{

    public function initialize() {
        
        parent::initialize();

        $this->loadComponent('Global');  
        
        $this->loadModel('UsersInvitation');
        
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
        
        $this->loadModel('Invitationlist');
        
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

        $sql_fields = "SELECT `users_by_akcess`.* FROM `users_by_akcess` LEFT JOIN `users_invitation` ON 
        `users_invitation`.`users_by_akcess_id` = `users_by_akcess`.`id` ORDER BY `users_by_akcess`.`id` DESC";

        $sql_fields_id = $conn->execute($sql_fields);

        $InvitationlistArray = $sql_fields_id->fetchAll('assoc');

        $Invitationlist = array();

        $this->loadModel('Users');
        
        foreach($InvitationlistArray as $key => $value) {  

            $fk_senddata_id = $value['fk_senddata_id'];
            $created = $value['created'];
            $idSingle = 0;
            if(!empty($fk_senddata_id)) {
                $eformResponseID_data = $conn->execute("SELECT id FROM eformresponse WHERE responseSendID = '" . $fk_senddata_id . "'");
                $eformResponseIDDetails = $eformResponseID_data->fetch('assoc');
                $idSingle = $eformResponseIDDetails['id'];
            }
            
            $Invitationlist[] = array(
                'id' => $value['id'],
                'fk_eform_id' => $value['fk_eform_id'],
                'fk_senddata_id' => $idSingle,
                'requested_user_id' => $value['requested_user_id'],
                'type' => $value['type'],
                'send_request_type' => $value['send_request_type'],
                'status' => $value['status'],
                'akcessId' => $value['akcessId'],
                'email' => $value['email'],
                'phoneno' => $value['phoneno'],
                'created' => $created
            );
        }

        $this->set(compact('Invitationlist'));
        
        $this->set('page_title', 'Invitation List');
    }  

    public function postInvitationData()
    {
        $conn = ConnectionManager::get("default"); // name of your database connection     

        $idEncode = isset($_POST['sd']) ? $_POST['sd'] : '';
        $customInvitation = isset($_POST['customInvitation']) ? $_POST['customInvitation'] : '';

        if(empty($idEncode)) {
            $data['status'] = 1;
            $data['message'] = "Something is wrong!";
            $res = json_encode($data);
            $this->response->type('json');
            $this->response->body($res);

            return $this->response;
        }
        
        if(empty($customInvitation)) {
            $data['status'] = 1;
            $data['message'] = "Status is required fields";
            $res = json_encode($data);
            $this->response->type('json');
            $this->response->body($res);

            return $this->response;
        }

        $id = $this->Global->userIdDecode($idEncode);

        if (!empty($id)) {

            $sql_fields = "SELECT `users_invitation`.*,`users_by_akcess`.`type` FROM `users_invitation` LEFT JOIN `users_by_akcess` ON 
                            `users_invitation`.`users_by_akcess_id` = `users_by_akcess`.`id` WHERE `users_invitation`.`id`=".$id." 
                            ORDER BY `id` DESC LIMIT 0,1";

            $sql_fields_id = $conn->execute($sql_fields);

            $sql_last_fields = $sql_fields_id->fetch('assoc');

            $name = isset($sql_last_fields['name']) ? $sql_last_fields['name'] : "";
            $companyName = isset($sql_last_fields['companyName']) ? $sql_last_fields['companyName'] : "";
            $address = isset($sql_last_fields['address']) ? $sql_last_fields['address'] : "";
            $city = isset($sql_last_fields['city']) ? $sql_last_fields['city'] : "";
            $country = isset($sql_last_fields['country']) ? $sql_last_fields['country'] : "";
            $email = isset($sql_last_fields['email']) ? $sql_last_fields['email'] : "";
            $password = isset($sql_last_fields['password']) ? $sql_last_fields['password'] : 123;
            $mobileNumber = isset($sql_last_fields['mobileNumber']) ? $sql_last_fields['mobileNumber'] : "";
            $usertype = isset($sql_last_fields['type']) ? ucfirst($sql_last_fields['type']) : "";
            $gender = isset($sql_last_fields['gender']) ? $sql_last_fields['gender'] : "";
            $dob = isset($sql_last_fields['dob']) ? $sql_last_fields['dob'] : "";
            $photo = isset($sql_last_fields['photo']) ? $sql_last_fields['photo'] : "";
            $otherdetails = isset($sql_last_fields['otherdetails']) ? $sql_last_fields['otherdetails'] : "";
            $passkey = isset($sql_last_fields['passkey']) ? $sql_last_fields['passkey'] : "";
            $timeout = isset($sql_last_fields['timeout']) ? $sql_last_fields['timeout'] : "";
            $domainName = isset($sql_last_fields['domainName']) ? $sql_last_fields['domainName'] : "";
            $administrationName = isset($sql_last_fields['administrationName']) ? $sql_last_fields['administrationName'] : "";
            $loginOpt = isset($sql_last_fields['loginOpt']) ? $sql_last_fields['loginOpt'] : "";
            $siteStatus = isset($sql_last_fields['siteStatus']) ? $sql_last_fields['siteStatus'] : "";
            $status = isset($sql_last_fields['status']) ? $sql_last_fields['status'] : "";
            $active = isset($sql_last_fields['active']) ? 'yes' : 'no';
            $faculty = isset($sql_last_fields['faculty']) ? $sql_last_fields['faculty'] : "";
            $courses = isset($sql_last_fields['courses']) ? $sql_last_fields['courses'] : "";
            $academic_personal_type = isset($sql_last_fields['academic_personal_type']) ? $sql_last_fields['academic_personal_type'] : "";
            $staff_type = isset($sql_last_fields['staff_type']) ? $sql_last_fields['staff_type'] : "";
            $adminssion_date = isset($sql_last_fields['adminssion_date']) ? $sql_last_fields['adminssion_date'] : "";
            $created = isset($sql_last_fields['created']) ? $sql_last_fields['created'] : "";
            $akcessId = isset($sql_last_fields['akcessId']) ? $sql_last_fields['akcessId'] : "";
            $soft_delete = isset($sql_last_fields['soft_delete']) ? $sql_last_fields['soft_delete'] : "";
            $users_by_akcess_id = isset($sql_last_fields['users_by_akcess_id']) ? $sql_last_fields['users_by_akcess_id'] : "";

            $country_id = 0;
            if (isset($country) && $country != "") {
                $query_data = $conn->execute('SELECT * FROM countries where lower(country_name) = "' . strtolower($country) . '"');
                $query_country = $query_data->fetch('assoc');
                $country_id = $query_country['id'];
            }

            $staff_type_id = 0;
            if (isset($staff_type) && $staff_type != "") {
                $query_data_staff = $conn->execute('SELECT * FROM stafflist where lower(name) = "' . strtolower($staff_type) . '"');
                $query_staff_type = $query_data_staff->fetch('assoc');
                $staff_type_id = $query_staff_type['id'];
            }

            $academic_personal_type_id = 0;
            if (isset($academic_personal_type) && $academic_personal_type != "") {
                $query_data_academic_personal_type = $conn->execute('SELECT * FROM academiclist where lower(name) = "' . strtolower($academic_personal_type) . '"');
                $query_academic_personal_type = $query_data_academic_personal_type->fetch('assoc');
                $academic_personal_type_id = $query_academic_personal_type['id'];
            }
            

            $mobileno = '';
            if (isset($mobileNumber) && $mobileNumber != "") {
                $mobile_explode = explode(" ", $mobileNumber);
                foreach($mobile_explode as $key => $value) {
                    if($key > 0) {
                        $mobileno .= $value;
                    }
                }                
            }

            if ($customInvitation == 'accept') {

                $query_check_akcess_id = $conn->execute('SELECT * FROM users where lower(akcessId) = "' . strtolower($akcessId) . '"');
                $query_check_akcess = $query_check_akcess_id->fetch('assoc');
                $user_id = $query_check_akcess['id'];
               
                if($user_id > 0) {
                    $usertype_explode = isset($query_check_akcess['usertype']) ? explode(",",$query_check_akcess['usertype']) : "";
                    $utype = $usertype;
                    if(!empty($usertype_explode)) {
                        $comma = ",";
                        foreach($usertype_explode as $value) {
                            if (isset($value)) {
                                $utype .= $comma . ucfirst($value);
                            }
                        }
                    }

                    $str = implode(',',array_unique(explode(',', $utype)));
                    $str = ltrim($str,',');
                    $str = rtrim($str,',');

                    $migration  = '';

                    if(isset($name)) {
                        $migration .= "`name`='".$name."',";
                    }

                    if(isset($companyName)) {
                        $migration .= "`companyName`='".$companyName."',";
                    }

                    if(isset($address)) {
                        $migration .= "`address`='".$address."',";
                    }

                    if(isset($city)) {
                        $migration .= "`city`='".$city."',";
                    }

                    if(isset($country)) {
                        $migration .= "`country`='".$country."',";
                    }

                    if(isset($email)) {
                        $migration .= "`email`='".$email."',";
                    }

                    if(isset($mobileno)) {
                        $migration .= "`mobileNumber`='".$mobileno."',";
                    }

                    if(isset($dob)) {
                        $migration .= "`dob`='".$dob."',";
                    }

                    if(isset($otherdetails)) {
                        $migration .= "`otherdetails`='".$otherdetails."',";
                    }

                    if(isset($administrationName)) {
                        $migration .= "`administrationName`='".$administrationName."',";
                    }

                    if(isset($faculty)) {
                        $migration .= "`faculty`='".$faculty."',";
                    }

                    if(isset($academic_personal_type_id)) {
                        $migration .= "`academic_personal_type`='".$academic_personal_type_id."',";
                    }

                    if(isset($staff_type_id)) {
                        $migration .= "`staff_type`='".$staff_type_id."',";
                    }

                    if(isset($adminssion_date)) {
                        $migration .= "`adminssion_date`='".$adminssion_date."',";
                    }

                    $update_query = "UPDATE `users` SET
                        `name`='".$name."',
                        `companyName`='".$companyName."',
                        `address`='".$address."', 
                        `city`='".$city."',
                        `country`='".$country_id."',
                        `email`='".$email."',
                        `mobileNumber`='".$mobileno."',
                        `gender`='".$gender."', 
                        `dob`='".$dob."', 
                        `otherdetails`='".$otherdetails."',
                        `administrationName`='".$administrationName."',
                        `faculty`='".$faculty."',
                        `courses`='".$courses."',
                        `academic_personal_type`='".$academic_personal_type_id."',
                        `staff_type`='".$staff_type_id."',
                        `adminssion_date`='".$adminssion_date."',
                        `usertype`='".$str."',
                        `status`='".$status."',
                        `soft_delete`= 0
                        WHERE id=".$user_id;
                    $conn->execute($update_query);
                } else {
                    $insert_query = "INSERT INTO `users` (
                                        `name`, 
                                        `companyName`, 
                                        `address`, 
                                        `city`, 
                                        `country`,
                                        `email`,
                                        `password`,
                                        `mobileNumber`, 
                                        `usertype`, 
                                        `gender`, 
                                        `dob`, 
                                        `photo`,
                                        `otherdetails`,
                                        `passkey`,
                                        `timeout`, 
                                        `domainName`, 
                                        `administrationName`, 
                                        `loginOpt`, 
                                        `siteStatus`,
                                        `status`,
                                        `active`,
                                        `faculty`,
                                        `courses`, 
                                        `academic_personal_type`, 
                                        `staff_type`, 
                                        `adminssion_date`, 
                                        `isLogin`,
                                        `created`,
                                        `akcessId`,
                                        `soft_delete`
                                    ) VALUES (
                                        '".$name."',
                                        '".$companyName."',
                                        '".$address."',
                                        '".$city."',
                                        '".$country_id."',
                                        '".$email."',
                                        '".$password."',
                                        '".$mobileno."',
                                        '".$usertype."',
                                        '".$gender."',
                                        '".$dob."',
                                        '".$photo."',
                                        '".$otherdetails."',
                                        '".$passkey."',
                                        '".$timeout."',
                                        '".$domainName."',
                                        '".$administrationName."',
                                        '".$loginOpt."',
                                        '".$siteStatus."',
                                        '".$status."',
                                        '".$active."',
                                        '".$faculty."',
                                        '".$courses."',
                                        '".$academic_personal_type_id."',
                                        '".$staff_type_id."',
                                        '".$adminssion_date."',
                                        0,
                                        '".$created."',
                                        '".$akcessId."',
                                        0
                                    )";

                    $conn->execute($insert_query);
                }

                $update_query = "UPDATE `users_by_akcess` set `status`=2 WHERE id=".$users_by_akcess_id;

                $conn->execute($update_query);

                $data['message'] = 'Invitation accepted successfully.';

                $this->request->session()->write('notification_message', 'Invitation accepted successfully.');

            } else if($customInvitation == 'reject') {

                $update_query = "UPDATE `users_by_akcess` set `status`=3 WHERE id=".$users_by_akcess_id;

                $conn->execute($update_query);

                $data['message'] = 'Invitation rejected successfully.';

                $this->request->session()->write('notification_message', 'Invitation rejected successfully.');

            }

            $data['status'] = 0;
        } 
            
        $res = json_encode($data);
        $this->response->type('json');
        $this->response->body($res);

        return $this->response;
    }
    
    /**
     * View method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($idEncode = null) {

        $conn = ConnectionManager::get("default"); // name of your database connection        
        
        $id = $this->Global->userIdDecode($idEncode);

        $sql_fields = "SELECT `users_invitation`.*,`users_by_akcess`.`type` FROM `users_invitation` LEFT JOIN `users_by_akcess` ON 
        `users_invitation`.`users_by_akcess_id` = `users_by_akcess`.`id`
        WHERE `users_invitation`.`users_by_akcess_id`=".$id." ORDER BY `users_invitation`.`id` DESC LIMIT 0,1";

        $sql_fields_id = $conn->execute($sql_fields);

        $sql_last_fields = $sql_fields_id->fetch('assoc');

        $status = $_REQUEST['s'];
        
        $html = '';
        $html .= '<div class="input-group">';

        if (isset($sql_last_fields['akcessId'])) {
            $html .= '<div class="col-12 col-lg-12">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-6 mb-0">';            
            $html .= '<label>AKcess ID</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['akcessId'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['name'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Name</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['name'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['email'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Email</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['email'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['mobileNumber'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Mobile Number</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['mobileNumber'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['dob'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Date of birth</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['dob'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['city'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Place of Birth</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['city'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['gender'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Gender</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['gender'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $country = isset($sql_last_fields['country']) ? $sql_last_fields['country'] : "";        
        if (isset($country)) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Nationality</label>';
            $html .= '<input type="text" disabled value="' . $country . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['adminssion_date'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Admission Date</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['adminssion_date'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['address'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Address</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['address'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['faculty'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Faculty</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['faculty'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        if (isset($sql_last_fields['courses'])) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Courses</label>';
            $html .= '<input type="text" disabled value="' . $sql_last_fields['courses'] . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $academic_personal_type = '';
        if(isset($sql_last_fields['academic_personal_type']) && $sql_last_fields['academic_personal_type'] != "") {
            $academic_personal_type = $sql_last_fields['academic_personal_type'];
            $query_data = $conn->execute('SELECT * FROM academiclist where lower(name)="'.strtolower($academic_personal_type).'"');
            $query_academic = $query_data->fetch('assoc');
            $academic_personal_type = $query_academic['name'];
        }

        if (isset($academic_personal_type)) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Academic Personal Type</label>';
            $html .= '<input type="text" disabled value="' . $academic_personal_type . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $stafflist_name = isset($sql_last_fields['staff_type']) ? $sql_last_fields['staff_type'] : "";
        if (isset($sql_last_fields['staff_type']) && $sql_last_fields['staff_type'] != "") {
            $staff_type = $sql_last_fields['staff_type'];
            $query_data = $conn->execute('SELECT * FROM stafflist where lower(name)="'.strtolower($staff_type).'"');
            $query_stafflist = $query_data->fetch('assoc');
            $stafflist_name = $query_stafflist['name'];
        }

        if (isset($stafflist_name)) {
            $html .= '<div class="col-12 col-lg-6">';
            $html .= '<div class="row">';
            $html .= '<div class="form-group col-12 mb-0">';            
            $html .= '<label>Staff Type</label>';
            $html .= '<input type="text" disabled value="' . $stafflist_name . '" class="form-control" />';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }


        $html .= '</div>';

        $data['html'] = $html;
        $data['title'] = isset($sql_last_fields['type']) ? ucfirst($sql_last_fields['type']) : "";

        $button = '';
        $select = '<option value="">Choose your Status</option>';
       
        if ($status == 0) {
            $button = '<button type="submit" id="submit" class="btn btn-primary waves-effect" onclick="submitInvitationEformResponse()">Submit</button>';

            $select .= '<option value="accept">Accept</option>
                <option value="reject">Reject</option>';
        }

        $data['sd'] = $this->Global->userIdEncode($sql_last_fields['id']);
        $data['button'] = $button;
        $data['select'] = $select;
        
        $resultJ = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }
    
}
