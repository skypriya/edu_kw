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
class EformController extends AppController {

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
        
        //$this->SendEformMarketPlaceData();

        //exit;
        
        $this->loadModel('Countries');
        
        $this->loadModel('Users');
        
        $eform = $this->Eform->find('all' , array('conditions' => ['soft_delete' => 0]));    
       
        $users = $this->Users->find('all', array('conditions' => ['status' => 1,'soft_delete' => 0]));
        
        //$countries = $this->Countries->find('all');
		 $topcountries = $this->Countries->find('all')
        ->where(['id IN' => ['1','226']])->toArray();
        
        $othercountries = $this->Countries->find('all')
        ->where(['id NOT IN' => ['1','226']])->order(['country_name'=>'ASC'])->toArray();
        
        $countries=array_merge($topcountries,$othercountries);

       
        $this->set(compact('eform', 'countries', 'users'));
        
        $this->set('page_title', 'All EForms');

        $this->set('page_icon', '<i class="fab fa-wpforms mr-1"></i>');
    }

    
    public function SendEformMarketPlaceData() {        
        
        $conn = ConnectionManager::get("default"); // name of your database connection    
               
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;        
        $ackess = isset($_POST['ackess']) ? $_POST['ackess'] : '';   
        $eid = isset($_POST['eid']) ? $this->Global->userIdDecode($_POST['eid']) : '';      
        $type_doc = 'eform'; 
        $eid = 98;        
        $id = $eid;
                
        $user_id = $this->Auth->user( 'id' );
        
        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
        
        $eform = $this->Eform->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
                                
        $formName = $eform->formName;
        
        $description = $eform->description;
        
        $instruction = $eform->instruction;
        
        $user_id = $user_id;
        
        $date = date('Y-m-d H:i:s');
        
        $signature = $eform->signature;
        
        $facematch = $eform->facematch;
        
        $pulldata = $eform->pulldata;
        
        $publish = $eform->publish;
        
        $additional_notification = $eform->isAdditionalNotification;
        
        $isAdditionalNotificationDetails = "";

        if($additional_notification == 'yes') {

            $sql_query = "SELECT akcessId,email,mobile FROM `additionalnotificationto` WHERE fk_eform_id='".$id."'";

            $sql_query_data = $conn->execute($sql_query);

            $result_sql = $sql_query_data->fetchAll('assoc');
           
            if($result_sql) {

                foreach($result_sql as $result_sqls) {

                    $akcessIds = $result_sqls['akcessId'];
                    $emails = $result_sqls['email'];
                    $mobiles = $result_sqls['mobile'];

                    $isAdditionalNotificationDetails .= '{"akcessId":"'.$akcessIds.'", "email":"'.$emails.'", "mobile":"'.$mobiles.'"},';

                }

            }

        }

        $isAdditionalNotificationDetails = rtrim($isAdditionalNotificationDetails, ',');

        $array_akcesss_id = "";
        
        $array_akcesss_id .= "[";
        
        foreach($ackess as $akcessIds) {
             
            $akcessId = $akcessIds;

            $array_akcesss_id .= '"'.$akcessId.'",';

        }

        $array_akcesss_id = rtrim($array_akcesss_id, ',');

        $array_akcesss_id .= "]";
                
        $storeinprofile = $eform->storeinprofile;
        
        $isclientInvitationEform = $eform->isclientInvitationEform;   
        
        $this->loadModel('Fields');        
        
        $fieldsInformation_query = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));

        $eformFields = array();
        
        $eformFieldText = '';
        
        foreach($fieldsInformation_query as $key => $fieldsInformations){
         
            $field_id = isset($fieldsInformations->id) ? $fieldsInformations->id : ''; 
            $name = isset($fieldsInformations->labelname) ? $fieldsInformations->labelname : ''; 
            $instructions = isset($fieldsInformations->instructions) ? $fieldsInformations->instructions : ''; 
            $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
            $isVisible = isset($fieldsInformations->isVisible) ? $fieldsInformations->isVisible : ''; 
            $section_id = isset($fieldsInformations->section_id) ? $fieldsInformations->section_id : ''; 
            $section_color = isset($fieldsInformations->section_color) ? $fieldsInformations->section_color : ''; 
            $sectionfields = isset($fieldsInformations->sectionfields) ? $fieldsInformations->sectionfields : ''; 
            $verification_grade = isset($fieldsInformations->verification_grade) ? $fieldsInformations->verification_grade : ''; 
            $fieldver = isset($fieldsInformations->file_verified) ? $fieldsInformations->file_verified : ''; 
            $field_mandate = isset($fieldsInformations->is_mandatory) ? $fieldsInformations->is_mandatory : ''; 
            $signature_required = isset($fieldsInformations->signature_required) && $fieldsInformations->signature_required != "" ? $fieldsInformations->signature_required : 'no'; 
            $is_mandatory = isset($fieldsInformations->signatureis_mandatory_required) && $fieldsInformations->is_mandatory != "" ? $fieldsInformations->is_mandatory : 'no'; 
            $ids = isset($fieldsInformations->ids) ? $fieldsInformations->ids : ''; 
            $items = isset($fieldsInformations->options) ? $fieldsInformations->options : ''; 
            $key = isset($fieldsInformations->keyfields) ? $fieldsInformations->keyfields : ''; 
            $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
            
            $key_value = $key;
            if (strpos($key, '_') !== false) { 
                $key_value_explode = explode("_", $key);                
                $key_value = $key_value_explode[0];
            }
           
            $this->loadModel('FieldsOptions');        
        
            $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
            ]);
            
            $array_items = '';
            
            $array_items .= "[";

            foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                
                $optionsid = $fieldsOptionsInformations->uid;
                $checked = $fieldsOptionsInformations->checked;
                $lable = $fieldsOptionsInformations->lable;
                $key = $fieldsOptionsInformations->keyfields;

                $array_items .= '{
                    "checked": '.$checked.',
                    "keyfields": "'.strtolower($key).'",
                    "lable": "'.$lable.'",
                    "uid": "'.$optionsid.'"
                },';
            }
            $array_items = rtrim($array_items, ',');
            
            $array_items .= "]";
            
            $eformFieldText .= '{
                "options": '.$array_items.',
                "labelname": "'.$name.'",
                "key": "'.$key_value.'",
                "keytype": "'.$typeIn.'",
                "signature_required": "'.$signature_required.'",
                "file_verified": "'.$fieldver.'",
                "verification_grade": "'.$verification_grade.'",
                "instructions": "'.trim($instructions).'",
                "is_mandatory": "'.$is_mandatory.'"
            },';            
        }
                
        $eformFieldText = rtrim($eformFieldText, ',');
               
        $response_token = $this->Global->getToken();
           
        $token = $response_token;  
        
        $description_title = "Eform";        
      
        $data_array = '{
            "formName": "'.$formName.'",
            "name": "'.ORIGIN_URL.'",
            "description": "'.$description.'",
            "redirectURL":"",
            "logo": "'.$src.'",
            "eformId": "'.$eform->eformid.'",
            "status":"'.$publish.'",
            "country":"",
            "date":"'.$date.'",
            "fields": ['.$eformFieldText.'],
            "signature": "'.$signature.'",
            "facematch": "'.$facematch.'",
            "pulldata": "'.$pulldata.'",
            "instruction": "'.$instruction.'",
            "domainName": "'.ORIGIN_URL.'",
            "isAdditionalNotification": "'.$additional_notification.'",
            "additionalNotificationTo": ['.$isAdditionalNotificationDetails.'],
            "storeinprofile": "'.$storeinprofile.'"
        }'; 


        //$data_array = json_encode($data_array);

        $origin_array = array(
            'Content-Type: application/json',
            'authorization: ' . $token,
            'apikey: ' . $api,
            'Origin: ' . $origin_url
        );    
        
        //marketplace-eforms

        $type_method = 'marketplace-eforms';

        $method = "POST";   

        $response_data_akcess = $this->Global->curlGetPostEform($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);    
        
        $response_akcess_Data = json_decode($response_data_akcess);     

        echo "<pre>";
        print_R($response_akcess_Data);
        exit;
        
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

        $eform = $this->Eform->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $this->loadModel('Fields');
        
        $fields = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));
        
        $html = '';
        $html .= '<div class="form-group col-12">';
        $html .= '<div class="input-group">';

        $html .= '';
        $html .= '<div class="col-12 col-lg-12">';
        $html .= '<div class="row">';
        $html .= '<div class="form-group col-12 mb-0">';
        $html .= '<input type="text" readonly id="qrcode_url" name="qrcode_url" class="form-control">';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        foreach ($fields as $n) {
            
            $field_check = '0';
            $field_ids = $n->id;
            $field_name = $n->keyfields;
            $field_label = $n->labelname;
            $field_type = $n->keytype;
            $field_isVisible = $n->isVisible;
            $field_section = $n->section;
            $field_verification_grade = $n->verification_grade;
            $field_verified = $n->file_verified;
            $field_mandate = $n->is_mandatory;
            $field_items = $n->options;
            $field_label_instructions = $n->instructions;            
            if ( $field_type == 'file') {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<div class="custom-file">';
                $html .= '<input type="file" class="custom-file-input"  data-instructions="' . $field_label_instructions . '"  data-name="' . $field_name . '"  data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '"  data-section="' . $field_section . '"  data-verification-grade="' . $field_verification_grade . '"  data-fieldver="' . $field_verified . '"  data-field_mandate="' . $field_mandate . '"  id="field_' . $field_name . '"  name="field_name[' . $field_name . '][]"  data-ids="' . $field_ids . '"  data-items="' . $field_items . '" >';
                $html .= '<label class="custom-file-label" for="field_' . $field_name . '">Choose file</label>';
                $html .= '</div>';
                $html .= '</div>';
                if ($field_mandate == 'yes' || $field_verified == 'yes') {
                    $html .= '<div class="form-group col-12">';
                    if ($field_mandate == 'yes') {
                        $html .= '<label class="d-block">Signature required : ' . $field_mandate . '</label>';
                    }
                    if ( $field_verified == 'yes' ) {
                        $html .= '<label class="d-block">File needs to be verified : ' . $field_verified . '</label>';
                        $html .= '<label class="d-block">';

                        $field_verification_grade_text = '';
                        if ( $field_verification_grade == 'G') {
                            $field_verification_grade_text = 'Verification grade : Government';
                        } elseif ( $field_verification_grade == 'F' ) {
                            $field_verification_grade_text = 'Verification grade : Financial';
                        } elseif ( $field_verification_grade == 'T' ) {
                            $field_verification_grade_text = 'Verification grade : Telecom';
                        } elseif ( $field_verification_grade == 'A' ) {
                            $field_verification_grade_text = 'Verification grade : Akcess';
                        } elseif ( $field_verification_grade == 'O' ) {
                            $field_verification_grade_text = 'Verification grade : Other';
                        }
                        $html .= '</label>';
                        $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    }
                    $html .= '</div>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'password' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                    $html .= '<input type="password" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';
                $html .= '</div>';
                
                if ($field_verified == 'yes') { 
                    $html .= '<div class="form-group col-12"> ';
                    $html .= '<label class="d-block">File needs to be verified: ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';

                    $field_verification_grade_text = '';
                    if ( $field_verification_grade == 'G' ) {
                        $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ( $field_verification_grade == 'F' ) {
                        $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ( $field_verification_grade == 'T' ) {
                        $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ( $field_verification_grade == 'A' ) {
                        $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ( $field_verification_grade == 'O' ) {
                        $field_verification_grade_text = 'Verification grade: Other';
                    }

                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'radio' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';

                $explode = explode( ",", $field_items );

                foreach ( $explode as $key => $value ) {

                    $html .= '<div class="form-check form-check-radio">';
                    $html .= '<label class="form-check-label">';
                    $html .= '<input class="form-check-input" type="radio"  data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '"  data-type="' . $field_type . '"  data-isVisible="' . $field_isVisible . '"  data-section="' . $field_section . '"  data-verification-grade="' . $field_verification_grade . '"   data-fieldver="' . $field_verified . '"  data-field_mandate="' . $field_mandate . '"  id="field_' . $field_name . '"  name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $value . '"  placeholder="' . $field_label . '">';
                    $html .= $value;
                    $html .= '<span class="circle">';
                    $html .= '<span class="check"></span>';
                    $html .= '</span>';
                    $html .= '</label>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                
                if ($field_verified == 'yes') {
                    $html .= '<div class="form-group col-12"> ';
                    $html .= '<label class="d-block">File needs to be verified: ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';

                    $field_verification_grade_text = '';
                    if ($field_verification_grade == 'G') {
                            $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ($field_verification_grade == 'F') {
                            $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ($field_verification_grade == 'T') {
                            $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ($field_verification_grade == 'A') {
                            $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ($field_verification_grade == 'O') {
                            $field_verification_grade_text = 'Verification grade: Other';
                    }

                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                 }
                
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'checkbox' ) {

                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';

                $explode = explode( ",", $field_items );

                foreach ( $explode as $key => $value ) {
                    $html .= '<div class="form-check">';
                    $html .= '<label class="form-check-label">';
                    $html .= '<input class="form-check-input" type="checkbox"  data-instructions="' . $field_label_instructions . '"  data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $value . '" placeholder="' . $field_label . '" />';
                    $html .= $value;
                    $html .= '<span class="form-check-sign">';
                    $html .= '<span class="check"></span>';
                    $html .= '</span>';
                    $html .= '</label>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                
                if ($field_verified == 'yes') { 
                    $html .= '<div class="form-group col-12"> ';
                    $html .= '<label class="d-block">File needs to be verified: ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';

                    $field_verification_grade_text = '';
                    if ($field_verification_grade == 'G') {
                            $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ($field_verification_grade == 'F') {
                            $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ($field_verification_grade == 'T') {
                            $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ($field_verification_grade == 'A') {
                            $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ($field_verification_grade == 'O') {
                            $field_verification_grade_text = 'Verification grade: Other';
                    }

                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '</div>';

            }
            if ( $field_type == 'string' || $field_type == 'text' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<input type="text" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';
                $html .= '</div>';
                if ($field_verified == 'yes') {
                    $html .= '<div class="form-group col-12">';
                    $html .= '<label class="d-block">File needs to be verified: ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';

                    $field_verification_grade_text = '';
                    if (  $field_verification_grade == 'G' ) {
                        $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ( $field_verification_grade == 'F' ) {
                        $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ( $field_verification_grade == 'T' ) {
                        $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ( $field_verification_grade == 'A' ) {
                        $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ( $field_verification_grade == 'O' ) {
                        $field_verification_grade_text = 'Verification grade: Other';
                    }
                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
            if (  $field_type == 'address' || $field_type == 'textarea' ) {
           
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<textarea data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_name . '" col="3" class="form-control"></textarea>';
                $html .= '</div>';
                
                if ($field_verified == 'yes') { 
                    $html .= '<div class="form-group col-12">';
                    $html .= '<label class="d-block">File needs to be verified : ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';
                    $field_verification_grade_text = '';
                    if ($field_verification_grade == 'G') {
                        $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ($field_verification_grade == 'F') {
                        $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ($field_verification_grade == 'T') {
                        $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ($field_verification_grade == 'A') {
                        $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ($field_verification_grade == 'O') {
                        $field_verification_grade_text = 'Verification grade: Other';
                    }
                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                   }
                
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'list' || $field_type == 'select' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<select data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';

                $explode = explode( ",", $field_items );

                $html .= '<option value="">Select ' . $field_label . '</option>';

                foreach ( $explode as $key => $value ) {

                    $html .= '<option value="' . $value . '">' . $value . '</option>';
                }
                
                $html .= '</select>'; 
                $html .= '</div>';
                
                if ($field_verified == 'yes') {
                    $html .= '<div class="form-group col-12">';
                    $html .= '<label class="d-block">File needs to be verified: ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';

                    $field_verification_grade_text = '';
                    if ($field_verification_grade == 'G') {
                        $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ($field_verification_grade == 'F') {
                        $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ($field_verification_grade == 'T') {
                        $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ($field_verification_grade == 'A') {
                        $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ($field_verification_grade == 'O') {
                        $field_verification_grade_text = 'Verification grade: Other';
                    }
                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'phone' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<input type="tel" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';
                $html .= '</div>';
                
                if ($field_verified == 'yes') { 
                    $html .= '<div class="form-group col-12">';
                    $html .= '<label class="d-block">File needs to be verified: ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';

                    $field_verification_grade_text = '';
                    if ($field_verification_grade == 'G') {
                        $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ($field_verification_grade == 'F') {
                        $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ($field_verification_grade == 'T') {
                        $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ($field_verification_grade == 'A') {
                        $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ($field_verification_grade == 'O') {
                        $field_verification_grade_text = 'Verification grade: Other';
                    }

                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'number' ) {

                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<input type="number" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';
                $html .= '</div>';
                
                if ($field_verified == 'yes') {
                    $html .= '<div class="form-group col-12">';
                    $html .= '<label>File needs to be verified: ' . $field_verified . '</label>';

                    $field_verification_grade_text = '';
                    if ($field_verification_grade == 'G') {
                        $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ($field_verification_grade == 'F') {
                        $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ($field_verification_grade == 'T') {
                        $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ($field_verification_grade == 'A') {
                        $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ($field_verification_grade == 'O') {
                        $field_verification_grade_text = 'Verification grade: Other';
                    }

                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'date' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12 mb-0">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<input type="date" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . ' ( YYYY-MM-DD )" class="form-control">';
                $html .= '</div>';
                
                if ($field_verified == 'yes') {
                    $html .= '<div class="form-group col-12">';
                    $html .= '<label class="d-block">File needs to be verified: ' . $field_verified . '</label>';
                    $html .= '<label class="d-block">';
                    $field_verification_grade_text = '';
                    if ($field_verification_grade == 'G') {
                        $field_verification_grade_text = 'Verification grade: Government';
                    } elseif ($field_verification_grade == 'F') {
                        $field_verification_grade_text = 'Verification grade: Financial';
                    } elseif ($field_verification_grade == 'T') {
                        $field_verification_grade_text = 'Verification grade: Telecom';
                    } elseif ($field_verification_grade == 'A') {
                        $field_verification_grade_text = 'Verification grade: Akcess';
                    } elseif ($field_verification_grade == 'O') {
                        $field_verification_grade_text = 'Verification grade: Other';
                    }
                    $html .= '</label>';
                    $html .= '<label class="d-block">' . $field_verification_grade_text . '</label>';
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                $html .= '</div>';
            }
        }                                               
        
        $html .= '</div>';
        $html .= '</div>';
        
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;

        $this->loadModel('SendData');

        $type_doc = 'eform'; 

        $sendData = $this->SendData->newEntity();
        $sendData->fk_idcard_id = $id;
        $sendData->phone_no ="";
        $sendData->country_code = "";
        $sendData->send_type = "";
        $sendData->recievedType = $type_doc;
        $sendData->soft_delete = 1;     

        $savesendData = $this->SendData->save($sendData);

        $saveSendId = $savesendData->id;

        $origin_array = array(
            'authorization: ' . $token,
            'apikey: ' . $api,
            'origin: ' . $origin_url
        );

        $phoneHash = hash('sha256', $fullphone);

        $data_array = array(
            'id'    => $saveSendId,
            'component_id'    => $saveSendId,
            'component'    => $type_doc,
            'domain_name' => ORIGIN_URL,
            'send_by' => 'phone',
            'content' => '7984715974',
            'deeplink_type' => $type_doc,
            'company_name' => COMP_NAME
        );

        $method = "POST";

        $type_method = 'firebase/deeplinking';

        $response_data_deeplinking = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);

        $response_deeplinking_Data = json_decode($response_data_deeplinking);

        if($response_deeplinking_Data->status == 1) {

            $deeplink = $response_deeplinking_Data->data->shortLink;
            $doc_id = isset($response_deeplinking_Data->data->doc_id) ? $response_deeplinking_Data->data->doc_id : "";

            $send_status = "success";

            $this->loadModel('SendData');

            $docid = $this->Global->userIdEncode($doc_id); 

            $this->SendData->updateAll(
                [
                    'send_status'        => $send_status,
                    'soft_delete'        => 0,
                    'document_id'        => $docid 
                ], 
                [
                    'id' => $saveSendId
                ]
            );

            $query_response = "SELECT formName FROM eform WHERE id='" . $id . "'";

            $query_data = $conn->execute($query_response);

            $query_data = $query_data->fetch('assoc');

            $eformName = $query_data['formName'];

            $dataArray = array(
                "portal" => ORIGIN_URL,
                "deeplink_type" => $type_doc,
                "id" => $doc_id
            );
    
            $qrdata = json_encode($dataArray);

            $after = array(
                'user_id' => $user_id,
                'role_id' => $role_id,       
                'fk_user_id' => $fk_users_id,
                'fk_idcard_id' => $idcard->id,
                'phone_no' => "",
                'country_code' => "",
                'send_type' => "",
                'send_status' => $send_status,
                'recievedType' => $type_doc,
                'soft_delete' => 0
            );

            $this->Global->auditTrailApi($saveSendId, 'senddata', 'eform-view', null, $after);

        }


        $data['qrcode_url'] = $deeplink;
        $data['qrdata'] = $qrdata;

        $data['html'] = $html;
        $data['title'] = $eform->formName;
        
        
        $resultJ = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }
    
    /**
     * Edit method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($idEncode = null) {
        
        $conn = ConnectionManager::get("default"); // name of your database connection       
        
        $id = $this->Global->userIdDecode($idEncode);
       
        $eform = $this->Eform->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $this->loadModel('Users');
        
        $users = $this->Users->find('all', array('conditions' => ['usertype' => 'Admin', 'status' => 1]));
        //for eform process
        $users_process = $this->Users->find('all', array('conditions' => array(
            array('usertype LIKE' => 'Admin%','status'=>1),
       )));
        //'conditions' => ['usertype like' => 'Admin%', 'status' => 1]));
        
        $this->loadModel('Countries');
        
        $countries = $this->Countries->find('all');
        
        $this->loadModel('Fields');
        
        $fields = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));
        
        $this->loadModel('Additional');
        
        $additional = $this->Additional->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));
        
        $dataAdditional = array();
        
        foreach($additional as $key => $additionals) {
            
            $dataAdditional[] = $additionals->akcessId;
            
        }
       
        $eid = $this->Global->userIdEncode($id);
             
        if ($this->request->is(['patch', 'post', 'put'])) {

            $idcard = $this->Eform->patchEntity($idcard, $this->request->getData());

            if ($this->Eform->save($idcard)) {
                $this->Flash->success(__('The eform has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The eform could not be saved. Please, try again.'));
        }
        
        $this->set('page_title', 'Edit Eform');

        $this->set('page_icon', '<i class="fab fa-wpforms mr-1"></i>');
        
        $this->set(compact('eform', 'id', 'eid', 'fields', 'countries', 'users', 'dataAdditional','users_process'));
    }

    public function copy($idEncode = null) {
        
        $conn = ConnectionManager::get("default"); // name of your database connection             
        
        $copyid = $this->Global->userIdDecode($idEncode);

        $get_query_data_eform = $conn->execute('SELECT * FROM eform where id="'.$copyid.'"');

        $query_eform_data = $get_query_data_eform->fetch('assoc');

        $user_id = $this->Auth->user( 'id' );
        
        $role_id = $this->Auth->user( 'usertype' );
                        
        $formName = $query_eform_data['formName'];
        
        $description = $query_eform_data['description'];
        
        $instruction = $query_eform_data['instruction'];
        
        $user_id = $user_id;
        
        $logo = 'logo.png';
        
        $date = date('Y-m-d H:i:s');
        
        $signature = $query_eform_data['signature'];        
        
        $facematch = $query_eform_data['facematch'];
        
        $pulldata = $query_eform_data['pulldata'];
        
        $publish = $query_eform_data['publish'];   

        $storeinprofile = $query_eform_data['storeinprofile'];
        
        $isclientInvitationEform = $query_eform_data['isclientInvitationEform'];

        $send_invitation_eform = $query_eform_data['send_invitation_eform']; 
        
        $eformid = $this->Global->random_string('alnum', 24);
        
        $isAdditionalNotification = $query_eform_data['isAdditionalNotification'];  

        $additionalNotificationTo = $query_eform_data['additionalNotificationTo'];  

        $sql_eform = "INSERT INTO `eform` (`eformid`, `formName`, `description`, `instruction`, `UserId`, `logo`, `date`, `signature`, `facematch`, `pulldata`, `publish`, `isAdditionalNotification`, `additionalNotificationTo`, `storeinprofile`, `isclientInvitationEform`, `send_invitation_eform`) VALUES ('".$eformid."','".$formName."','".$description."','".$instruction."','".$user_id."','".$logo."','".$date."','".$signature."','".$facematch."','".$pulldata."','".$publish."','".$isAdditionalNotification."','".$additionalNotificationTo."','".$storeinprofile."','".$isclientInvitationEform."','".$send_invitation_eform."')";
            
        $saveEforms = $conn->execute($sql_eform);  
        
        $sql_last_query_eforms = "SELECT id FROM `eform` ORDER BY ID DESC LIMIT 0,1";

        $sql_last_query_eform_id = $conn->execute($sql_last_query_eforms);

        $sql_last_eform_id = $sql_last_query_eform_id->fetch('assoc');

        $id = $sql_last_eform_id['id'];

        $eform_after = [
            'user_id'                   => $user_id,
            'role_id'                   => $role_id,
            'fk_user_id'                => $user_id,     
            'formName'                  => $formName,
            'description'               => $description,
            'instruction'               => $instruction,
            'UserId'                    => $user_id,
            'logo'                      => $logo,
            'date'                      => $date,
            'signature'                 => $signature,
            'facematch'                 => $facematch,
            'pulldata'                  => $pulldata,
            'publish'                   => $publish,
            'isAdditionalNotification'  => $isAdditionalNotification,
            'additionalNotificationTo'  => $additionalNotificationTo,
            'storeinprofile'            => $storeinprofile,
            'isclientInvitationEform'   => $isclientInvitationEform,
            'send_invitation_eform'     => $send_invitation_eform
        ];
        
        $eform_before = [];        
        
        $lastInsertedEformId = $this->Global->auditTrailApi($id, 'eform', 'copy', $eform_before, $eform_after);
        
        if($isAdditionalNotification == 'yes') {

            $query_data_additional = $conn->execute('SELECT * FROM additionalnotificationto where fk_eform_id="'.$copyid.'"');

            $query_get_additional_notification = $query_data_additional->fetchAll('assoc');
            
            if($query_get_additional_notification) {
            
                foreach($query_get_additional_notification as $query_get_additional_notifications) {

                    $akcessId = $query_get_additional_notifications['akcessId'];

                    $query_data = $conn->execute('SELECT * FROM users where akcessId="'.$akcessId.'"');

                    $query_users = $query_data->fetch('assoc');

                    $email = $query_users['email'];

                    $mobile = $query_users['mobileNumber'];

                    $sql_additionalnotificationto = "INSERT INTO `additionalnotificationto` (`fk_eform_id`, `email`, `mobile`, `akcessId`) VALUES ('".$id."','".$email."','".$mobile."','".$akcessId."')";

                    $sql_last_query_additionalnotificationto = $conn->execute($sql_additionalnotificationto);

                    $sql_last_query = "SELECT id FROM `additionalnotificationto` ORDER BY ID DESC LIMIT 0,1";

                    $sql_last_query_id = $conn->execute($sql_last_query);

                    $sql_last_id = $sql_last_query_id->fetch('assoc');

                    $lastInsertedId = $sql_last_id['id'];
                }
            }
        } 

        $query_data_Fields = $conn->execute('SELECT * FROM fields where fk_eform_id="'.$copyid.'"');

        $query_get_Fields = $query_data_Fields->fetchAll('assoc');
        
        $fieldsData = array();

        if($query_get_Fields) {
        
            foreach($query_get_Fields as $query_get_Fields) {
                $fieldsData[] = array(
                    'fk_eform_id' => $query_get_Fields['fk_eform_id'],
                    'labelname' => $query_get_Fields['labelname'],
                    'keyfields' => $query_get_Fields['keyfields'],
                    'keytype' => $query_get_Fields['keytype'],
                    'signature_required' => $query_get_Fields['signature_required'],
                    'file_verified' => $query_get_Fields['file_verified'],
                    'options' => $query_get_Fields['options'],
                    'verification_grade' => $query_get_Fields['verification_grade'],
                    'section_id' => $query_get_Fields['section_id'],
                    'section_color' => $query_get_Fields['section_color'],
                    'sectionfields' => $query_get_Fields['sectionfields'],
                    'instructions' => $query_get_Fields['instructions'],
                    'is_mandatory' => $query_get_Fields['is_mandatory'],
                    'status' => $query_get_Fields['status'],
                    'soft_delete' => $query_get_Fields['soft_delete'],
                );
            }

        }

        $fieldsName = $fieldsData;
               
        $fields_array_after = array(
            'user_id'  => $user_id,
            'role_id'  => $role_id,
            'fk_user_id' => $user_id,
        );
        
        $fields_options_array_after = array(
            'user_id'  => $user_id,
            'role_id'  => $role_id,
            'fk_user_id' => $user_id,
        );
        
        foreach($fieldsName as $key => $fieldsNames){
            $fk_eform_id = isset($fieldsNames['fk_eform_id']) ? $fieldsNames['fk_eform_id'] : ''; 
            $labelname = isset($fieldsNames['labelname']) ? $fieldsNames['labelname'] : ''; 
            $keyfields = isset($fieldsNames['keyfields']) ? $fieldsNames['keyfields'] : ''; 
            $keytype = isset($fieldsNames['keytype']) ? $fieldsNames['keytype'] : ''; 
            $signature_required = isset($fieldsNames['signature_required']) ? $fieldsNames['signature_required'] : ''; 
            $file_verified = isset($fieldsNames['file_verified']) ? $fieldsNames['file_verified'] : ''; 
            $options = isset($fieldsNames['options']) ? $fieldsNames['options'] : ''; 
            $verification_grade = isset($fieldsNames['verification_grade']) ? $fieldsNames['verification_grade'] : ''; 
            $section_id = isset($fieldsNames['section_id']) ? $fieldsNames['section_id'] : ''; 
            $section_color = isset($fieldsNames['section_color']) ? $fieldsNames['section_color'] : ''; 
            $sectionfields = isset($fieldsNames['sectionfields']) ? $fieldsNames['sectionfields'] : ''; 
            $instructions = isset($fieldsNames['instructions']) ? $fieldsNames['instructions'] : ''; 
            $is_mandatory = isset($fieldsNames['is_mandatory']) ? $fieldsNames['is_mandatory'] : ''; 
            $status = isset($fieldsNames['status']) ? $fieldsNames['status'] : ''; 
            $soft_delete = isset($fieldsNames['soft_delete']) ? $fieldsNames['soft_delete'] : ''; 
                        
            $fields_array_after[] = array(
                'fk_eform_id' => $id,
                'labelname' => $labelname,
                'keyfields' => $keyfields,
                'keytype' => $keytype,
                'signature_required' => $signature_required,
                'file_verified' => $file_verified,
                'options' => $options,
                'verification_grade' => $verification_grade,
                'section_id' => $section_id,
                'section_color' => $section_color,
                'sectionfields' => $sectionfields,
                'instructions' => $instructions,
                'is_mandatory' => $is_mandatory,
                'status' => $status,
                'soft_delete' => $soft_delete
            );
            
            $sql = "INSERT INTO `fields` (`fk_eform_id`, `labelname`, `keyfields`, `keytype`, `signature_required`, `file_verified`, `options`, `verification_grade`, `section_id`, `section_color`, `sectionfields`, `instructions`, `is_mandatory`, `status`, `soft_delete`) VALUES ('".$id."','".$labelname."','".$keyfields."','".$keytype."','".$signature_required."','".$file_verified."','".$options."','".$verification_grade."','".$section_id."','".$section_color."','".$sectionfields."','".$instructions."','".$is_mandatory."','".$status."','".$soft_delete."')";
            
            $saveFields = $conn->execute($sql);  
            
            $sql_last_query_fields = "SELECT id FROM `fields` ORDER BY ID DESC LIMIT 0,1";

            $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

            $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

            $lastfieldsInsertedId = $sql_last_fields_id['id'];
            
            $items_array = explode(",", $options);
            
            foreach($items_array as $items_arrays) {
                if($items_arrays) {                      
                    $optionsid = $this->Global->random_string('alnum', 12);
                    
                    $sql_options = "INSERT INTO `fields_options` (`fk_eform_id`, `fk_fields_id`, `keyfields`, `lable`, `uid`, `checked`) VALUES (".$id.",'".$lastfieldsInsertedId."','".strtolower($items_arrays)."','".$items_arrays."','".$optionsid."','false')";
            
                    $conn->execute($sql_options);       
                    
                    $fields_options_array_after[] = array(
                        'fk_eform_id' => $fk_eform_id,
                        'fk_fields_id' => $lastfieldsInsertedId,
                        'keyfields' => strtolower($items_arrays),
                        'lable' => $items_arrays,
                        'uid' => $optionsid,
                        'checked' => 'false'
                    );
                    
                }
            }            
            
        }
        
        $lastInsertedfieldsId = $this->Global->auditTrailApi($id, 'fields', 'copy', $fields_array_before, $fields_array_after);        
        $this->Global->auditTrailApiSuccess($lastInsertedfieldsId, 1);        
       
        $lastInsertedfieldsOptionsId = $this->Global->auditTrailApi($id, 'fields_options', 'copy', $fields_options_array_before, $fields_options_array_after);
        
        $this->Global->auditTrailApiSuccess($lastInsertedfieldsOptionsId, 1);

        if ($id) {
            $this->Flash->success(__('The ' . $formName . ' eform has been copied successfully.'));
        } else {
            $this->Flash->error(__('The ' . $formName . ' eform could not be copied. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Eform', 'action' => 'index']);        
        
    }
    
    public function submitEform() {
        
        $conn = ConnectionManager::get("default"); // name of your database connection             
        
        $id = $this->Global->userIdDecode($_GET['eid']);
        
        $user_id = $this->Auth->user( 'id' );
        
        $role_id = $this->Auth->user( 'usertype' );
                        
        $formName = $this->request->data('formName');
        
        $description = $this->request->data('description');
        
        $instruction = $this->request->data('instruction');
        
        $user_id = $user_id;
        
        $logo = 'logo.png';
        
        $date = date('Y-m-d H:i:s');
        
        $signature = $this->request->data('signature');        
        
        $facematch = $this->request->data('facematch');
        
        $pulldata = $this->request->data('pulldata');
        
        $publish = $this->request->data('publish');   
        
        $isAdditionalNotification = $this->request->data('additional_notification');  
        
        $additionalNotificationTo = 0;
        
        if($isAdditionalNotification == 'yes') {
            
            $akcessID = $this->request->data('akcessID');  
            
            if($akcessID) {
            
                foreach($akcessID as $akcessIDs) {

                    $akcessId = $akcessIDs;

                    $query_data = $conn->execute('SELECT * FROM users where akcessId="'.$akcessId.'"');

                    $query_users = $query_data->fetch('assoc');

                    $email = $query_users['email'];

                    $mobile = $query_users['mobileNumber'];

                    $sql_additionalnotificationto = "INSERT INTO `additionalnotificationto` (`fk_eform_id`, `email`, `mobile`, `akcessId`) VALUES ('".$id."','".$email."','".$mobile."','".$akcessId."')";

                    $sql_last_query_additionalnotificationto = $conn->execute($sql_additionalnotificationto);

                    $sql_last_query = "SELECT id FROM `additionalnotificationto` ORDER BY ID DESC LIMIT 0,1";

                    $sql_last_query_id = $conn->execute($sql_last_query);

                    $sql_last_id = $sql_last_query_id->fetch('assoc');

                    $lastInsertedId = $sql_last_id['id'];
                }
            }
        }
        
        $storeinprofile = $this->request->data('storeinprofile');
        
        $isclientInvitationEform = $this->request->data('isclientInvitationEform');

        $send_invitation_eform = $this->request->data('send_invitation_eform'); 
        
        $this->loadModel('Eform');    
        
        $eformid = $this->Global->random_string('alnum', 24);
        
        $eform_after = [
            'user_id'                   => $user_id,
            'role_id'                   => $role_id,
            'fk_user_id'                => $user_id,     
            'formName'                  => $formName,
            'description'               => $description,
            'instruction'               => $instruction,
            'UserId'                    => $user_id,
            'logo'                      => $logo,
            'date'                      => $date,
            'signature'                 => $signature,
            'facematch'                 => $facematch,
            'pulldata'                  => $pulldata,
            'publish'                   => $publish,
            'isAdditionalNotification'  => $isAdditionalNotification,
            'additionalNotificationTo'  => $additionalNotificationTo,
            'storeinprofile'            => $storeinprofile,
            'isclientInvitationEform'   => $isclientInvitationEform,
            'send_invitation_eform'   => $send_invitation_eform
        ];
        
        $eformDetails_before = $this->Eform->get($id, array('conditions' => ['soft_delete' => 0, 'id' => $id]));
        
        $eform_before = [
            'formName'                  => $eformDetails_before->formName,
            'description'               => $eformDetails_before->description,
            'instruction'               => $eformDetails_before->instruction,
            'UserId'                    => $eformDetails_before->UserId,
            'logo'                      => $eformDetails_before->logo,
            'date'                      => $eformDetails_before->date,
            'signature'                 => $eformDetails_before->signature,
            'facematch'                 => $eformDetails_before->facematch,
            'pulldata'                  => $eformDetails_before->pulldata,
            'publish'                   => $eformDetails_before->publish,
            'isAdditionalNotification'  => $eformDetails_before->isAdditionalNotification,
            'additionalNotificationTo'  => $eformDetails_before->additionalNotificationTo,
            'storeinprofile'            => $eformDetails_before->storeinprofile,
            'isclientInvitationEform'   => $eformDetails_before->isclientInvitationEform,
            'send_invitation_eform'     => $eformDetails_before->send_invitation_eform
        ];        
        
        $lastInsertedEformId = $this->Global->auditTrailApi($id, 'eform', 'update', $eform_before, $eform_after);
        
        unset($eform_after['user_id']);
        unset($eform_after['role_id']);
        unset($eform_after['fk_user_id']);
        
        $updateIDCard = $this->Eform->updateAll(
            $eform_after, 
            [
                'id' => $id
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedEformId, 1);

        $fieldsName = $_POST['field_name'];
        
        $this->loadModel('Fields');        
        
        $fieldsDetails_before = $this->Fields->find('all', array('conditions' => ['soft_delete' => 0, 'fk_eform_id' => $id]));
        
        $fields_array_before = array();
        
        if($fieldsName) {
        
            foreach($fieldsName as $key => $fieldsNames){

                $fk_eform_id = isset($fieldsNames->fk_eform_id) ? $fieldsNames->fk_eform_id : ''; 
                $labelname = isset($fieldsNames->labelname) ? $fieldsNames->labelname : ''; 
                $key = isset($fieldsNames->keyfields) ? $fieldsNames->keyfields : ''; 
                $keytype = isset($fieldsNames->keytype) ? $fieldsNames->keytype : ''; 
                $file_verified = isset($fieldsNames->file_verified) ? $fieldsNames->file_verified : ''; 
                $options = isset($fieldsNames->options) ? $fieldsNames->options : ''; 
                $verification_grade = isset($fieldsNames->verification_grade) ? $fieldsNames->verification_grade : ''; 
                $section_id = isset($fieldsNames->section_id) ? $fieldsNames->section_id : ''; 
                $instructions = isset($fieldsNames->instructions) ? $fieldsNames->instructions : ''; 
                $is_mandatory = isset($fieldsNames->is_mandatory) ? $fieldsNames->is_mandatory : '';    
                $signature_required = isset($fieldsNames->signature_required) ? $fieldsNames->signature_required : '';    

                $fields_array_before[] = array(
                    'fk_eform_id' => $fk_eform_id,
                    'labelname' => $labelname,
                    'keyfields' => $key,
                    'keytype' => $keytype,
                    'file_verified' => $file_verified,
                    'options' => $options,
                    'verification_grade' => $verification_grade,
                    'section_id' => $section_id,
                    'instructions' => $instructions,
                    'is_mandatory' => $is_mandatory,
                    'signature_required' => $signature_required
                );

            }
        }
                        
        $this->loadModel('FieldsOptions');        
        
        $fieldsOptionsDetails_before = $this->FieldsOptions->find('all', array('conditions' => ['soft_delete' => 0, 'fk_eform_id' => $id]));
        
        $fields_options_array_before = array();
        
        if($fieldsOptionsDetails_before) {
        
            foreach($fieldsOptionsDetails_before as $key => $fieldsOptionsDetails_befores){      

                $fk_eform_id = isset($fieldsOptionsDetails_befores->fk_eform_id) ? $fieldsOptionsDetails_befores->fk_eform_id : ''; 
                $fk_fields_id = isset($fieldsOptionsDetails_befores->fk_fields_id) ? $fieldsOptionsDetails_befores->fk_fields_id : ''; 
                $key = isset($fieldsOptionsDetails_befores->keyfields) ? $fieldsOptionsDetails_befores->keyfields : ''; 
                $lable = isset($fieldsOptionsDetails_befores->lable) ? $fieldsOptionsDetails_befores->lable : ''; 
                $uid = isset($fieldsOptionsDetails_befores->uid) ? $fieldsOptionsDetails_befores->uid : ''; 
                $checked = isset($fieldsOptionsDetails_befores->checked) ? $fieldsOptionsDetails_befores->checked : ''; 

                $fields_options_array_before[] = array(
                    'fk_eform_id' => $fk_eform_id,
                    'fk_fields_id' => $fk_fields_id,
                    'keyfields' => $key,
                    'lable' => $lable,
                    'uid' => $uid,
                    'checked' => $checked
                );

            }
        }
        
        $fieldsOptions_before = $fieldsOptionsDetails_before;
        
        $this->Fields->deleteAll(array('fk_eform_id' => $id));
            
        $this->FieldsOptions->deleteAll(array('fk_eform_id' => $id));
        
        $eformFields = array();
        
        $eformFieldText = '';
        
        $fields_array_after = array(
            'user_id'  => $user_id,
            'role_id'  => $role_id,
            'fk_user_id' => $user_id,
        );
        
        $fields_options_array_after = array(
            'user_id'  => $user_id,
            'role_id'  => $role_id,
            'fk_user_id' => $user_id,
        );
        
        foreach($fieldsName as $key => $fieldsNames){
            $name = isset($fieldsNames['name']) ? $fieldsNames['name'] : ''; 
            $instructions = isset($fieldsNames['instructions']) ? $fieldsNames['instructions'] : ''; 
            $type = isset($fieldsNames['type']) ? $fieldsNames['type'] : ''; 
            $isVisible = isset($fieldsNames['isVisible']) ? $fieldsNames['isVisible'] : ''; 
            $section = isset($fieldsNames['section']) ? $fieldsNames['section'] : ''; 
            $verification_grade = isset($fieldsNames['verification_grade']) ? $fieldsNames['verification_grade'] : ''; 
            $fieldver = isset($fieldsNames['fieldver']) ? $fieldsNames['fieldver'] : ''; 
            $field_mandate = isset($fieldsNames['field_mandate']) ? $fieldsNames['field_mandate'] : ''; 
            $signature_required = isset($fieldsNames['signature_required']) ? $fieldsNames['signature_required'] : ''; 
            $ids = isset($fieldsNames['ids']) ? $fieldsNames['ids'] : ''; 
            $items = isset($fieldsNames['items']) ? $fieldsNames['items'] : ''; 
            $key = isset($fieldsNames['keyfields']) ? $fieldsNames['keyfields'] : ''; 
            
            $key_value = $key;
            if (strpos($key, '_') !== false) { 
                $key_value_explode = explode("_", $key);                
                $key_value = $key_value_explode[0];
            }
            
            $fields_array_after[] = array(
                'fk_eform_id' => $id,
                'labelname' => $name,
                'keyfields' => $key_value,
                'keytype' => $type,
                'file_verified' => $fieldver,
                'options' => $items,
                'verification_grade' => $verification_grade,
                'section_id' => $section,
                'instructions' => $instructions,
                'is_mandatory' => $field_mandate,
                'signature_required' => $signature_required
            );
            
            $sql = "INSERT INTO `fields` (`fk_eform_id`, `labelname`, `keyfields`, `keytype`, `file_verified`, `options`, `verification_grade`, `section_id`, `instructions`, `is_mandatory`, `signature_required`) VALUES (".$id.",'".$name."','".$key_value."','".$type."','".$fieldver."','".$items."','".$verification_grade."','".$section."','".$instructions."','".$field_mandate."','".$signature_required."')";
            
            $saveFields = $conn->execute($sql);  
            
            $sql_last_query_fields = "SELECT id FROM `fields` ORDER BY ID DESC LIMIT 0,1";

            $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

            $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

            $lastfieldsInsertedId = $sql_last_fields_id['id'];
            
            $items_array = explode(",", $items);
            
            foreach($items_array as $items_arrays) {
                if($items_arrays) {                      
                    $optionsid = $this->Global->random_string('alnum', 12);
                    
                    $sql_options = "INSERT INTO `fields_options` (`fk_eform_id`, `fk_fields_id`, `keyfields`, `lable`, `uid`, `checked`) VALUES (".$id.",".$lastfieldsInsertedId.",'".strtolower($items_arrays)."','".$items_arrays."','".$optionsid."','false')";
            
                    $conn->execute($sql_options);       
                    
                    $fields_options_array_after[] = array(
                        'fk_eform_id' => $fk_eform_id,
                        'fk_fields_id' => $lastfieldsInsertedId,
                        'keyfields' => strtolower($items_arrays),
                        'lable' => $items_arrays,
                        'uid' => $optionsid,
                        'checked' => 'false'
                    );
                    
                }
            }            
            
        }
        
        $lastInsertedfieldsId = $this->Global->auditTrailApi($id, 'fields', 'update', $fields_array_before, $fields_array_after);        
        $this->Global->auditTrailApiSuccess($lastInsertedfieldsId, 1);        
       
        $lastInsertedfieldsOptionsId = $this->Global->auditTrailApi($id, 'fields_options', 'update', $fields_options_array_before, $fields_options_array_after);
        
        $this->Global->auditTrailApiSuccess($lastInsertedfieldsOptionsId, 1);
        
        if ($saveFields) {
            $data['message'] = "success";
            $data['data'] = "The eform fields has been saved.";
             $this->request->session()->write('eform_message', 'The eform fields has been successfully saved.');
        } else {
            $data['message'] = "error";
            $data['data'] = "The eform fields could not be saved. Please, try again.";
        }
        
        $resultJ = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }
    
    public function SendEformData() {        
        
        $conn = ConnectionManager::get("default"); // name of your database connection      
                
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        $country_code = isset($_POST['country_code']) ? $_POST['country_code'] : '';
        $emailArray = isset($_POST['email']) ? $_POST['email'] : '';
        $ackess = isset($_POST['ackess']) ? $_POST['ackess'] : '';
        $field_phone = isset($_POST['field']) ? $_POST['field'] : '';      
        $eid = isset($_POST['eid']) ? $this->Global->userIdDecode($_POST['eid']) : '';      
        $inlineRadioOptions = isset($_POST['inlineRadioOptions']) ? $_POST['inlineRadioOptions'] : '';
        $type_doc = 'eform'; 
                
        $id = $eid;
                
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
                
        $type = '';
        if($inlineRadioOptions == 'phone') {
            $type = 'send-sms';
        } else if($inlineRadioOptions == 'email') {
            $type = 'send-email';
        } else if($inlineRadioOptions == 'ackess') {
            $type = 'ackess';
        }
        
        $dir  = WWW_ROOT . "/img/logo.png";
        // A few settings
        $img_file = $dir;
        // Read image path, convert to base64 encoding
        $imgData = base64_encode(file_get_contents($img_file));

        // Format the image SRC:  data:{mime};base64,{data};
        $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;
        
        $eform = $this->Eform->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
                        
        $formName = $eform->formName;
        
        $description = $eform->description;
        
        $instruction = $eform->instruction;
        
        $user_id = $user_id;
        
        $date = date('Y-m-d H:i:s');
        
        $signature = $eform->signature;
        
        $facematch = $eform->facematch;
        
        $pulldata = $eform->pulldata;
        
        $publish = $eform->publish;
        
        $additional_notification = $eform->isAdditionalNotification;
        
        $isAdditionalNotificationDetails = "";

        if($additional_notification == 'yes') {

            $sql_query = "SELECT akcessId,email,mobile FROM `additionalnotificationto` WHERE fk_eform_id='".$id."'";

            $sql_query_data = $conn->execute($sql_query);

            $result_sql = $sql_query_data->fetchAll('assoc');
           
            if($result_sql) {

                foreach($result_sql as $result_sqls) {

                    $akcessIds = $result_sqls['akcessId'];
                    $emails = $result_sqls['email'];
                    $mobiles = $result_sqls['mobile'];

                    $isAdditionalNotificationDetails .= '{"akcessId":"'.$akcessIds.'", "email":"'.$emails.'", "mobile":"'.$mobiles.'"},';

                }

            }

        }

        $isAdditionalNotificationDetails = rtrim($isAdditionalNotificationDetails, ',');

        $array_akcesss_id = "";
        
        $array_akcesss_id .= "[";
        
        foreach($ackess as $akcessIds) {
             
            $akcessId = $akcessIds;

            $array_akcesss_id .= '"'.$akcessId.'",';

        }

        $array_akcesss_id = rtrim($array_akcesss_id, ',');

        $array_akcesss_id .= "]";
                
        $storeinprofile = $eform->storeinprofile;
        
        $isclientInvitationEform = $eform->isclientInvitationEform;   
        
        $this->loadModel('Fields');        
        
        $fieldsInformation_query = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));        
        $eformFields = array();
        
        $eformFieldText = '';
        
        foreach($fieldsInformation_query as $key => $fieldsInformations){
         
            $field_id = isset($fieldsInformations->id) ? $fieldsInformations->id : ''; 
            $name = isset($fieldsInformations->labelname) ? $fieldsInformations->labelname : ''; 
            $instructions = isset($fieldsInformations->instructions) ? $fieldsInformations->instructions : ''; 
            $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
            $isVisible = isset($fieldsInformations->isVisible) ? $fieldsInformations->isVisible : ''; 
            $section_id = isset($fieldsInformations->section_id) ? $fieldsInformations->section_id : ''; 
            $section_color = isset($fieldsInformations->section_color) ? $fieldsInformations->section_color : ''; 
            $sectionfields = isset($fieldsInformations->sectionfields) ? $fieldsInformations->sectionfields : ''; 
            $verification_grade = isset($fieldsInformations->verification_grade) ? $fieldsInformations->verification_grade : ''; 
            $fieldver = isset($fieldsInformations->file_verified) ? $fieldsInformations->file_verified : ''; 
            $field_mandate = isset($fieldsInformations->is_mandatory) ? $fieldsInformations->is_mandatory : ''; 
            $signature_required = isset($fieldsInformations->signature_required) && $fieldsInformations->signature_required != "" ? $fieldsInformations->signature_required : 'no'; 
            $is_mandatory = isset($fieldsInformations->signatureis_mandatory_required) && $fieldsInformations->is_mandatory != "" ? $fieldsInformations->is_mandatory : 'no'; 
            $ids = isset($fieldsInformations->ids) ? $fieldsInformations->ids : ''; 
            $items = isset($fieldsInformations->options) ? $fieldsInformations->options : ''; 
            $key = isset($fieldsInformations->keyfields) ? $fieldsInformations->keyfields : ''; 
            $typeIn = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
            
            $key_value = $key;
            if (strpos($key, '_') !== false) { 
                $key_value_explode = explode("_", $key);                
                $key_value = $key_value_explode[0];
            }
           
            $this->loadModel('FieldsOptions');        
        
            $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
            ]);
            
            $array_items = '';
            
            $array_items .= "[";

            foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                
                $optionsid = $fieldsOptionsInformations->uid;
                $checked = $fieldsOptionsInformations->checked;
                $lable = $fieldsOptionsInformations->lable;
                $key = $fieldsOptionsInformations->keyfields;

                $array_items .= '{
                    "checked": '.$checked.',
                    "keyfields": "'.strtolower($key).'",
                    "lable": "'.$lable.'",
                    "uid": "'.$optionsid.'"
                },';
            }
            $array_items = rtrim($array_items, ',');
            
            $array_items .= "]";
            
            $eformFieldText .= '{
                "options": '.$array_items.',
                "labelname": "'.$name.'",
                "key": "'.$key_value.'",
                "keytype": "'.$typeIn.'",
                "signature_required": "'.$signature_required.'",
                "file_verified": "'.$fieldver.'",
                "verification_grade": "'.$verification_grade.'",
                "instructions": "'.$instructions.'",
                "is_mandatory": "'.$is_mandatory.'"
            },';
            
        }
                
        $eformFieldText = rtrim($eformFieldText, ',');
               
        $response_token = $this->Global->getToken();
           
        $token = $response_token;  
        
        $description_title = "Eform";

        
        if(isset($type) && $inlineRadioOptions == 'phone') {
           
            $response_token = $this->Global->getToken();
           
            $token = $response_token;

            if($field_phone) {
                
                foreach($field_phone as $key => $value) {
                    
                    $phone = $value['phone'];
                    $country_code = $value['country_code'];
                    
                    $fullphone = "+" . $country_code . $phone;
                    
                    $this->loadModel('SendData');

                    $sendData = $this->SendData->newEntity();
                    $sendData->fk_idcard_id = $id;
                    $sendData->phone_no = $phone;
                    $sendData->country_code = $country_code;
                    $sendData->send_type = $inlineRadioOptions;
                    $sendData->recievedType = $type_doc;
                    $sendData->soft_delete = 1;     

                    $savesendData = $this->SendData->save($sendData);

                    $saveSendId = $savesendData->id;

                    $origin_array = array(
                        'authorization: ' . $token,
                        'apikey: ' . $api,
                        'origin: ' . $origin_url
                    );

                    $phoneHash = hash('sha256', $fullphone);

                    $data_array = array(
                        'id'    => $saveSendId,
                        'component_id'    => $saveSendId,
                        'component'    => $type_doc,
                        'domain_name' => ORIGIN_URL,
                        'send_by' => 'phone',
                        'content' => $phone,
                        'deeplink_type' => $type_doc,
                        'company_name' => COMP_NAME
                    );

                    $method = "POST";

                    $type_method = 'firebase/deeplinking';

                    $response_data_deeplinking = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);

                    $response_deeplinking_Data = json_decode($response_data_deeplinking);

                    if($response_deeplinking_Data->status == 1) {

                        $deeplink = $response_deeplinking_Data->data->shortLink;
                        $doc_id = isset($response_deeplinking_Data->data->doc_id) ? $response_deeplinking_Data->data->doc_id : "";

                        $data_array = array(
                            'countryCode'    => $country_code,
                            'phone'    => $phone,
                            'msg'    => "Hi, Please check your Eform for details and download. " . $deeplink,
                            'recievedType' => $type_doc
                        );
                        
                        $response_data = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);
                        $response_phone_Data = json_decode($response_data);

                        $send_status = "error";
                        
                        if($response_phone_Data->status == 1) {

                            $send_status = "success";

                            $this->loadModel('SendData');

                            $docid = $this->Global->userIdEncode($doc_id);

                            $this->SendData->updateAll(
                                [
                                    'send_status'        => $send_status,
                                    'soft_delete'        => 0,
                                    'document_id'        => $docid 
                                ], 
                                [
                                    'id' => $saveSendId
                                ]
                            );

                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fk_idcard_id' => $idcard->id,
                                'phone_no' => $phone,
                                'country_code' => $country_code,
                                'send_type' => $inlineRadioOptions,
                                'send_status' => $send_status,
                                'recievedType' => $type_doc,
                                'soft_delete' => 0
                            );

                            $this->Global->auditTrailApi($saveSendId, 'senddata', 'phoneEform', null, $after);

                            $data['message'] = "success";
                            $data['data'] = "The Eform has been sent successfully.";
                        } else {
                            $data['data'] = "type is not set!";
                            $data['message'] = "error";
                        }
                    } else {
                        $data['message'] = "error";
                        $data['data'] = "Link generation issue!";
                    }
                }
            } else {
                $data['message'] = "error";
                $data['data'] = "Link generation issue!";
            }
        } 
        else if(isset($type) && $inlineRadioOptions == 'email') {
           
            if($emailArray) {
                
                $emaillist = explode(",", $emailArray);
             
                foreach($emaillist as $key => $value) {
                    
                    $email = $value;
            
                    $response_token = $this->Global->getToken();

                    $token = $response_token;

                    $this->loadModel('SendData');

                    $sendData = $this->SendData->newEntity();
                    $sendData->fk_idcard_id = $id;
                    $sendData->email = $email;
                    $sendData->send_type = $inlineRadioOptions;
                    $sendData->recievedType = $type_doc;
                    $sendData->soft_delete = 1;     

                    $savesendData = $this->SendData->save($sendData);

                    $saveSendId = $savesendData->id;

                    $origin_array = array(
                        'authorization: ' . $token,
                        'apikey: ' . $api,
                        'origin: ' . $origin_url
                    );

                    $emailHash = hash('sha256', $email);

                    $data_array = array(
                        'id'    => $saveSendId,
                        'component_id'    => $saveSendId,
                        'component'    => $type_doc,
                        'domain_name' => ORIGIN_URL,
                        'send_by' => 'email',
                        'content' => $email,
                        'deeplink_type' => $type_doc,
                        'company_name' => COMP_NAME
                    );

                    $method = "POST";

                    $type_method = 'firebase/deeplinking';

                    $response_data_deeplinking = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);

                    $response_deeplinking_Data = json_decode($response_data_deeplinking);

                    if($response_deeplinking_Data->status == 1) {

                        $deeplink = $response_deeplinking_Data->data->shortLink;
                        $doc_id = isset($response_deeplinking_Data->data->doc_id) ? $response_deeplinking_Data->data->doc_id : "";

                        $data_array = array(
                            'to'    => $email,
                            'subject'    => "Send " . $description_title,
                            'text' => "Send " . $description_title,
                            'html'    => "<p>Hi ".$email.", </p> <p>Please check your " . $description_title . " for details and download. </p> <p><a href='" . $deeplink . "'>Click Here</a>",
                            'recievedType' => $type_doc
                        );

                        $method = "POST";

                        $response_data = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);

                        $response_phone_Data = json_decode($response_data);

                        $send_status = "error";

                        if($response_phone_Data->status == 1) {

                            $send_status = "success";

                            $this->loadModel('SendData');

                            $docid = $this->Global->userIdEncode($doc_id);

                            $this->SendData->updateAll(
                                [
                                    'send_status'        => $send_status,
                                    'soft_delete'        => 0,
                                    'document_id'        => $docid 
                                ], 
                                [
                                    'id' => $saveSendId
                                ]
                            );

                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fk_idcard_id' => $idcard->id,
                                'email' => $phone,
                                'send_type' => $inlineRadioOptions,
                                'send_status' => $send_status,
                                'recievedType' => $type_doc,
                                'soft_delete' => 0
                            );

                            $this->Global->auditTrailApi($saveSendId, 'senddata', 'emailEform', null, $after);

                            $data['message'] = "success";
                            $data['data'] = "The " . $description_title . " has been sent successfully.";
                        } else {
                            $data['message'] = "type is not set!";
                        }
                    } else {
                        $data['message'] = "error";
                        $data['data'] = "Link generation issue!";
                    }
                }
            } else {
                $data['message'] = "error";
                $data['data'] = "Link generation issue!";
            }
        }  
        else if(isset($type) && $inlineRadioOptions == 'ackess') {
            
            if($ackess) {
                
                foreach($ackess as $akcessIds) {

                    $response_token = $this->Global->getToken();

                    $token = $response_token;

                    $origin_array = array(
                        'authorization: ' . $token,
                        'apikey: ' . $api,
                        'origin: ' . $origin_url,
                        'Content-Type: application/x-www-form-urlencoded'
                    );

                    $data_array = '?akcessId='.$akcessIds;

                    $type_method = 'users/akcessId';

                    $method = "GET";

                    $response_data_akcess_verify = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);

                    $verify_check = json_decode($response_data_akcess_verify);

                    if(isset($verify_check->data->akcessId) && $verify_check->data->akcessId != "") {
                        
                        $this->loadModel('SendData');

                        $sendData = $this->SendData->newEntity();
                        $sendData->fk_idcard_id = $id;
                        $sendData->ackessID = $akcessIds;
                        $sendData->send_type = $inlineRadioOptions;
                        $sendData->recievedType = $type_doc;
                        $sendData->send_status = $send_status;

                        $savesendData = $this->SendData->save($sendData);

                        $saveSendId = $savesendData->id;

                        $data_array = '{
                            "eformconfig": {
                                "akcessID": '.$array_akcesss_id.',
                                "UserHost": "AKcess-dev",
                                "formName": "'.$formName.'",
                                "description": "'.$description.'",
                                "eform": ['.$eformFieldText.'],
                                "eformId": "'.$eform->eformid.'",
                                "name": "'.ORIGIN_URL.'",
                                "signature": "'.$signature.'",
                                "logo": "'.$src.'",
                                "pulldata": "'.$pulldata.'",
                                "facematch": "'.$facematch.'",
                                "recievedType": "'.$type_doc.'",
                                "component_id": "'.$saveSendId.'",
                                "component": "'.$type_doc.'",
                                "domainName": "'.ORIGIN_URL.'",
                                "company_name": null,
                                "isAdditionalNotification": "'.$additional_notification.'",
                                "additionalNotificationTo": ['.$isAdditionalNotificationDetails.'],
                                "companyName": null
                            },
                            "portalDomainName": "'.ORIGIN_URL.'"
                        }'; 


                        //$data_array = json_encode($data_array, true);

                        $origin_array = array(
                            'Content-Type: application/json',
                            'authorization: ' . $token,
                            'apikey: ' . $api,
                            'Origin: ' . $origin_url
                        );    
                        
                        //marketplace-eforms

                        $type_method = 'incoming-eforms';

                        $method = "POST";   
                        
                        $response_data_akcess = $this->Global->curlGetPostEform($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);    
                        
                        $response_akcess_Data = json_decode($response_data_akcess);     

                        $send_status = "error";

                        if($response_akcess_Data->status == 1) {

                            $send_status = "success";

                            $this->loadModel('SendData');
                            
                            $this->SendData->updateAll(
                                [
                                    'response_id'        => $response_akcess_Data->data->_id,
                                    'send_status'        => $send_status,
                                    'soft_delete'        => 0
                                ], 
                                [
                                    'id' => $saveSendId
                                ]
                            );

                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fk_eform_id' => $id,
                                'ackessID' => $akcessIds,
                                'response_id' => $response_akcess_Data->data->_ids[0],
                                'send_type' => $inlineRadioOptions,
                                'send_status' => $send_status,
                                'recievedType' => $type_doc,
                                'soft_delete' => 0
                            );

                            $this->Global->auditTrailApi($saveSendId, 'senddata', 'akcessEform', null, $after);

                            $this->loadModel('ResponseEform');

                            $responseData = $this->ResponseEform->newEntity();
                            $responseData->fk_eform_id = $id;
                            $responseData->akcessId = $akcessIds;
                            $responseData->response_id = $response_akcess_Data->data->_ids[0];
                            $responseData->status = $send_status;

                            $saveResponseData = $this->ResponseEform->save($responseData);

                            $saveResponseId = $saveResponseData->id;

                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fk_eform_id' => $id,
                                'akcessId' => $akcessIds,
                                'response_id' => $response_akcess_Data->data->_ids[0],
                                'status' => $send_status
                            );

                            $this->Global->auditTrailApi($saveResponseId, 'response_eform', 'akcessEform', null, $after);

                            $data['message'] = "success";
                            $data['data'] = "The Eform has been sent successfully.";
                        } else {
                            $data['message'] = "error";
                            $data['data'] = "type is not set!";
                        }
                    } else {
                        $data['message'] = "error";
                        $data['data'] = "Akcess ID is not found!";
                    }
                }
            } else {
                $data['message'] = "error";
                $data['data'] = "Akcess ID is not found!";
            }
        } else {
            $data['message'] = "error";
            $data['data'] = "Please select any one options!";
        }
       
        print_r(json_encode($data));
        
        exit;
        
    }
        
    public function editField() {
        
        $conn = ConnectionManager::get("default"); // name of your database connection        
        
        $id = $this->Global->userIdDecode($_GET['eid']);
        
        $user_id = $this->Auth->user( 'id' );
        
        $role_id = $this->Auth->user( 'usertype' );
        
        $fieldsName = $_POST['field_name'];
        
        $this->loadModel('Fields');        
        
        $fieldsDetails_before = $this->Fields->find('all', array('conditions' => ['soft_delete' => 0, 'fk_eform_id' => $id]));
        
        $fields_array_before = array();
        
        if($fieldsName) {
        
            foreach($fieldsName as $key => $fieldsNames){

                $fk_eform_id = isset($fieldsNames->fk_eform_id) ? $fieldsNames->fk_eform_id : ''; 
                $labelname = isset($fieldsNames->labelname) ? $fieldsNames->labelname : ''; 
                $key = isset($fieldsNames->keyfields) ? $fieldsNames->keyfields : ''; 
                $keytype = isset($fieldsNames->keytype) ? $fieldsNames->keytype : ''; 
                $file_verified = isset($fieldsNames->file_verified) ? $fieldsNames->file_verified : ''; 
                $options = isset($fieldsNames->options) ? $fieldsNames->options : ''; 
                $verification_grade = isset($fieldsNames->verification_grade) ? $fieldsNames->verification_grade : ''; 
                $section_id = isset($fieldsNames->section_id) ? $fieldsNames->section_id : ''; 
                $instructions = isset($fieldsNames->instructions) ? $fieldsNames->instructions : ''; 
                $is_mandatory = isset($fieldsNames->is_mandatory) ? $fieldsNames->is_mandatory : ''; 
                $signature_required = isset($fieldsNames->signature_required) ? $fieldsNames->signature_required : '';

                $fields_array_before[] = array(
                    'fk_eform_id' => $fk_eform_id,
                    'labelname' => $labelname,
                    'keyfields' => $key,
                    'keytype' => $keytype,
                    'file_verified' => $file_verified,
                    'options' => $options,
                    'verification_grade' => $verification_grade,
                    'section_id' => $section_id,
                    'instructions' => $instructions,
                    'is_mandatory' => $is_mandatory,
                    'signature_required' => $signature_required
                );

            }
        }
                        
        $this->loadModel('FieldsOptions');        
        
        $fieldsOptionsDetails_before = $this->FieldsOptions->find('all', array('conditions' => ['soft_delete' => 0, 'fk_eform_id' => $id]));
        
        $fields_options_array_before = array();
        
        if($fieldsOptionsDetails_before) {
        
            foreach($fieldsOptionsDetails_before as $key => $fieldsOptionsDetails_befores){      

                $fk_eform_id = isset($fieldsOptionsDetails_befores->fk_eform_id) ? $fieldsOptionsDetails_befores->fk_eform_id : ''; 
                $fk_fields_id = isset($fieldsOptionsDetails_befores->fk_fields_id) ? $fieldsOptionsDetails_befores->fk_fields_id : ''; 
                $key = isset($fieldsOptionsDetails_befores->keyfields) ? $fieldsOptionsDetails_befores->keyfields : ''; 
                $lable = isset($fieldsOptionsDetails_befores->lable) ? $fieldsOptionsDetails_befores->lable : ''; 
                $uid = isset($fieldsOptionsDetails_befores->uid) ? $fieldsOptionsDetails_befores->uid : ''; 
                $checked = isset($fieldsOptionsDetails_befores->checked) ? $fieldsOptionsDetails_befores->checked : ''; 

                $fields_options_array_before[] = array(
                    'fk_eform_id' => $fk_eform_id,
                    'fk_fields_id' => $fk_fields_id,
                    'keyfields' => $key,
                    'lable' => $lable,
                    'uid' => $uid,
                    'checked' => $checked
                );

            }
        }
        
        $fieldsOptions_before = $fieldsOptionsDetails_before;
        
        $this->Fields->deleteAll(array('fk_eform_id' => $id));
            
        $this->FieldsOptions->deleteAll(array('fk_eform_id' => $id));
        
        $eformFields = array();
        
        $eformFieldText = '';
        
        $fields_array_after = array(
            'user_id'  => $user_id,
            'role_id'  => $role_id,
            'fk_user_id' => $user_id,
        );
        
        $fields_options_array_after = array(
            'user_id'  => $user_id,
            'role_id'  => $role_id,
            'fk_user_id' => $user_id,
        );
        
        foreach($fieldsName as $key => $fieldsNames){
            $name = isset($fieldsNames['name']) ? $fieldsNames['name'] : ''; 
            $instructions = isset($fieldsNames['instructions']) ? $fieldsNames['instructions'] : ''; 
            $type = isset($fieldsNames['type']) ? $fieldsNames['type'] : ''; 
            $isVisible = isset($fieldsNames['isVisible']) ? $fieldsNames['isVisible'] : ''; 
            $section = isset($fieldsNames['section']) ? $fieldsNames['section'] : ''; 
            $verification_grade = isset($fieldsNames['verification_grade']) ? $fieldsNames['verification_grade'] : ''; 
            $fieldver = isset($fieldsNames['fieldver']) ? $fieldsNames['fieldver'] : ''; 
            $field_mandate = isset($fieldsNames['field_mandate']) ? $fieldsNames['field_mandate'] : ''; 
            $signature_required = isset($fieldsNames['signature_required']) ? $fieldsNames['signature_required'] : ''; 
            $ids = isset($fieldsNames['ids']) ? $fieldsNames['ids'] : ''; 
            $items = isset($fieldsNames['items']) ? $fieldsNames['items'] : ''; 
            $key = isset($fieldsNames['keyfields']) ? $fieldsNames['keyfields'] : ''; 
            
            $key_value = $key;
            if (strpos($key, '_') !== false) { 
                $key_value_explode = explode("_", $key);                
                $key_value = $key_value_explode[0];
            }
            
            $fields_array_after[] = array(
                'fk_eform_id' => $id,
                'labelname' => $name,
                'keyfields' => $key_value,
                'keytype' => $type,
                'file_verified' => $fieldver,
                'options' => $items,
                'verification_grade' => $verification_grade,
                'section_id' => $section,
                'instructions' => $instructions,
                'is_mandatory' => $field_mandate,
                'signature_required' => $signature_required
            );
            
            $sql = "INSERT INTO `fields` (`fk_eform_id`, `labelname`, `keyfields`, `keytype`, `file_verified`, `options`, `verification_grade`, `section_id`, `instructions`, `is_mandatory`, `signature_required`) VALUES (".$id.",'".$name."','".$key_value."','".$type."','".$fieldver."','".$items."','".$verification_grade."','".$section."','".$instructions."','".$field_mandate."','".$signature_required."')";
            
            $saveFields = $conn->execute($sql);  
            
            $sql_last_query_fields = "SELECT id FROM `fields` ORDER BY ID DESC LIMIT 0,1";

            $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

            $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

            $lastfieldsInsertedId = $sql_last_fields_id['id'];
            
            $items_array = explode(",", $items);
            
            foreach($items_array as $items_arrays) {
                if($items_arrays) {                      
                    $optionsid = $this->Global->random_string('alnum', 12);
                    
                    $sql_options = "INSERT INTO `fields_options` (`fk_eform_id`, `fk_fields_id`, `keyfields`, `lable`, `uid`, `checked`) VALUES (".$id.",".$lastfieldsInsertedId.",'".strtolower($items_arrays)."','".$items_arrays."','".$optionsid."','false')";
            
                    $conn->execute($sql_options);       
                    
                    $fields_options_array_after[] = array(
                        'fk_eform_id' => $fk_eform_id,
                        'fk_fields_id' => $lastfieldsInsertedId,
                        'keyfields' => strtolower($items_arrays),
                        'lable' => $items_arrays,
                        'uid' => $optionsid,
                        'checked' => 'false'
                    );
                    
                }
            }            
            
        }
        
        $lastInsertedfieldsId = $this->Global->auditTrailApi($id, 'fields', 'update', $fields_array_before, $fields_array_after);        
        $this->Global->auditTrailApiSuccess($lastInsertedfieldsId, 1);        
       
        $lastInsertedfieldsOptionsId = $this->Global->auditTrailApi($id, 'fields_options', 'update', $fields_options_array_before, $fields_options_array_after);
        
        $this->Global->auditTrailApiSuccess($lastInsertedfieldsOptionsId, 1);
        
        if ($saveFields) {
            $data['message'] = "success";
            $data['data'] = "The eform fields has been saved.";
        } else {
            $data['message'] = "error";
            $data['data'] = "The eform fields could not be saved. Please, try again.";
        }
        
        $resultJ = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
    }
    
    public function getAkcessID(){
        
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        
        $response_token = $this->Global->getToken();
           
        $token = $response_token;
        
        $origin_array = array(
            'authorization: ' . $token,
            'apikey: ' . $api,
            'origin: ' . $origin_url,
            'Content-Type: application/x-www-form-urlencoded'
        );
      
        $data_array = '';

        $type = 'users/akcessId';
        
        $method = "GET";
                
        $response_data_akcess_verify = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);
                
        return json_decode($response_data_akcess_verify);
        
    }
    
    public function getFields(){
        
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_FIELD_URL;
        $country = isset($_GET['c']) ? $_GET['c'] : '';
        
        $response_token = $this->Global->getToken();
           
        $token = $response_token;

        $origin_array = array(
            'authorization: ' . $token,
            'apikey: ' . $api,
            'origin: ' . $origin_url
        );
        
        
        $type = 'getGlobalprofileField';
      
        $data_array = '';
        if(isset($country) && $country != "" && $country != "undefined") {
            $data_array = array(
                'country'    => $country
            );
        }        
        
        $method = "POST";
        
        $response_data_fields = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);        
        $response_fields = json_decode($response_data_fields);
        
        $resultJ = json_encode($response_fields->data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
        
    }
    
    public function getResponseData(){
        
        $conn = ConnectionManager::get("default"); // name of your database connection   
                
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        
        $dataArray = array();
        
        if($type == 'viewemail') {
            
            $query_response = "SELECT email,response_id,status,created FROM `response_eform` WHERE email != '' ";

            $results = $conn->execute($query_response);

            $data = $results->fetchAll('assoc');
            
            foreach($data as $key => $datas) {
                $dataArray[] = array(
                    $datas["email"],
                    $datas["status"],
                    $datas["created"]
                );
            }
            
        } else if($type == 'viewphone') {
            
            $query_response = "SELECT phone_no,response_id,status,created FROM `response_eform` WHERE phone_no != '' ";

            $results = $conn->execute($query_response);

            $data = $results->fetchAll('assoc');
            
            foreach($data as $key => $datas) {
                $dataArray[] = array(
                    $datas["phone_no"],
                    $datas["status"],
                    $datas["created"]
                );
            }
            
        } else if($type == 'viewackess') {
            
            $query_response = "SELECT akcessId,response_id,status,created FROM `response_eform` WHERE akcessId != '' ";

            $results = $conn->execute($query_response);

            $data = $results->fetchAll('assoc');
            
            foreach($data as $key => $datas) {
                $dataArray[] = array(
                    $datas["akcessId"],
                    $datas["status"],
                    $datas["created"]
                );
            }
            
        }  
        
        $resultJ = json_encode($dataArray);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
        
    }
    public function eFormData() {        
       
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        $description = isset($_POST['description']) ? $_POST['description'] : '';
        $formName = isset($_POST['formName']) ? $_POST['formName'] : '';
        $instruction = isset($_POST['instruction']) ? $_POST['instruction'] : ''; 
        $date = date('Y-m-d H:i:s');
        
        $eformid = $this->Global->random_string('alnum', 24);
      
        $this->loadModel('Eform');
        
        $users = $this->Auth->user();

        $eform = $this->Eform->newEntity();
        $eform->formName = $formName;
        $eform->description = $description;
        $eform->instruction = $instruction;
        $eform->date = $date;
        $eform->UserId = $users->id;
        $eform->eformid = $eformid;

        $savesendData = $this->Eform->save($eform);
        
        $saveSendId = $this->Global->userIdEncode($savesendData->id);
        
        $data['id'] = $saveSendId; 
        $data['message'] = "success";
        
        
//        $after = array(
//            'user_id' => $user_id,
//            'role_id' => $role_id, 
//            'fk_user_id' => $formName,
//            'formName' => $formName,
//            'description' => $description,
//            'instruction' => $instruction,
//            'date' => $date,
//            'UserId' => $users->id,
//            'eformid' => $eformid,
//            'documentId' => $documentID
//        );
//
//        $this->Global->auditTrailApi($saveVerifyDocsId, 'verifeddocs', 'insert', null, $after);
       
        print_r(json_encode($data));
        
        exit;
        
    }

    public function delete($idEncode = null) {
        
        $eformid = $this->Global->userIdDecode($idEncode);
        
        $this->request->allowMethod(['post', 'delete']);
        
        $eform = $this->Eform->get($eformid);
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'fk_eform_id' => $eformid,     
            'soft_delete' => 1
        );
        
        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($eformid, 'eform', 'delete', $before, $after);
        
        $updateEform = $this->Eform->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $eformid
            ]
        );
        
        $formName = $eform->formName;
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
        
        if ($updateEform) {
            $this->Flash->success(__('The ' . $formName . ' eform has been deleted.'));
        } else {
            $this->Flash->error(__('The ' . $formName . ' eform could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Eform', 'action' => 'index']);
    }
    
}
