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
use Cake\Routing\Router;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotifyTable $notify
 *
 * @method \App\Model\Entity\Notify[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class EformResponseController extends AppController {

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

//        $conn = ConnectionManager::get("default"); // name of your database connection
//
//        $query = 'SELECT er.formName, er.akcessId, er.status, er.id, er.created FROM eformresponse as er
//                LEFT JOIN eform as e ON e.eformid = er.eformId WHERE er.soft_delete=0 ORDER BY er.created DESC limit 5';
//
//        $querySql = $conn->execute($query);
//
//        $query_response = $querySql->fetchAll('assoc');
//
//        $eformResponse = array();
//
//        $this->loadModel('Users');
//
//        foreach($query_response as $key => $value) {
//
//            $eformResponse[] = array(
//                'formName' => $value['formName'],
//                'akcessId' => $value['akcessId'],
//                'status' => isset($value['status']) && $value['status'] != "" ? $value['status'] : "alive",
//                'id' => $value['id'],
//                'created' => $value['created'],
//            );
//        }
//
//        $this->set(compact('eformResponse'));
        
        $this->set('page_title', 'EForms Responses');

        $this->set('page_icon', '<i class="fab fa-wpforms mr-1"></i>');
    }

    public function getList()
    {
        $conn = ConnectionManager::get("default"); // name of your database connection

        $param = $this->request->data();

        $limit = '';
        if(array_key_exists('length',$param) && $param['length'] != -1)
        {
            $limit = ' limit '.$param['start'].', '.$param['length'];
        }

        $search = '';
        if(array_key_exists('search',$param) && $param['search']['value'] != '')
        {
            /* Set searchable column fields */
            $search = " AND (er.formName like '%".$param['search']['value']."%' or er.akcessId like '%".$param['search']['value']."%' or er.id like '%".$param['search']['value']."%')";
        }


        $query = 'SELECT er.formName, er.akcessId, er.status, er.id, er.created FROM eformresponse as er
                LEFT JOIN eform as e ON e.eformid = er.eformId WHERE er.soft_delete=0'.$search.' ORDER BY er.created DESC'.$limit;

        $querySql = $conn->execute($query);
        $data = $querySql->fetchAll('assoc');


        $query1 = 'SELECT count(er.id) as total FROM eformresponse as er
                LEFT JOIN eform as e ON e.eformid = er.eformId WHERE er.soft_delete=0'.$search;
        $querySql1 = $conn->execute($query1);
        $query_response1 = $querySql1->fetch('assoc');
        $total_record = $query_response1['total'];


        $tblData = array();

        $i       = $param['start'];
        if(!empty($data))
        {
            foreach($data as $r)
            {


                /* set action data */
                $action_btn  = '<div class="actions-div">';
                $action_btn .= '<a href="javascript:void(0);" data-title="'.$r['id'].'" class="viewEform" onclick="getEformResponse('.$r['id'].');" data-backdrop="static" data-keyboard="false" data-toggle="tooltip" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                $action_btn .= '</div>';

                $fieldsResponseFname = $this->getData('firstname', $r['id']);
                $name = "";
                $space = "";
                if(isset($fieldsResponseFname['value']) && $fieldsResponseFname['value'] != '') {
                    $name = $fieldsResponseFname['value'];
                    $space = " ";
                }

                $fieldsResponseMname = $this->getData('middlename', $r['id']);
                if(isset($fieldsResponseMname['value']) && $fieldsResponseMname['value'] != '') {
                    $name = $name . $space . $fieldsResponseMname['value'];
                    $space = " ";
                }

                $fieldsResponseLname =$this-> getData('lastname', $r['id']);
                if(isset($fieldsResponseLname['value']) && $fieldsResponseLname['value'] != '') {
                    $name = $name . $space . $fieldsResponseLname['value'];
                }

                $tdArray   = array();
                $tdArray[] = ++$i;
                $tdArray[] = $name  ?  $name  : '-';
                $tdArray[] = isset($r['akcessId']) ? $r['akcessId'] : '-';
                $tdArray[] = isset($r['formName']) ? $r['formName'] : '-';
                $tdArray[] = isset($r['status']) && $r['status'] != "" ? $r['status'] : "alive";
                $tdArray[] = isset($r['created']) && $r['created'] ? date("d/m/Y h:i:s", strtotime($r['created'])) : '-';
                $tdArray[] = $action_btn;
                $tblData[] = $tdArray;
            }
        }

        $output = array(
            "draw"            => $param['draw'],
            "recordsFiltered" => $total_record,
            "recordsTotal" => $total_record,
            "data"            => $tblData,
        );
        /* Output to JSON format */
        echo json_encode($output);
        exit;

    }

    function getData($key, $id){

        $conn = ConnectionManager::get("default"); // name of your database connection

        $query = $conn->execute('SELECT * FROM fieldsresponse WHERE fk_eformresponse_id="'.$id.'" AND keyfields="'.$key.'" AND soft_delete=0');

        $query_response = $query->fetch('assoc');

        return $query_response;
    }
    
    public function getDataFromDocument(){
         
        $conn = ConnectionManager::get("default"); // name of your database connection  

        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;
        
        $c = isset($_POST['c']) ? $this->Global->userIdDecode($_POST['c']) : '';
        $data_key = isset($_POST['data_key']) ? $_POST['data_key'] : '';
        $data_value = isset($_POST['data_value']) ? $_POST['data_value'] : '';

        
        $query = 'SELECT fr.*, er.formName, er.akcessId, er.eformId, er.id as erid, er.status,er.eformasfile,er.facematch,er.profilepic FROM fieldsresponse as fr
                LEFT JOIN eformresponse as er ON fr.fk_eformresponse_id = er.id
                WHERE er.id = '.$c;
       
        $querySql = $conn->execute($query);

        $query_response = $querySql->fetchAll('assoc');
        
        $data = '';

        $respnose_data['message'] = "error";
        $respnose_data['data'] = "Data not found from document. Please, try again.";

        foreach ($query_response as $key => $n) {

            $fieldsresponse_id = $n['id'];
            $id = $n['id'];
            $erid = $n['erid'];
            $status = $n['status'];
            $fk_eformresponse_id = $n['fk_eformresponse_id'];
            $fk_eform_id = $n['fk_eform_id'];
            $labelname = $n['labelname'];
            $keyfields = $n['keyfields'];
            $keytype = $n['keytype'];
            $value = $n['value'];
            $file = $n['file'];
            $verify_status = $n['verify_status'];
            $file_verified = $n['file_verified'];
            $expiryDate = date('d/m/Y',strtotime($n['expiryDate']));
            $isverified = $n['isverified'];
            $docuementType = $n['docuementType'];
            $isDocFetched = $n['isDocFetched'];
            $signature_required = $n['signature_required'];
            $options = $n['options'];
            $verification_grade = $n['verification_grade'];
            $section_id = $n['section_id'];
            $section_color = $n['section_color'];
            $sectionfields = $n['sectionfields'];
            $formName = $n['formName'];
            $akcessId = $n['akcessId'];
            $eformId = $n['eformId'];  
            $eformasfile = $n['eformasfile'];  
            $facematch = $n['facematch']; 
            $profilepic = $n['profilepic']; 

            $faceMatchPic_imgData = "";
            
            if ($facematch == 'yes') {
                $doc_dir_faceMatchPic  = WWW_ROOT . "/uploads/eforms/" . $eformId . "/" . $profilepic;

                // A few settings
                $doc_file_Pic = $doc_dir_faceMatchPic;
                // Read image path, convert to base64 encoding
                $docData_faceMatchPic = base64_encode(file_get_contents($doc_file_Pic));

                // Format the image SRC:  data:{mime};base64,{data};
                $faceMatchPic_imgData = 'data:' . mime_content_type($doc_file_Pic) . ';base64,' . $docData_faceMatchPic;
            }

            if ( $keytype == 'file') {

                if ($data_key == $eformId && $data_value == $value) {

                    $doc_dir_file  = WWW_ROOT . "uploads/eforms/" . $eformId . "/" . $value;    
                    //$doc_dir_file  = WWW_ROOT . "uploads/eforms/" . $eformId . "/1c9dd0bf-cb22-4eb4-8e8a-89ffd3ef8dd4.h5cexp8kd62ur7wgn490qjbm.pdf";    
                    
                    $last_dot_index = strrpos($value, ".");
                    $without_extention = substr($value, 0, $last_dot_index);
                    
                    $doc_dir_file_image  = WWW_ROOT . "uploads/eforms/" . $eformId . "/" . $without_extention . '.png';
                    
                    // source PDF file
                    $source = $doc_dir_file;
                    // output file
                    $target = $doc_dir_file_image;                    
                    // create a command string 

                    exec('pdftoppm -png "'.$source .'" "'.$target.'"', $output, $response);
                   
                    // A few settings
                    $doc_file_image_png = WWW_ROOT . "uploads/eforms/" . $eformId . "/" . $without_extention . '.png-1.png';

                    $doc_file_image_jpg = WWW_ROOT . "uploads/eforms/" . $eformId . "/" . $without_extention . '.jpg';

                    $image = imagecreatefrompng($doc_file_image_png);
                    imagejpeg($image, $doc_file_image_jpg , 100);
                    imagedestroy($image);
                    // Read image path, convert to base64 encoding
                    $docData = base64_encode(file_get_contents($doc_file_image_jpg));

                    // Format the image SRC:  data:{mime};base64,{data};
                    //$valuefile_imgData = 'data:' . mime_content_type($doc_file_image_jpg) . ';base64,' . $docData;
                    $valuefile_imgData = $docData;

                    if($facematch == 'yes') {
                        $valuefile_data_array = array(
                            'document'    => $valuefile_imgData,
                            'documentType'    => 1,
                            'facematch' => $facematch,
                            'faceMatchPic' => $faceMatchPic_imgData
                        );
                    } else {
                        $valuefile_data_array = array(
                            'document'    => $valuefile_imgData,
                            'documentType'    => 1,
                            'facematch' => $facematch
                        );
                    }

                    $response_token = $this->Global->getToken();

                    $token = $response_token;

                    $origin_array = array(
                        'authorization: ' . $token,
                        'apikey: ' . $api,
                        'origin: ' . $origin_url
                    );

                    $valuefile_method = "POST";

                    $valuefile_type_method = 'getDataFromDocument';

                    // print_r($valuefile_method);
                    // print_r($valuefile_type_method);
                    // print_r($api);
                    // print_r($origin_url);
                    // print_r($api_url);
                    // print_r($valuefile_data_array);
                    // print_r($origin_array);

                    $valuefile_response_data = $this->Global->curlGetPost($valuefile_method, $valuefile_type_method, $api, $origin_url, $api_url, $valuefile_data_array, $origin_array);

                    $response = json_decode($valuefile_response_data);
                    $doc_data = isset($response->data->idDocuments[0]) ? $response->data->idDocuments[0] : array();
                    if(!empty($doc_data))
                    {
                        $forename        = isset($doc_data->countryCode) ? $doc_data->countryCode : '';
                        $middleName      = isset($doc_data->middleName) ? $doc_data->middleName : '';
                        $surname         = isset($doc_data->surname) ? $doc_data->surname : '';
                        $fullName        = isset($doc_data->fullName) ? $doc_data->fullName : '';
                        $dateOfBirth     = isset($doc_data->dateOfBirth) ? $doc_data->dateOfBirth : '';
                        $country         = isset($doc_data->country) ? $doc_data->country : '';
                        $documentNumber  = isset($doc_data->documentNumber) ? $doc_data->documentNumber : '';
                        $expiryDate      = isset($doc_data->expiryDate) ? $doc_data->expiryDate : '';
                        $addressFull     = isset($doc_data->addressFull) ? $doc_data->addressFull : '';
                        $addressPostcode = isset($doc_data->addressPostcode) ? $doc_data->addressPostcode : '';
                        $addressCity     = isset($doc_data->addressCity) ? $doc_data->addressCity : '';

                        $docExpiredDate =  date('Y-m-d H:i:s',strtotime('+1 year'));

                        $insert_query1 = "INSERT INTO `fieldsresponse` (
                        `fk_eformresponse_id`,
                        `fk_eform_id`,
                        `labelname`,
                        `keyfields`,
                        `keytype`,
                        `value`,
                        `expiryDate`,
                        `docuementType`,
                        `isDocFetched`
                    ) VALUES (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'forename',
                        'forename',
                        'string',
                        '".$forename."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'middle name',
                        'middleName',
                        'string',
                        '".$middleName."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'surname',
                        'surname',
                        'string',
                        '".$surname."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'full name',
                        'fullName',
                        'string',
                        '".$fullName."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'date of birth',
                        'dateOfBirth',
                        'string',
                        '".$dateOfBirth."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'country',
                        'country',
                        'string',
                        '".$country."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'document number',
                        'documentNumber',
                        'string',
                        '".$documentNumber."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'expiry date',
                        'expiryDate',
                        'string',
                        '".$expiryDate."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'address full',
                        'addressFull',
                        'string',
                        '".$addressFull."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'address postcode',
                        'addressPostcode',
                        'string',
                        '".$addressPostcode."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    ),
                    (
                        ".$fk_eformresponse_id.",
                        '".$fk_eform_id."',
                        'address city',
                        'addressCity',
                        'string',
                        '".$addressCity."',
                        '".$docExpiredDate."',
                        '0',
                        '1'
                    )";

                    $conn->execute($insert_query1);






//                        $calling_code = '';
//                        $countryCode = isset($doc_data->countryCode) ? $doc_data->countryCode : '';
//                        $country = isset($doc_data->country) ? $doc_data->country : '';
//                        if($countryCode && $country)
//                        {
//                            $query1 = $conn->execute("SELECT calling_code FROM `countries` WHERE country_code = '".$countryCode."' OR `country_name` LIKE '".$country."'");
//                            $country_query_response = $query1->fetch('assoc');
//
//                            if(!empty($country_query_response))
//                            {
//                                $calling_code = isset($country_query_response['calling_code']) ? $country_query_response['calling_code'] : '';
//
//                                if($calling_code)
//                                {
//                                    $sql_1 = "UPDATE `fieldsresponse` SET `value` = '".$calling_code."' WHERE `keyfields` = 'countrycode' AND `fk_eformresponse_id` = ".$fk_eformresponse_id;
//                                    $conn->execute($sql_1);
//                                }
//                            }
//                        }
//
//                        $lastname = isset($doc_data->surname) ? $doc_data->surname : '';
//                        if($lastname)
//                        {
//                            $sql_2 = "UPDATE `fieldsresponse` SET `value` = '".$lastname."' WHERE `keyfields` = 'lastname' AND `fk_eformresponse_id` = ".$fk_eformresponse_id;
//                            $conn->execute($sql_2);
//                        }
//
//                        $firstname = isset($doc_data->forename) ? $doc_data->forename : '';
//                        if($firstname)
//                        {
//                            $sql_3 = "UPDATE `fieldsresponse` SET `value` = '".$firstname."' WHERE `keyfields` = 'firstname' AND `fk_eformresponse_id` = ".$fk_eformresponse_id;
//                            $conn->execute($sql_3);
//                        }
//
//                        $civilidexpirydate = isset($doc_data->expiryDate) && $doc_data->expiryDate ? $doc_data->expiryDate : '';
//                        if($civilidexpirydate)
//                        {
//                            $dExpiredArray = explode('T',$civilidexpirydate);
//                            $dExpired = isset($dExpiredArray[0]) ? $dExpiredArray[0] : '';
//                            if($dExpired)
//                            {
//                                $sql_4 = "UPDATE `fieldsresponse` SET `value` = '".$dExpired."' WHERE `keyfields` = 'civilidexpirydate' AND `fk_eformresponse_id` = ".$fk_eformresponse_id;
//                                $conn->execute($sql_4);
//                            }
//                        }
//
//                        $civilidnumber = isset($doc_data->documentNumber) ? $doc_data->documentNumber : '';
//                        if($civilidnumber)
//                        {
//                            $sql_5 = "UPDATE `fieldsresponse` SET `value` = '".$civilidnumber."' WHERE `keyfields` = 'civilidnumber' AND `fk_eformresponse_id` = ".$fk_eformresponse_id;
//                            $conn->execute($sql_5);
//                        }
//
//                        $sql_6 = "UPDATE `fieldsresponse` SET `isDocFetched` = 1 WHERE `id` = ".$fieldsresponse_id;
//                        $conn->execute($sql_6);

                    }

                    $insert_query = "INSERT INTO `eformresponse_document_data` (
                        `fk_eform_response_id`, 
                        `fk_field_response_id`, 
                        `eformId`, 
                        `document_value`,
                        `description`
                    ) VALUES (
                        ".$fk_eformresponse_id.",
                        '".$id."',
                        '".$eformId."',
                        '".$value."',
                        '".json_encode($response->data)."'
                    )";

                    $conn->execute($insert_query);

                    $respnose_data['message'] = "success";
                    $respnose_data['data'] = "Data found successfully from document.";
                
                }
                
            } 

            
        }
                
        $resultJ = json_encode($respnose_data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
        
    }
        
    
    public function getFields(){

        $conn = ConnectionManager::get("default"); // name of your database connection  
        
        $c = isset($_GET['c']) ? $_GET['c'] : '';
        
        $query = 'SELECT fr.*, er.formName, er.akcessId, er.eformId, er.id as erid, er.status,er.eformasfile FROM fieldsresponse as fr
                LEFT JOIN eformresponse as er ON fr.fk_eformresponse_id = er.id
                WHERE fr.is_public = 0 AND fr.isDocFetched = 0 AND fr.fk_eformresponse_id = '.$c;

        $querySql = $conn->execute($query);

        $query_response = $querySql->fetchAll('assoc');

        $query1 = 'SELECT fr.*, er.formName, er.akcessId, er.eformId, er.id as erid, er.status,er.eformasfile FROM fieldsresponse as fr
                LEFT JOIN eformresponse as er ON fr.fk_eformresponse_id = er.id
                WHERE fr.is_public = 0 AND fr.isDocFetched = 1 AND fr.fk_eformresponse_id = '.$c;

        $querySql1 = $conn->execute($query1);

        $query_response1 = $querySql1->fetchAll('assoc');


        $html = '';

        $docFieldHtml = '';
        if(!empty($query_response1))
        {
            foreach ($query_response1 as $key => $n) {

                $id = $n['id'];
                $erid = $n['erid'];
                $status = $n['status'];
                $fk_eformresponse_id = $n['fk_eformresponse_id'];
                $fk_eform_id = $n['fk_eform_id'];
                $labelname = $n['labelname'];
                $keyfields = $n['keyfields'];
                $keytype = $n['keytype'];
                $value = $n['value'];
                $file = $n['file'];
                $verify_status = $n['verify_status'];
                $file_verified = $n['file_verified'];
                $expiryDate = date('Y-m-d',strtotime($n['expiryDate']));
                $isverified = $n['isverified'];
                $docuementType = $n['docuementType'];
                $isDocFetched = $n['isDocFetched'];
                $signature_required = $n['signature_required'];
                $options = $n['options'];
                $verification_grade = $n['verification_grade'];
                $section_id = $n['section_id'];
                $section_color = $n['section_color'];
                $sectionfields = $n['sectionfields'];
                $formName = $n['formName'];
                $akcessId = $n['akcessId'];
                $eformId = $n['eformId'];
                $eformasfile = $n['eformasfile'];



                $id_verify = $this->Global->userIdEncode($id);
                $erid_verify = $this->Global->userIdEncode($erid);

                $checked = "";
                if($verify_status == 'Yes') {
                    $checked = "checked";
                }

                $eformasfile_url = Router::url('/', true) . "uploads/eforms/".$eformId."/".$eformasfile;

                if ( $keytype == 'string' || $keytype == 'text') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<div class="col-sm-10">
            <input type="text" class="form-control" value="'.$value.'" readonly /></div>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm ">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';

                } else if ( $keytype == 'password') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<div class="col-sm-10">
            <input type="password" class="form-control" value="'.$value.'" readonly /></div>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                } else if ( $keytype == 'file') {

                    if($keyfields == 'profilepic')
                    {
                        $docFieldHtml .= '<tr>';
                        $docFieldHtml .= '<td>';
                        $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                        $doc_url = Router::url('/', true) . $value;
//                        $doc_dir_data_key = $eformId;
//                        $doc_dir_data_value = $value;
                        $docFieldHtml .= '<div class="col-sm-10"><a target="_blank" href="'.$doc_url.'"> Profile pic </a>';
                        //$docFieldHtml .= '<a href="javascript:void(0);" onclick="getDataFromDocument(\''.$doc_dir_data_key.'\',\''.$doc_dir_data_value.'\');"><i class="fa fa-play-circle-o" style="vertical-align: middle"></i></a>';
                        $docFieldHtml .= '</div>';
                        //$docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="expiry_date_'.$key.'" value="'.$value.'" />';
                        //$docFieldHtml .= '<input type="file" class="form-control" value="'.$value.'" readonly />';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '<td class="text-center">';
                        $docFieldHtml .= '<div class="col-sm-10">
            <input class="checkbox_display" '.$checked.' type="checkbox" style="margin-left: 12px;" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" /></div>';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '<td class="text-center">';
                        $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                        $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                        $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                        $docFieldHtml .= '
        <a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '<td class="text-center">';
                        $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '</tr>';
                    }else
                    {
                        $docFieldHtml .= '<tr>';
                        $docFieldHtml .= '<td>';
                        $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                        $doc_url = Router::url('/', true) . "uploads/eforms/".$eformId."/".$value;
                        $doc_dir_data_key = $eformId;
                        $doc_dir_data_value = $value;
                        $docFieldHtml .= '<div class="col-sm-10"><a target="_blank" href="'.$doc_url.'"> Document </a>';
                        $docFieldHtml .= '<a href="javascript:void(0);" onclick="getDataFromDocument(\''.$doc_dir_data_key.'\',\''.$doc_dir_data_value.'\');"><i class="fa fa-play-circle-o" style="vertical-align: middle"></i></a>';
                        $docFieldHtml .= '</div>';
                        //$docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="expiry_date_'.$key.'" value="'.$value.'" />';
                        //$docFieldHtml .= '<input type="file" class="form-control" value="'.$value.'" readonly />';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '<td class="text-center">';
                        $docFieldHtml .= '<div class="col-sm-10">
            <input class="checkbox_display" '.$checked.' type="checkbox" style="margin-left: 12px;" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" /></div>';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '<td class="text-center">';
                        $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                        $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                        $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                        $docFieldHtml .= '
        <a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '<td class="text-center">';
                        $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                        $docFieldHtml .= '</td>';
                        $docFieldHtml .= '</tr>';
                    }


                } else if ( $keytype == 'radio') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<div class="custom-control custom-radio">';
                    $docFieldHtml .= '<div class="col-sm-10">
                <input type="radio" name="active-status" id="activeStatus" class="custom-control-input" checked disabled></div>';
                    $docFieldHtml .= '<label class="custom-control-label" for="activeStatus">'.$value.'</label>';
                    $docFieldHtml .= '</div>';
                    //$docFieldHtml .= '<input type="radio" class="form-check-input" checked readonly />'.$value;
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                } else if ( $keytype == 'checkbox') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $explode_value =  $value;
                    if (strpos($value, ',') !== false) {
                        $explode_value = explode(",", $value);
                    }
                    $count_value = count($explode_value);
                    if($count_value == 1) {
                        $docFieldHtml .= '<div class="custom-control custom-checkbox">';
                        $docFieldHtml .= '<input type="checkbox" checked disabled name="checkbox-status" id="checkboxStatus" class="custom-control-input">';
                        $docFieldHtml .= '<label class="custom-control-label" for="checkboxStatus">'.$value.'</label>';
                        $docFieldHtml .= '</div>';
                    } else {
                        if($explode_value) {
                            foreach($explode_value as $key => $v) {
                                $docFieldHtml .= '<div class="custom-control custom-checkbox">';
                                $docFieldHtml .= '<input type="checkbox" checked disabled name="checkbox-status" id="checkboxStatus" class="custom-control-input">';
                                $docFieldHtml .= '<label class="custom-control-label" for="checkboxStatus">'.$v.'</label>';
                                $docFieldHtml .= '</div>';
                            }
                        }
                    }
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                } else if ( $keytype == 'address' || $keytype == 'textarea') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<textarea col="3" class="form-control" readonly>'.$value.'</textarea>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<div class="col-sm-10">
            <input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" /></div>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                } else if ( $keytype == 'list' || $keytype == 'select') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<div class="col-sm-10"><select class="form-control" readonly>';
                    $docFieldHtml .= '<option value="'.$value.'">'.$value.'</option>';
                    $docFieldHtml .= '</select></div>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                } else if ( $keytype == 'phone') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<div class="col-sm-10">
            <input type="tel" class="form-control" value="'.$value.'" readonly /></div>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                } else if ( $keytype == 'number') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<div class="col-sm-10">
            <input type="number" class="form-control" value="'.$value.'" readonly /></div>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                } else if ( $keytype == 'date') {
                    $docFieldHtml .= '<tr>';
                    $docFieldHtml .= '<td>';
                    $docFieldHtml .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                    $docFieldHtml .= '<div class="col-sm-10">
            <input type="date" class="form-control" value="'.$value.'" readonly /></div>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $docFieldHtml .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $docFieldHtml .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '<td class="text-center">';
                    $docFieldHtml .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $docFieldHtml .= '</td>';
                    $docFieldHtml .= '</tr>';
                }
            }
        }

        foreach ($query_response as $key => $n) {

            $id = $n['id'];
            $erid = $n['erid'];
            $status = $n['status'];
            $fk_eformresponse_id = $n['fk_eformresponse_id'];
            $fk_eform_id = $n['fk_eform_id'];
            $labelname = $n['labelname'];
            $keyfields = $n['keyfields'];
            $keytype = $n['keytype'];
            $value = $n['value'];
            $file = $n['file'];
            $verify_status = $n['verify_status'];
            $file_verified = $n['file_verified'];
            $expiryDate = date('Y-m-d',strtotime($n['expiryDate']));
            $isverified = $n['isverified'];
            $docuementType = $n['docuementType'];
            $isDocFetched = $n['isDocFetched'];
            $signature_required = $n['signature_required'];
            $options = $n['options'];
            $verification_grade = $n['verification_grade'];
            $section_id = $n['section_id'];
            $section_color = $n['section_color'];
            $sectionfields = $n['sectionfields'];
            $formName = $n['formName'];
            $akcessId = $n['akcessId'];
            $eformId = $n['eformId'];  
            $eformasfile = $n['eformasfile'];  
            


            $id_verify = $this->Global->userIdEncode($id);
            $erid_verify = $this->Global->userIdEncode($erid);

            $checked = "";
            if($verify_status == 'Yes') {
                $checked = "checked";
            }

            $eformasfile_url = Router::url('/', true) . "uploads/eforms/".$eformId."/".$eformasfile;

            if ( $keytype == 'string' || $keytype == 'text') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<div class="col-sm-10">
                <input type="text" class="form-control" value="'.$value.'" readonly /></div>';
                $html .= '</td>';
                $html .= '<td class="text-center">';                
                $html .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm ">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
                
            } else if ( $keytype == 'password') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<div class="col-sm-10">
                <input type="password" class="form-control" value="'.$value.'" readonly /></div>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else if ( $keytype == 'file') {

                if($keyfields == 'profilepic')
                {
                    $html .= '<tr>';
                    $html .= '<td>';
//                    $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $doc_url = Router::url('/', true) .$value;
                    $doc_dir_data_key = $eformId;
                    $doc_dir_data_value = $value;
                    $html .= '<div class="col-sm-10"><a target="_blank" href="'.$doc_url.'"> Profile pic </a>';
                    $html .= '</div>';
                    //$html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="expiry_date_'.$key.'" value="'.$value.'" />';
                    //$html .= '<input type="file" class="form-control" value="'.$value.'" readonly />';
                    $html .= '</td>';
                    $html .= '<td class="text-center">';
                    $html .= '<div class="col-sm-10">
                <input class="checkbox_display" '.$checked.' type="checkbox" style="margin-left: 12px;" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" /></div>';
                    $html .= '</td>';
                    $html .= '<td class="text-center">';
                    $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $html .= '
                <a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $html .= '</td>';
                    $html .= '<td class="text-center">';
                    $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
                else
                {
                    $html .= '<tr>';
                    $html .= '<td>';
                    $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                    $doc_url = Router::url('/', true) . "uploads/eforms/".$eformId."/".$value;
                    $doc_dir_data_key = $eformId;
                    $doc_dir_data_value = $value;
                    $html .= '<div class="col-sm-10"><a target="_blank" href="'.$doc_url.'"> Document </a>';
                    if(empty($docFieldHtml))
                    {
                        $html .= '<a href="javascript:void(0);" onclick="getDataFromDocument(\''.$doc_dir_data_key.'\',\''.$doc_dir_data_value.'\');"><i class="fa fa-play-circle-o" style="vertical-align: middle"></i></a>';
                    }

                    $html .= '</div>';
                    //$html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="expiry_date_'.$key.'" value="'.$value.'" />';
                    //$html .= '<input type="file" class="form-control" value="'.$value.'" readonly />';
                    $html .= '</td>';
                    $html .= '<td class="text-center">';
                    $html .= '<div class="col-sm-10">
                <input class="checkbox_display" '.$checked.' type="checkbox" style="margin-left: 12px;" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" /></div>';
                    $html .= '</td>';
                    $html .= '<td class="text-center">';
                    $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                    $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                    $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                    $html .= '
                <a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                    $html .= '</td>';
                    $html .= '<td class="text-center">';
                    $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }




            } else if ( $keytype == 'radio') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<div class="custom-control custom-radio">';                
                $html .= '<div class="col-sm-10">
                <input type="radio" name="active-status" id="activeStatus" class="custom-control-input" checked disabled></div>';
                $html .= '<label class="custom-control-label" for="activeStatus">'.$value.'</label>';
                $html .= '</div>';                
                //$html .= '<input type="radio" class="form-check-input" checked readonly />'.$value;
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else if ( $keytype == 'checkbox') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>'; 
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $explode_value =  $value;              
                if (strpos($value, ',') !== false) {
                    $explode_value = explode(",", $value);
                }
                $count_value = count($explode_value);
                if($count_value == 1) {
                    $html .= '<div class="custom-control custom-checkbox">';                
                    $html .= '<input type="checkbox" checked disabled name="checkbox-status" id="checkboxStatus" class="custom-control-input">';
                    $html .= '<label class="custom-control-label" for="checkboxStatus">'.$value.'</label>';
                    $html .= '</div>';   
                } else {
                    if($explode_value) {
                        foreach($explode_value as $key => $v) {
                            $html .= '<div class="custom-control custom-checkbox">';                
                            $html .= '<input type="checkbox" checked disabled name="checkbox-status" id="checkboxStatus" class="custom-control-input">';
                            $html .= '<label class="custom-control-label" for="checkboxStatus">'.$v.'</label>';
                            $html .= '</div>';  
                        }
                    }
                }
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else if ( $keytype == 'address' || $keytype == 'textarea') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<textarea col="3" class="form-control" readonly>'.$value.'</textarea>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<div class="col-sm-10">
                <input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" /></div>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else if ( $keytype == 'list' || $keytype == 'select') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<div class="col-sm-10"><select class="form-control" readonly>';
                $html .= '<option value="'.$value.'">'.$value.'</option>';
                $html .= '</select></div>'; 
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else if ( $keytype == 'phone') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<div class="col-sm-10">
                <input type="tel" class="form-control" value="'.$value.'" readonly /></div>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else if ( $keytype == 'number') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<div class="col-sm-10">
                <input type="number" class="form-control" value="'.$value.'" readonly /></div>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input class="checkbox_display" '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>';
            } else if ( $keytype == 'date') {
                $html .= '<tr>';
                $html .= '<td>';
                $html .= '<label class="col-sm-2 col-form-label">' . $labelname . '</label>';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][name]" id="name_'.$key.'" value="'.$value.'" />';
                $html .= '<div class="col-sm-10">
                <input type="date" class="form-control" value="'.$value.'" readonly /></div>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input '.$checked.' type="checkbox" name="field_name[' . $keyfields . '][verify_status]" id="verify_status_'.$key.'" />';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][expiry_date]" id="expiry_date_'.$key.'" value="'.$expiryDate.'" />';
                $html .= '<input type="hidden" name="field_name[' . $keyfields . '][d]" id="d'.$key.'" value="'.$id_verify.'" />';
                $html .= '<label class="col-form-label" id="expiry_date_label_'.$key.'">'.$expiryDate.'</label>';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseExpiryDate(\''.$key.'\');" class="btn btn-primary btn-sm ml-2">Add</a>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<a href="javascript:void(0);" onclick="getEformResponseVerify(\''.$id_verify.'\', \''.$erid_verify.'\');" class="btn btn-primary btn-sm">View</a>';
                $html .= '</td>';
                $html .= '</tr>'; 
            } 
        }

        if(!empty($docFieldHtml))
        {
            $html .= '<tr>';
            $html .= '<td colspan="4">';
            $html .= '<h5 class="col-sm-2">Document filed</h5>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= $docFieldHtml;
        }


        $html .= '<input type="hidden" id="akcessId" value="'.$akcessId.'">';

        $data['html'] = $html;
        $data['eformasfile_url'] = $eformasfile_url;
        $data['label'] = $formName;
        $data['customVerify'] = $status;
        $data['erid'] = $this->Global->userIdEncode($erid);
                
        $resultJ = json_encode($data);
        $this->response->type('json');
        $this->response->body($resultJ);
        return $this->response;
        
    }
        
    /**
     * View method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {

        $conn = ConnectionManager::get("default"); // name of your database connection        

        $eform = $this->Eform->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $this->loadModel('Fields');
        
        $fields = $this->Fields->find('all', array('conditions' => ['fk_eform_id' => $id, 'soft_delete' => 0]));
        
        $html = '';
        $html .= '<div class="form-group col-12">';
        $html .= '<div class="input-group">';
        
        foreach ($fields as $n) {
            
            $field_check = '0';
            $field_ids = $n->id;
            $field_name = $n->key;
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
                $html .= '<div class="form-group col-12"> ';
                if ($field_verified == 'yes') { 
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
                }
                $html .= '</div>';
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
                $html .= '<div class="form-group col-12"> ';
                if ($field_verified == 'yes') {
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
                 }
                $html .= '</div>';
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
                $html .= '<div class="form-group col-12"> ';
                if ($field_verified == 'yes') { 
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
                }
                $html .= '</div>';
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
                $html .= '<div class="form-group col-12">';
                if ($field_verified == 'yes') {
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
                }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            if (  $field_type == 'address' || $field_type == 'textarea' ) {
           
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<textarea data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_name . '" col="3" class="form-control"></textarea>';
                $html .= '</div>';
                $html .= '<div class="form-group col-12">';
                if ($field_verified == 'yes') { 
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
                   }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'list' || $field_type == 'select' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<select data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';

                $explode = explode( ",", $field_items );

                $html .= '<option value="">Select ' . $field_label . '</option>';

                foreach ( $explode as $key => $value ) {

                    $html .= '<option value="' . $value . '">' . $value . '</option>';
                }
                
                $html .= '</select>'; 
                $html .= '</div>';
                $html .= '<div class="form-group col-12">';
                if ($field_verified == 'yes') {
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
                }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'phone' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<input type="tel" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';
                $html .= '</div>';
                $html .= '<div class="form-group col-12">';
                if ($field_verified == 'yes') { 
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
                }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'number' ) {

                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<input type="number" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . '" class="form-control">';
                $html .= '</div>';
                $html .= '<div class="form-group col-12">';
                if ($field_verified == 'yes') {
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
                }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            if ( $field_type == 'date' ) {
                $html .= '<div class="col-12 col-lg-6 ' . $field_ids . '">';
                $html .= '<div class="row">';
                $html .= '<div class="form-group col-12">';
                $html .= '<label>' . $field_label . '</label>';
                $html .= '<input type="date" data-instructions="' . $field_label_instructions . '" data-name="' . $field_name . '" data-type="' . $field_type . '" data-isVisible="' . $field_isVisible . '" data-section="' . $field_section . '" data-verification-grade="' . $field_verification_grade . '" data-fieldver="' . $field_verified . '" data-field_mandate="' . $field_mandate . '" id="field_' . $field_name . '" name="field_name[' . $field_name . '][]" data-ids="' . $field_ids . '" data-items="' . $field_items . '" placeholder="' . $field_label . ' ( YYYY-MM-DD )" class="form-control">';
                $html .= '</div>';
                $html .= '<div class="form-group col-12">';
                if ($field_verified == 'yes') {
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
                }
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }                                               
        
        $html .= '</div>';
        $html .= '</div>';
        
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
    public function edit($id = null) {
        
        $conn = ConnectionManager::get("default"); // name of your database connection        

        $eform = $this->Eform->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $this->loadModel('Users');
        
        $users = $this->Users->find('all', array('conditions' => ['usertype' => 'Student', 'status' => 1]));
        
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
        
        $this->set(compact('eform', 'id', 'eid', 'fields', 'countries', 'users', 'dataAdditional'));
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
        
        $storeinprofile = $this->request->data('storeinprofile');
        
        $isclientInvitationEform = $this->request->data('isclientInvitationEform');
        
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
            'isclientInvitationEform'   => $isclientInvitationEform
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
            'isclientInvitationEform'   => $eformDetails_before->isclientInvitationEform
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
                $key = isset($fieldsNames->key) ? $fieldsNames->key : ''; 
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
                    'key' => $key,
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
                $key = isset($fieldsOptionsDetails_befores->key) ? $fieldsOptionsDetails_befores->key : ''; 
                $lable = isset($fieldsOptionsDetails_befores->lable) ? $fieldsOptionsDetails_befores->lable : ''; 
                $uid = isset($fieldsOptionsDetails_befores->uid) ? $fieldsOptionsDetails_befores->uid : ''; 
                $checked = isset($fieldsOptionsDetails_befores->checked) ? $fieldsOptionsDetails_befores->checked : ''; 

                $fields_options_array_before[] = array(
                    'fk_eform_id' => $fk_eform_id,
                    'fk_fields_id' => $fk_fields_id,
                    'key' => $key,
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
            $key = isset($fieldsNames['key']) ? $fieldsNames['key'] : ''; 
            
            $key_value = $key;
            if (strpos($key, '_') !== false) { 
                $key_value_explode = explode("_", $key);                
                $key_value = $key_value_explode[0];
            }
            
            $fields_array_after[] = array(
                'fk_eform_id' => $id,
                'labelname' => $name,
                'key' => $key_value,
                'keytype' => $type,
                'file_verified' => $fieldver,
                'options' => $items,
                'verification_grade' => $verification_grade,
                'section_id' => $section,
                'instructions' => $instructions,
                'is_mandatory' => $field_mandate,
                'signature_required' => $signature_required
            );
            
            $sql = "INSERT INTO `fields` (`fk_eform_id`, `labelname`, `key`, `keytype`, `file_verified`, `options`, `verification_grade`, `section_id`, `instructions`, `is_mandatory`, `signature_required`) VALUES (".$id.",'".$name."','".$key_value."','".$type."','".$fieldver."','".$items."','".$verification_grade."','".$section."','".$instructions."','".$field_mandate."','".$signature_required."')";
            
            $saveFields = $conn->execute($sql);  
            
            $sql_last_query_fields = "SELECT id FROM `fields` ORDER BY ID DESC LIMIT 0,1";

            $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

            $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

            $lastfieldsInsertedId = $sql_last_fields_id['id'];
            
            $items_array = explode(",", $items);
            
            foreach($items_array as $items_arrays) {
                if($items_arrays) {                      
                    $optionsid = $this->Global->random_string('alnum', 12);
                    
                    $sql_options = "INSERT INTO `fields_options` (`fk_eform_id`, `fk_fields_id`, `key`, `lable`, `uid`, `checked`) VALUES (".$id.",".$lastfieldsInsertedId.",'".strtolower($items_arrays)."','".$items_arrays."','".$optionsid."','false')";
            
                    $conn->execute($sql_options);       
                    
                    $fields_options_array_after[] = array(
                        'fk_eform_id' => $fk_eform_id,
                        'fk_fields_id' => $lastfieldsInsertedId,
                        'key' => strtolower($items_arrays),
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
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $ackess = isset($_POST['ackess']) ? $_POST['ackess'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';      
        $eid = isset($_POST['eid']) ? $_POST['eid'] : '';      
        $inlineRadioOptions = isset($_POST['inlineRadioOptions']) ? $_POST['inlineRadioOptions'] : '';
        
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
        
        $additional_notification = $eform->additional_notification;
        
        $isAdditionalNotification = $eform->isAdditionalNotification;
        
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
            $type = isset($fieldsInformations->keytype) ? $fieldsInformations->keytype : ''; 
            $isVisible = isset($fieldsInformations->isVisible) ? $fieldsInformations->isVisible : ''; 
            $section_id = isset($fieldsInformations->section_id) ? $fieldsInformations->section_id : ''; 
            $verification_grade = isset($fieldsInformations->verification_grade) ? $fieldsInformations->verification_grade : ''; 
            $fieldver = isset($fieldsInformations->file_verified) ? $fieldsInformations->file_verified : ''; 
            $field_mandate = isset($fieldsInformations->is_mandatory) ? $fieldsInformations->is_mandatory : ''; 
            $signature_required = isset($fieldsInformations->signature_required) ? $fieldsInformations->signature_required : 'no'; 
            $ids = isset($fieldsInformations->ids) ? $fieldsInformations->ids : ''; 
            $items = isset($fieldsInformations->options) ? $fieldsInformations->options : ''; 
            $key = isset($fieldsInformations->key) ? $fieldsInformations->key : ''; 
            
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
                $key = $fieldsOptionsInformations->key;

                $array_items .= '{
                    "checked": '.$checked.',
                    "key": "'.strtolower($key).'",
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
                "keytype": "'.$type.'",
                "signature_required": "'.$signature_required.'",
                "file_verified": "'.$fieldver.'",
                "verification_grade": "'.$verification_grade.'",
                "section_id": "",
                "section_color": "",
                "sectionfields": {}
            },';
            
        }
                
        $eformFieldText = rtrim($eformFieldText, ',');
               
        $response_token = $this->Global->getToken();
           
        $token = $response_token;  
        
        if(isset($type) && $inlineRadioOptions == 'phone') {
           
            $response_token = $this->Global->getToken();
           
            $token = $response_token;
           
            $origin_array = array(
                'authorization: ' . $token,
                'apikey: ' . $api,
                'origin: ' . $origin_url
            );
            
            $data_array = array(
                'countryCode'    => $country_code,
                'phone'    => $phone,
                'msg'    => "Hi, Please check your eform for details and download. " . $idcard->fileUrl
            );
            
            $method = "POST";

            $response_data = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);
            
            $response_phone_Data = json_decode($response_data);
           
            $send_status = "error";
            if($response_phone_Data->status == 1) {
                $send_status = "success";
                
                $this->loadModel('SendData');
            
                $sendData = $this->SendData->newEntity();
                $sendData->fk_idcard_id = $idcard->id;
                $sendData->phone_no = $phone;
                $sendData->country_code = $country_code;
                $sendData->send_type = $inlineRadioOptions;
                $sendData->send_status = $send_status;

                $savesendData = $this->SendData->save($sendData);
                
                $saveSendId = $savesendData->id;

                $after = array(
                    'user_id' => $user_id,
                    'role_id' => $role_id,       
                    'fk_user_id' => $fk_users_id,
                    'fk_idcard_id' => $idcard->id,
                    'phone_no' => $phone,
                    'country_code' => $country_code,
                    'send_type' => $inlineRadioOptions,
                    'send_status' => $send_status
                );

                $this->Global->auditTrailApi($saveSendId, 'senddata', 'phoneEform', null, $after);

                $data['message'] = "success";
                $data['data'] = "The eform has been sent successfully.";
            } else {
                $data['message'] = "type is not set!";
            }
        } 
        else if(isset($type) && $inlineRadioOptions == 'email') {
           
            $response_token = $this->Global->getToken();
           
            $token = $response_token;
           
            $origin_array = array(
                'authorization: ' . $token,
                'apikey: ' . $api,
                'origin: ' . $origin_url
            );
            
            $data_array = array(
                'to'    => $email,
                'subject'    => "Send Eform",
                'text' => "Send Eform",
                'html'    => "<p>Hi ".$email.", </p> <p>Please check your Eform for details and download. </p> <p><a href='" . $idcard->fileUrl . "'>Download Here</a>"
            );
            
            $method = "POST";

            $response_data = $this->Global->curlGetPost($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array);
            
            $response_phone_Data = json_decode($response_data);
           
            $send_status = "error";
            if($response_phone_Data->status == 1) {
                $send_status = "success";
                
                $this->loadModel('SendData');
            
                $sendData = $this->SendData->newEntity();
                $sendData->fk_idcard_id = $idcard->id;
                $sendData->email = $email;
                $sendData->send_type = $inlineRadioOptions;
                $sendData->send_status = $send_status;

                $savesendData = $this->SendData->save($sendData);
                
                $saveSendId = $savesendData->id;

                $after = array(
                    'user_id' => $user_id,
                    'role_id' => $role_id,       
                    'fk_user_id' => $fk_users_id,
                    'fk_idcard_id' => $idcard->id,
                    'email' => $phone,
                    'send_type' => $inlineRadioOptions,
                    'send_status' => $send_status
                );

                $this->Global->auditTrailApi($saveSendId, 'senddata', 'emailEform', null, $after);

                $data['message'] = "success";
                $data['data'] = "The eform has been sent successfully.";
            } else {
                $data['message'] = "type is not set!";
            }
        }  
        else if(isset($type) && $inlineRadioOptions == 'ackess') {
            
            foreach($ackess as $akcessIds) {
                
                $origin_array = array(
                    'authorization: ' . $token,
                    'apikey: ' . $api,
                    'origin: ' . $origin_url,
                    'Content-Type: application/x-www-form-urlencoded'
                );

                $data_array = '?akcessId='.$akcessIds;

                $type_method = 'users/akcessId';

                $method = "GET";

                //$response_data_akcess_verify = $this->Global->curlGetPost($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);

                //$verify_check = json_decode($response_data_akcess_verify);

                //if(isset($verify_check->data->akcessId) && $verify_check->data->akcessId != "") {

                     $data_array = '{
                        "eformconfig": {
                            "akcessID": '.$array_akcesss_id.',
                            "UserHost": "AKcess-dev",
                            "formName": "'.$formName.'",
                            "description": "'.$description.'",
                            "eform": ['.$eformFieldText.'],
                            "eformId": "'.$eform->eformid.'",
                            "signature": "'.$signature.'",
                            "logo": "'.$src.'",
                            "pulldata": "'.$pulldata.'",
                            "facematch": "'.$facematch.'"
                        }
                    }'; 

                    $origin_array = array(
                        'Content-Type: application/json',
                        'authorization: ' . $token,
                        'apiKey: ' . $api,
                        'Origin: ' . $origin_url
                    );        

                    $type_method = 'incoming-eforms';

                    $method = "POST";   

                    $response_data_akcess = $this->Global->curlGetPostEform($method, $type_method, $api, $origin_url, $api_url, $data_array, $origin_array);        

                    $response_akcess_Data = json_decode($response_data_akcess);                

                    $send_status = "error";

                    if($response_akcess_Data->status == 1) {

                        $send_status = "success";

                        $this->loadModel('SendData');

                        $sendData = $this->SendData->newEntity();
                        $sendData->fk_eform_id = $id;
                        $sendData->ackessID = $akcessIds;
                        $sendData->response_id = $response_akcess_Data->data->_ids[0];
                        $sendData->send_type = $inlineRadioOptions;
                        $sendData->send_status = $send_status;

                        $savesendData = $this->SendData->save($sendData);

                        $saveSendId = $savesendData->id;

                        $after = array(
                            'user_id' => $user_id,
                            'role_id' => $role_id,       
                            'fk_user_id' => $fk_users_id,
                            'fk_eform_id' => $id,
                            'ackessID' => $akcessIds,
                            'response_id' => $response_akcess_Data->data->_ids[0],
                            'send_type' => $inlineRadioOptions,
                            'send_status' => $send_status
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
                        $data['message'] = "type is not set!";
                    }
                //} else {
                //    $data['message'] = "Akcess ID is not found!";
                //}
            }
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
                $key = isset($fieldsNames->key) ? $fieldsNames->key : ''; 
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
                    'key' => $key,
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
                $key = isset($fieldsOptionsDetails_befores->key) ? $fieldsOptionsDetails_befores->key : ''; 
                $lable = isset($fieldsOptionsDetails_befores->lable) ? $fieldsOptionsDetails_befores->lable : ''; 
                $uid = isset($fieldsOptionsDetails_befores->uid) ? $fieldsOptionsDetails_befores->uid : ''; 
                $checked = isset($fieldsOptionsDetails_befores->checked) ? $fieldsOptionsDetails_befores->checked : ''; 

                $fields_options_array_before[] = array(
                    'fk_eform_id' => $fk_eform_id,
                    'fk_fields_id' => $fk_fields_id,
                    'key' => $key,
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
            $key = isset($fieldsNames['key']) ? $fieldsNames['key'] : ''; 
            
            $key_value = $key;
            if (strpos($key, '_') !== false) { 
                $key_value_explode = explode("_", $key);                
                $key_value = $key_value_explode[0];
            }
            
            $fields_array_after[] = array(
                'fk_eform_id' => $id,
                'labelname' => $name,
                'key' => $key_value,
                'keytype' => $type,
                'file_verified' => $fieldver,
                'options' => $items,
                'verification_grade' => $verification_grade,
                'section_id' => $section,
                'instructions' => $instructions,
                'is_mandatory' => $field_mandate,
                'signature_required' => $signature_required
            );
            
            $sql = "INSERT INTO `fields` (`fk_eform_id`, `labelname`, `key`, `keytype`, `file_verified`, `options`, `verification_grade`, `section_id`, `instructions`, `is_mandatory`, `signature_required`) VALUES (".$id.",'".$name."','".$key_value."','".$type."','".$fieldver."','".$items."','".$verification_grade."','".$section."','".$instructions."','".$field_mandate."','".$signature_required."')";
            
            $saveFields = $conn->execute($sql);  
            
            $sql_last_query_fields = "SELECT id FROM `fields` ORDER BY ID DESC LIMIT 0,1";

            $sql_last_query_fields_id = $conn->execute($sql_last_query_fields);

            $sql_last_fields_id = $sql_last_query_fields_id->fetch('assoc');

            $lastfieldsInsertedId = $sql_last_fields_id['id'];
            
            $items_array = explode(",", $items);
            
            foreach($items_array as $items_arrays) {
                if($items_arrays) {                      
                    $optionsid = $this->Global->random_string('alnum', 12);
                    
                    $sql_options = "INSERT INTO `fields_options` (`fk_eform_id`, `fk_fields_id`, `key`, `lable`, `uid`, `checked`) VALUES (".$id.",".$lastfieldsInsertedId.",'".strtolower($items_arrays)."','".$items_arrays."','".$optionsid."','false')";
            
                    $conn->execute($sql_options);       
                    
                    $fields_options_array_after[] = array(
                        'fk_eform_id' => $fk_eform_id,
                        'fk_fields_id' => $lastfieldsInsertedId,
                        'key' => strtolower($items_arrays),
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

    /**
     * Delete method
     *
     * @param string|null $id IDCard id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null, $userId = null) {
        $this->request->allowMethod(['post', 'delete']);
        $doc = $this->IDCard->get($id);

        $updateIDCard = $this->IDCard->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id,
                'fk_users_id' => $userId
            ]
        );
         
        if ($updateIDCard) {            
            $this->Flash->success(__('The eform has been deleted.'));
        } else {
            $this->Flash->error(__('The eform could not be deleted. Please, try again.'));
        }

        return $this->redirect(['controller' => 'Users', 'action' => 'view', $userId]);
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
        
        $saveSendId = $savesendData->id;
        
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

    public function postVerifyData() {        
        
        $fieldName = $_POST['field_name'];
        $customVerify = $_POST['customVerify'];

        
        $conn = ConnectionManager::get("default"); // name of your database connection   

        $fieldNamesArray = array();

        $profile_field_string = "[";
        $expiry_date_field_string = "[";

        foreach($fieldName as $key => $fieldNames) {
            $profile_field_string .= '"'.$key.'"';
            $profile_field_string .= ",";

            $expiry = date("Y-m-d\TH:i:s.000\Z", strtotime($fieldNames['expiry_date']));
            $expiry_date_field_string .= '"'.$expiry.'"';
            $expiry_date_field_string .= ",";

        }
        $profile_field_string = rtrim($profile_field_string,',');
        $profile_field_string .= "]";

        $expiry_date_field_string = rtrim($expiry_date_field_string,',');
        $expiry_date_field_string .= "]";

        $this->loadModel('Users');
        
        $user_id = $this->Auth->user( 'id' );
        
        $user = $this->Users->get($user_id, [
            'conditions' => []
        ]);

        $sessionId = $user->akcessId;
        
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;

        $eform_response_id = isset($_POST['erid']) ? $this->Global->userIdDecode($_POST['erid']) : "";

        $eform_response_data = $conn->execute("SELECT fr.*, er.formName, er.akcessId, er.eformId, er.id as erid,er.mobile_local_id FROM fieldsresponse as fr
        LEFT JOIN eformresponse as er ON fr.fk_eformresponse_id = er.id
        WHERE er.id = '".$eform_response_id."'");

        $eform_response = $eform_response_data->fetch('assoc');

        $userAkcessId = isset($eform_response['akcessId']) ? $eform_response['akcessId'] : '';
        $formName = isset($eform_response['formName']) ? $eform_response['formName'] : '';
        $erid = isset($eform_response['erid']) ? $eform_response['erid'] : '';
        $eformId = isset($eform_response['eformId']) ? $eform_response['eformId'] : '';
        $mobile_local_id = isset($eform_response['mobile_local_id']) ? $eform_response['mobile_local_id'] : '';        

        $response_token = $this->Global->getToken();
           
        $token = $response_token;
        
        $origin_array = array(
            'authorization:'.$token,
            'apikey:'.$api,
            'origin:'.$origin_url
        );

        $verifiers_data = $conn->execute("SELECT * FROM verifiers");
            
        $verifiers = $verifiers_data->fetch('assoc');

        $verifierAkcessId = $verifiers['verifierAkcessId'];
        $verifierName = $verifiers['verifierName'];
        $fileName_ak = $verifiers['fileName'];
        $akData = $verifiers['akData']; 
        $verifierGrade = $verifiers['verifierGrade']; 
        
        $data['message'] = "Some Error in Eform Response.";
        $status_response =  false;
        $data['status'] = $status_response;

        //pending - alive - Raj & Dipal
        //return - reject

        if(isset($customVerify) && $customVerify == "pending"){

            $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                
            $conn->execute($sql_trail);

            foreach($fieldName as $key => $fieldNames) {
            
                $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
    
                if($id != '0') {

//                    $expiryDate = date('Y-m-d',$expiryDate);

                    $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
            
                    $conn->execute($sql_update); 
                }
            }

            $data['status'] = true;
            $data['message'] = "Eform response is pending";
        }
        else if(isset($customVerify) && $customVerify == "alive"){

            $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                
            $conn->execute($sql_trail);

            foreach($fieldName as $key => $fieldNames) {
            
                $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
    
                if($id != '0') {

//                    $expiryDate = date('Y-m-d',$expiryDate);

                    $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
            
                    $conn->execute($sql_update); 
                }
            }

            $data['status'] = true;
            $data['message'] = "Eform response is alive";
        }
        else if(isset($customVerify) && $customVerify == "accept"){

            $this->loadModel('FieldsResponse');        

            $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

            $results = $conn->execute($query_response);

            $fieldsInformation_query = $results->fetchAll('assoc');
           
            $eformFields = array();
            
            $eformFieldText = array();

            $fields_form = array();
            
            foreach($fieldsInformation_query as $key => $fieldsInformations){
            
                $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : '';
                $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
//                $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : 'false';
                $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                
                $key_value = $keyfields;
                if (strpos($keyfields, '_') !== false) { 
                    $key_value_explode = explode("_", $keyfields);                
                    $key_value = $key_value_explode[0];
                }
            
                $this->loadModel('FieldsOptions');        
            
                $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                    'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                ]);
                
                //$array_items = '';
                
                $array_items = array();

                foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                    
                    $optionsid = $fieldsOptionsInformations->uid;
                    $checked = $fieldsOptionsInformations->checked;
                    $lable = $fieldsOptionsInformations->lable;
                    $key = $fieldsOptionsInformations->keyfields;

                    $array_items[] = [
                        "checked"=> $checked,
                        "keyfields"=> strtolower($key),
                        "lable"=> $lable,
                        "uid"=> $optionsid
                    ];
                }
             
                $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                
                $eformFieldText[] = [                
                    "docuementType" => $docuementType,
                    "expiryDate" => $expiryDate,
                    "isDocFetched" => $isDocFetched,
                    "isverified" => $isverified,
                    "key" => $keyfields,
                    "keytype" => $keytype,
                    "labelname" => $labelname,
                    "options" => $options,
                    "optionsData" => $array_items,
                    "sectionfields" => $sectionfields,
                    "value" => $value,
                    "val" => $value,
                    "verify_status" => $verify_status,
                    //"_id" => $field_id                   
                ];

                $fields_form[] = $field_id;
                
            }
                    
            //$eformFieldText = rtrim($eformFieldText, ',');
            //$fields_form = rtrim($fields_form, ',');

            $dir  = WWW_ROOT . "/img/logo.png";
            // A few settings
            $img_file = $dir;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($img_file));

            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

            $date = date("Y-m-d\TH:i:s.000\Z");

            $data_array_eform_response =array(
                "eformsFields" => $eformFieldText,
                "fields_form"  => $fields_form,
                "eformStatus" =>$customVerify,
                "akcessId"  => $userAkcessId,
                "formName" => $formName,
                "eformId" => $mobile_local_id,
                "eFormResponseId" =>$erid,
                "response_msg"=>"EForm response has been accepted successfully",
                "date" => $date,
                "logo" => $src,
                "name" => ORIGIN_URL,
                "verifier_name" => $verifierName,
                "verifier_grade" =>$verifierGrade
            );               
           
            $response_data_akcess_eform_response = "";

            $type = 'eform-response-verification';
        
            $method = "POST";

            if($userAkcessId != "") { 

                $origin_array_response = array(
                    'authorization:'.$token,
                    'apikey:'.$api,
                    'origin:'.$origin_url,
                    'Content-Type: application/json'
                );

                // print_R($method);
                // print_R($type);
                // print_R($api);
                // print_R($origin_url);
                // print_R($api_url);
                // print_R(json_encode($data_array_eform_response));
                // print_R($origin_array);
                

                $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);

                //print_R($response_data_akcess_eform_response);

                //exit;
            }
            
            if($response_data_akcess_eform_response->status == 1) {

                $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;
               
                $type = 'field/Verify';
            
                $method = "POST";

                $response_data_akcess_verify = "";

                $data_array_verify = array(
                    'userAkcessId'  => $userAkcessId, 
                    'verifierAkcessId' => $verifierAkcessId,
                    'sessionId' => $sessionId,
                    'profileField'  => $profile_field_string,
                    'expiryDate' => $expiry_date_field_string,
                    'fileName'      => $fileName_ak,
                    'akData'        => $akData
                );

                // print_R($method);
                // print_R($type);
                // print_R($api);
                // print_R($origin_url);
                // print_R($api_url);
                // print_R($data_array_verify);
                // print_R($origin_array);
                

                if($userAkcessId != "" && $profile_field_string != "") { 
                    $response_data_akcess_verify = $this->Global->curlPostEformResponseVerifyField($method, $type, $api, $origin_url, $api_url, $data_array_verify, $origin_array);
                }

                $response = json_decode($response_data_akcess_verify);

                //print_R($response);
                //exit;

                if($response->statusCode == '200'){

                    $txId = 0;
                    $txId_array = json_decode($response->txId);
                    if (json_last_error() === 0 && isset($txId_array->txId) && $txId_array->txId)
                    {
                        $txId = $txId_array->txId;
                    }
                    else
                    {
                    if (!empty($response->txId->txId) && $response->txId->txId != "") {
                        if(json_decode($response->txId)->success == 1) {
                            $txId = json_decode($response->txId)->txId;
                        } else {

                            $data['status'] = false;
                            $data['message'] = "User id doesn't exist";

                            $res = json_encode($data);
                            $this->response->type('json');
                            $this->response->body($res);

                            return $this->response;
                            
                        }                           
                    } else {
                        $txId = $response->txId;
                    }
                    }


                    $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";
                    
                    $conn->execute($sql_response);

                    $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                    
                    $conn->execute($sql_trail);

                    foreach($fieldName as $key => $fieldNames) {
                    
                        $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                        $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                        $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
            
                        if($id != '0') {
                            //$expiryDate = date('Y-m-d H:i:s',$expiryDate);

                            $sql_update = 'UPDATE `fieldsresponse` SET `verify_status`="'.$verify_status.'",`expiryDate`="'.$expiryDate.'"  WHERE `id` = '.$id;
                    
                            $conn->execute($sql_update); 
                        }
                    }

                    $status_response = $response->status;

                    $data['status'] = true;
//                    $data['message'] = "Eform Response Accepted";
                    $data['message'] = 'EForm response has been accepted successfully';

                } 
            }
            
        }
        else if(isset($customVerify) && $customVerify == "verify and accept"){

            if(!empty($fieldName))
            {
                foreach($fieldName as $key => $fieldNames) {

                    $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                    $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                    $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';

                    if($id != '0') {
                        $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;

                        $conn->execute($sql_update);
                    }
                }
            }



            $this->loadModel('FieldsResponse');        

            $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

            $results = $conn->execute($query_response);

            $fieldsInformation_query = $results->fetchAll('assoc');
           
            $eformFields = array();
            
            $eformFieldText = array();

            $fields_form = array();

            foreach($fieldsInformation_query as $key => $fieldsInformations){ 
            
                $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : ''; 
                $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
                $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                
                $key_value = $keyfields;
                if (strpos($keyfields, '_') !== false) { 
                    $key_value_explode = explode("_", $keyfields);                
                    $key_value = $key_value_explode[0];
                }
            
                $this->loadModel('FieldsOptions');        
            
                $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
//                    'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                    'conditions' => ['fk_fields_id' => $field_id, 'soft_delete' => 0]

                ]);
                
                //$array_items = '';
                
                $array_items = array();

                foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                    
                    $optionsid = $fieldsOptionsInformations->uid;
                    $checked = $fieldsOptionsInformations->checked;
                    $lable = $fieldsOptionsInformations->lable;
                    $key = $fieldsOptionsInformations->keyfields;

                    $array_items[] = [
                        "checked"=> $checked,
                        "keyfields"=> strtolower($key),
                        "lable"=> $lable,
                        "uid"=> $optionsid
                    ];
                }
             
                $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                
                $eformFieldText[] = [                
                    "docuementType" => $docuementType,
                    "expiryDate" => $expiryDate,
                    "isDocFetched" => $isDocFetched,
                    "isverified" => $isverified,
                    "key" => $keyfields,
                    "keytype" => $keytype,
                    "labelname" => $labelname,
                    "options" => $options,
                    "optionsData" => $array_items,
                    "sectionfields" => $sectionfields,
                    "value" => $value,
                    "val" => $value,
                    "verify_status" => $verify_status,
                    //"_id" => $field_id                   
                ];

                $fields_form[] = $field_id;
                
            }
                    
            //$eformFieldText = rtrim($eformFieldText, ',');
            //$fields_form = rtrim($fields_form, ',');

            $dir  = WWW_ROOT . "/img/logo.png";
            // A few settings
            $img_file = $dir;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($img_file));

            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

            $date = date("Y-m-d\TH:i:s.000\Z");

            $data_array_eform_response =array(
                "eformsFields" => $eformFieldText,
                "fields_form"  => $fields_form,
                "eformStatus" =>$customVerify,
                "akcessId"  => $userAkcessId,
                "formName" => $formName,
                "eformId" => $mobile_local_id,
                "eFormResponseId" =>$erid,
                "response_msg"=>"EForm response has been verified and accepted successfully",
                "date" => $date,
                "logo" => $src,
                "name" => ORIGIN_URL,
                "verifier_name" => $verifierName,
                "verifier_grade" =>$verifierGrade
            );               
           
            $response_data_akcess_eform_response = "";

            $type = 'eform-response-verification';
        
            $method = "POST";
           
            if($userAkcessId != "") { 

                $origin_array_response = array(
                    'authorization:'.$token,
                    'apikey:'.$api,
                    'origin:'.$origin_url,
                    'Content-Type: application/json'
                );
                

                $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);
            }
            if($response_data_akcess_eform_response->status == 1) {

                $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;
               
                $type = 'field/Verify';
            
                $method = "POST";

                $response_data_akcess_verify = "";

                $data_array_verify = array(
                    'userAkcessId'  => $userAkcessId, 
                    'verifierAkcessId' => $verifierAkcessId,
                    'sessionId' => $sessionId,
                    'profileField'  => $profile_field_string,
                    'expiryDate' => $expiry_date_field_string,
                    'fileName'      => $fileName_ak,
                    'akData'        => $akData
                );

                // print_r($method);
                // print_r($type);
                // print_r($api);
                // print_r($origin_url);
                // print_r($api_url);
                // print_r($data_array_verify);
                // print_r($origin_array);

                if($userAkcessId != "" && $profile_field_string != "") { 
                    $response_data_akcess_verify = $this->Global->curlPostEformResponseVerifyField($method, $type, $api, $origin_url, $api_url, $data_array_verify, $origin_array);
                }

                //print_R($response_data_akcess_verify);exit;


                $response = json_decode($response_data_akcess_verify);
            
                if($response->statusCode == '200')
                {
                    $txId = 0;
                    $txId_array = json_decode($response->txId);
                    if (json_last_error() === 0 && isset($txId_array->txId) && $txId_array->txId)
                    {
                        $txId = $txId_array->txId;
                    }
                    else
                    {
                    if (!empty($response->txId->txId) && $response->txId->txId != "") {
                        $txId = $response->txId->txId;
                    } else {
                        $txId = $response->txId;
                    }
                    }

                    $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";

                    $conn->execute($sql_response);

                    $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                    
                    $conn->execute($sql_trail);

                    foreach($fieldName as $key => $fieldNames) {

                        $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                        $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                        $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';

                        if($id != '0') {
                            //$expiryDate = date('Y-m-d',$expiryDate);

                            $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;

                            $conn->execute($sql_update);
                        }
                    }

                    $status_response = $response->status;

                    $data['status'] = true;
//                    $data['message'] = "Eform Response verify and accept";
                    $data['message'] = 'EForm response has been verified and accepted successfully';

                } 
            }

        }
        else if(isset($customVerify) && $customVerify == "create staff"){

            $this->loadModel('Users');
            
            $filedArray = array();

            foreach($fieldName as $key => $fieldNames) {
                
                $name = isset($fieldNames['name']) ? $fieldNames['name'] : '';

                $filedArray[$key] = $name;    
            }

            $usertype = "Staff";            
            $firstname = isset($filedArray['firstname']) ? $filedArray['firstname'] : "";
            $lastname = isset($filedArray['lastname']) ? $filedArray['lastname'] : "";
            $name = $firstname . " " . $lastname;
            $email = isset($filedArray['email']) ? $filedArray['email'] : "";
            if(isset($filedArray['mobile'])) {
                $mobileNumber = $filedArray['mobile'];
            } else if(isset($filedArray['mobile number'])) {
                $mobileNumber = $filedArray['mobile number'];
            }
            $dob = isset($filedArray['dateofbirth']) ? date("Y-m-d",strtotime($filedArray['dateofbirth'])) : "";
            $city = isset($filedArray['birthplace']) ? $filedArray['birthplace'] : "";$filedArray['birthplace'];
            $gender = isset($filedArray['gender']) ? $filedArray['gender'] : "";$filedArray['gender'];
            $address = isset($filedArray['address']) ? $filedArray['address'] : "";$filedArray['address'];            
            $active = isset($filedArray['active']) ? $filedArray['active'] : "";$filedArray['active'];
            $staff_type = isset($filedArray['staff type']) ? $filedArray['staff type'] : "";
            $countryofresidence = isset($filedArray['countryofresidence']) ? $filedArray['countryofresidence'] : "";
            
            $query_response_countries = "SELECT * FROM countries WHERE country_name = '".$countryofresidence."'";

            $results_countries = $conn->execute($query_response_countries);

            $countries_query = $results_countries->fetch('assoc');

            $country = $countries_query['id'];
            $akcessId = isset($filedArray['akcess id']) ? $filedArray['akcess id'] : "";

            if(!empty($akcessId) && $akcessId != ""){
                $akcessId = $akcessId;
            } else {
                $akcessId = $userAkcessId;
            }

            $check_akcessId = 0;
           
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');

            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                $check_akcessId = 1;
            }

            if($check_akcessId == 1) {
                $append_string = "";
                $comma = "";                
                if($check_akcessId == 1){
                    $append_string .= $comma . " Akcess Id";
                }                               
                //$data['status'] = $status_response;
                //$data['message'] = "This " . $append_string . " is already exists. Please, try again.";
            }

            $check_akcessId = 0;

            if($check_akcessId == 0) {

                $this->loadModel('FieldsResponse');        

                $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

                $results = $conn->execute($query_response);

                $fieldsInformation_query = $results->fetchAll('assoc');
            
                $eformFields = array();
                
                $eformFieldText = array();

                $fields_form = array();
                
                foreach($fieldsInformation_query as $key => $fieldsInformations){
                
                    $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                    $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                    $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                    $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                    $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                    $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                    $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                    $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : '';
                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
//                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : 'false';
                    $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                    $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                    $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                    $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                    $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                    $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                    $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                    $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                    $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                    $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                    $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                    
                    $key_value = $keyfields;
                    if (strpos($keyfields, '_') !== false) { 
                        $key_value_explode = explode("_", $keyfields);                
                        $key_value = $key_value_explode[0];
                    }
                
                    $this->loadModel('FieldsOptions');        
                
                    $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                        'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                    ]);
                    
                    //$array_items = '';
                    
                    $array_items = array();

                    foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                        
                        $optionsid = $fieldsOptionsInformations->uid;
                        $checked = $fieldsOptionsInformations->checked;
                        $lable = $fieldsOptionsInformations->lable;
                        $key = $fieldsOptionsInformations->keyfields;

                        $array_items[] = [
                            "checked"=> $checked,
                            "keyfields"=> strtolower($key),
                            "lable"=> $lable,
                            "uid"=> $optionsid
                        ];
                    }
                
                    $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                    
                    $eformFieldText[] = [                
                        "docuementType" => $docuementType,
                        "expiryDate" => $expiryDate,
                        "isDocFetched" => $isDocFetched,
                        "isverified" => $isverified,
                        "key" => $keyfields,
                        "keytype" => $keytype,
                        "labelname" => $labelname,
                        "options" => $options,
                        "optionsData" => $array_items,
                        "sectionfields" => $sectionfields,
                        "value" => $value,
                        "val" => $value,
                        "verify_status" => $verify_status,
                        //"_id" => $field_id                   
                    ];

                    $fields_form[] = $field_id;
                    
                }
                        
                //$eformFieldText = rtrim($eformFieldText, ',');
                //$fields_form = rtrim($fields_form, ',');

                $dir  = WWW_ROOT . "/img/logo.png";
                // A few settings
                $img_file = $dir;
                // Read image path, convert to base64 encoding
                $imgData = base64_encode(file_get_contents($img_file));

                // Format the image SRC:  data:{mime};base64,{data};
                $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

                $date = date("Y-m-d\TH:i:s.000\Z");


                if($check_akcessId == 1){
                    $response_msg = "EForm response has been verified and updated staff record successfully.";
                } else {
                    $response_msg = "EForm response has been verified and created staff record successfully.";
                }

                $data_array_eform_response =array(
                    "eformsFields" => $eformFieldText,
                    "fields_form"  => $fields_form,
                    "eformStatus" =>$customVerify,
                    "akcessId"  => $userAkcessId,
                    "formName" => $formName,
                    "eformId" => $mobile_local_id,
                    "eFormResponseId" =>$erid,
                    "response_msg"=> $response_msg,
                    "date" => $date,
                    "logo" => $src,
                    "name" => ORIGIN_URL,
                    "verifier_name" => $verifierName,
                    "verifier_grade" =>$verifierGrade
                );               
            
                $response_data_akcess_eform_response = "";

                $type = 'eform-response-verification';
            
                $method = "POST";

                if($userAkcessId != "") { 

                    $origin_array_response = array(
                        'authorization:'.$token,
                        'apikey:'.$api,
                        'origin:'.$origin_url,
                        'Content-Type: application/json'
                    );

                    $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);
                }

                if($response_data_akcess_eform_response->status == 1) {

                    $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;    
                
                    $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                    $akcessIdDetails = $akcessId_data->fetch('assoc');
                    if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                        $check_akcessId = 1;
                    }

                    if($check_akcessId == 1){

                        $idSingleusertype = "";
                        if($check_akcessId == 1) {
                            $akcessId_data = $conn->execute("SELECT id,usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                            $akcessIdDetails = $akcessId_data->fetch('assoc');
                            $idSingle = $akcessIdDetails['id'];
                            $idSingleusertype = $akcessIdDetails['usertype'];
                        }

                        $user = $this->Users->get($idSingle, [
                            'conditions' => ['soft_delete' => 0]
                        ]);

                        $query_response_stafflist = "SELECT * FROM stafflist WHERE name = '".$staff_type."'";

                        $results_stafflist = $conn->execute($query_response_stafflist);

                        $stafflist_query = $results_stafflist->fetch('assoc');

                        $stafflist_staff_type = $stafflist_query['id'];

                        $user = $this->Users->patchEntity($user, $this->request->getData());

                        $utype = $idSingleusertype . "," .$usertype;
                        $string = implode(',', array_unique(explode(',', $utype)));
                        $string = trim($string,",");

                    
                        $user->name = $firstname . " " . $lastname;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->staff_type = $stafflist_staff_type;
                        $user->usertype = $string;

                        $this->loadModel('Users');

                        $result = $this->Users->save($user);

//                        $message = "Eform Response staff recored update successfully.";
                        $message = "Eform Response has been updated staff record successfully";

                    } else {

                        $user = $this->Users->newEntity();

                        $query_response_stafflist = "SELECT * FROM stafflist WHERE name = '".$staff_type."'";

                        $results_stafflist = $conn->execute($query_response_stafflist);

                        $stafflist_query = $results_stafflist->fetch('assoc');

                        $stafflist_staff_type = $stafflist_query['id'];
                    
                        $user->name = $firstname . " " . $lastname;
                        $user->akcessId = $akcessId;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->email = $email;
                        $user->mobileNumber = $mobileNumber;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->staff_type = $stafflist_staff_type;
                        $user->usertype = $usertype;
                        $user->loginOpt = 'pin';
                        $user->siteStatus = 'Development';
                        $user->status = 1;

                        $this->loadModel('Users');

                        $result = $this->Users->save($user);

                        $insertedId = $result->id;
                        
                        $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                        
                        $this->Users->updateAll(
                            [
                                'idcardno' => $idcard_randon
                            ], 
                            [
                                'id' => $insertedId
                            ]
                        );
                    
                        $after = array(                    
                            'akcessId' => $akcessId,
                            'idcardno' => $idcardno,
                            'user_id' => $user_id,
                            'role_id' => $role_id,            
                            'name' => $firstname . " " . $lastname,
                            'address' => $address,
                            'city' => $city,
                            'country' => $country,
                            'email' => $email,
                            'mobileNumber' => $mobileNumber,
                            'gender' => $gender,
                            'dob' => $dob,            
                            'active' => $active,
                            'staff_type' => $staff_type,
                            'usertype' => $usertype,
                            'loginOpt' => 'pin',
                            'siteStatus' => 'Development',
                            'status' => 1
                        );
                        
                        $this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);

//                        $message = "Eform Response staff recored created successfully.";
                        $message = "Eform Response has been created staff record successfully";
                    }
                    
                    if ($result) {  
                        
                        foreach($fieldName as $key => $fieldNames) {
                        
                            $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                            $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                            $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
                
                            if($id != '0') {
//                                $expiryDate = date('Y-m-d',$expiryDate);

                                $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
                        
                                $conn->execute($sql_update); 
                            }
                        }

                        $txId = 0;

                        $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";
    
                        $conn->execute($sql_response);
        
                        $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                        
                        $conn->execute($sql_trail);

                    }

                    $data['status'] = true;
                    $data['message'] = $message;
                }
    
            }

        }
        else if(isset($customVerify) && $customVerify == "create student"){
            $this->loadModel('Users');
            
            $filedArray = array();

            foreach($fieldName as $key => $fieldNames) {
                
                $name = isset($fieldNames['name']) ? $fieldNames['name'] : '';

                $filedArray[$key] = $name;    
            }
           
            $usertype = "Student";            
            $firstname = isset($filedArray['firstname']) ? $filedArray['firstname'] : "";
            $lastname = isset($filedArray['lastname']) ? $filedArray['lastname'] : "";
            $name = $firstname . " " . $lastname;
            $email = isset($filedArray['email']) ? $filedArray['email'] : "";

            if(isset($filedArray['mobile'])) {
                $mobileNumber = $filedArray['mobile'];
            } else if(isset($filedArray['mobile number'])) {
                $mobileNumber = $filedArray['mobile number'];
            }

            $dob = isset($filedArray['firstname']) ? date("Y-m-d",strtotime($filedArray['dateofbirth'])) : "";
            $city = isset($filedArray['birthplace']) ? $filedArray['birthplace'] : "";
            $gender = isset($filedArray['gender']) ? $filedArray['gender'] : "";
            $address = isset($filedArray['address']) ? $filedArray['address'] : "";         
            $active = isset($filedArray['active']) ? $filedArray['active'] : "";
            $faculty = isset($filedArray['faculty']) ? $filedArray['faculty'] : "";
            $courses = isset($filedArray['courses']) ? $filedArray['courses'] : "";
            $countryofresidence = $filedArray['countryofresidence'];
            
            $query_response_countries = "SELECT * FROM countries WHERE country_name = '".$countryofresidence."'";

            $results_countries = $conn->execute($query_response_countries);

            $countries_query = $results_countries->fetch('assoc');

            $country = $countries_query['id'];
            $akcessId = isset($filedArray['akcess id']) ? $filedArray['akcess id'] : "";

            if(!empty($akcessId) && $akcessId != "") {
                $akcessId = $akcessId;
            } else {
                $akcessId = $userAkcessId;
            }

            $check_akcessId = 0;            
           
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');

            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                $check_akcessId = 1;
            }

            if($check_akcessId == 1) {
                 $append_string = "";
                 $comma = "";                
                 if($check_akcessId == 1){
                     $append_string .= $comma . " Akcess Id";
                 }                               
                //  $data['status'] = $status_response;
                //  $data['message'] = "This " . $append_string . " is already exists. Please, try again.";
             }

            $check_akcessId = 0;

            if($check_akcessId == 0) {

                $this->loadModel('FieldsResponse');        

                $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

                $results = $conn->execute($query_response);

                $fieldsInformation_query = $results->fetchAll('assoc');
            
                $eformFields = array();
                
                $eformFieldText = array();

                $fields_form = array();
                
                foreach($fieldsInformation_query as $key => $fieldsInformations){
                
                    $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                    $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                    $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                    $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                    $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                    $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                    $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                    $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : '';
                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
//                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : 'false';
                    $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                    $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                    $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                    $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                    $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                    $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                    $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                    $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                    $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                    $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                    $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                    
                    $key_value = $keyfields;
                    if (strpos($keyfields, '_') !== false) { 
                        $key_value_explode = explode("_", $keyfields);                
                        $key_value = $key_value_explode[0];
                    }
                
                    $this->loadModel('FieldsOptions');        
                
                    $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                        'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                    ]);
                    
                    //$array_items = '';
                    
                    $array_items = array();

                    foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                        
                        $optionsid = $fieldsOptionsInformations->uid;
                        $checked = $fieldsOptionsInformations->checked;
                        $lable = $fieldsOptionsInformations->lable;
                        $key = $fieldsOptionsInformations->keyfields;

                        $array_items[] = [
                            "checked"=> $checked,
                            "keyfields"=> strtolower($key),
                            "lable"=> $lable,
                            "uid"=> $optionsid
                        ];
                    }
                
                    $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                    
                    $eformFieldText[] = [                
                        "docuementType" => $docuementType,
                        "expiryDate" => $expiryDate,
                        "isDocFetched" => $isDocFetched,
                        "isverified" => $isverified,
                        "key" => $keyfields,
                        "keytype" => $keytype,
                        "labelname" => $labelname,
                        "options" => $options,
                        "optionsData" => $array_items,
                        "sectionfields" => $sectionfields,
                        "value" => $value,
                        "val" => $value,
                        "verify_status" => $verify_status,
                        //"_id" => $field_id                   
                    ];

                    $fields_form[] = $field_id;
                    
                }
                        
                //$eformFieldText = rtrim($eformFieldText, ',');
                //$fields_form = rtrim($fields_form, ',');

                $dir  = WWW_ROOT . "/img/logo.png";
                // A few settings
                $img_file = $dir;
                // Read image path, convert to base64 encoding
                $imgData = base64_encode(file_get_contents($img_file));

                // Format the image SRC:  data:{mime};base64,{data};
                $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

                $date = date("Y-m-d\TH:i:s.000\Z");

                if($check_akcessId == 1){
                    $response_msg = "EForm response has been verified and updated student record successfully.";
                } else {
                    $response_msg = "EForm response has been verified and created student record successfully.";
                }

                $data_array_eform_response =array(
                    "eformsFields" => $eformFieldText,
                    "fields_form"  => $fields_form,
                    "eformStatus" =>$customVerify,
                    "akcessId"  => $userAkcessId,
                    "formName" => $formName,
                    "eformId" => $mobile_local_id,
                    "eFormResponseId" =>$erid,
                    "response_msg"=> $response_msg,
                    "date" => $date,
                    "logo" => $src,
                    "name" => ORIGIN_URL,
                    "verifier_name" => $verifierName,
                    "verifier_grade" =>$verifierGrade
                );               
            
                $response_data_akcess_eform_response = "";

                $type = 'eform-response-verification';
            
                $method = "POST";

                if($userAkcessId != "") { 

                    $origin_array_response = array(
                        'authorization:'.$token,
                        'apikey:'.$api,
                        'origin:'.$origin_url,
                        'Content-Type: application/json'
                    );

                    $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);
                }
            
                $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                    $check_akcessId = 1;
                }

                if($response_data_akcess_eform_response->status == 1) {
                    
                    $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;
                        
                    if($check_akcessId == 1){

                        $idSingleusertype = "";
                        if($check_akcessId == 1) {
                            $akcessId_data = $conn->execute("SELECT id,usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                            $akcessIdDetails = $akcessId_data->fetch('assoc');
                            $idSingle = $akcessIdDetails['id'];
                            $idSingleusertype = $akcessIdDetails['usertype'];
                        } 

                        $user = $this->Users->get($idSingle, [
                            'conditions' => ['soft_delete' => 0]
                        ]);

                        $user = $this->Users->patchEntity($user, $this->request->getData());

                        $utype = $idSingleusertype . "," .$usertype;
                        $string = implode(',', array_unique(explode(',', $utype)));
                        $string = trim($string,",");

                        $user->name = $firstname . " " . $lastname;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->email = $email;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->faculty = $faculty;
                        $user->courses = $courses;
                        $user->usertype = $string;
                        $user->mobileNumber = $mobileNumber;

                        $this->loadModel('Users');


                        $result = $this->Users->save($user);

//                        $message = "Eform Response student recored update successfully.";
                        $message = "Eform Response has been updated student record successfully";


                    } else {
                        $user = $this->Users->newEntity();

                        $user->name = $firstname . " " . $lastname;
                        $user->akcessId = $akcessId;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->email = $email;
                        $user->mobileNumber = $mobileNumber;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->faculty = $faculty;
                        $user->courses = $courses;
                        $user->usertype = $usertype;
                        $user->loginOpt = 'pin';
                        $user->siteStatus = 'Development';
                        $user->status = 1;

                        $this->loadModel('Users');

                        $result = $this->Users->save($user);    

                        if ($result) {  
                        
                            $insertedId = $result->id;
                            
                            $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                            
                            $this->Users->updateAll(
                                [
                                    'idcardno' => $idcard_randon
                                ], 
                                [
                                    'id' => $insertedId
                                ]
                            );
                        
                            $after = array(                    
                                'akcessId' => $akcessId,
                                'idcardno' => $idcardno,
                                'user_id' => $user_id,
                                'role_id' => $role_id,            
                                'name' => $firstname . " " . $lastname,
                                'address' => $address,
                                'city' => $city,
                                'country' => $country,
                                'email' => $email,
                                'mobileNumber' => $mobileNumber,
                                'gender' => $gender,
                                'dob' => $dob,            
                                'active' => $active,
                                'faculty' => $faculty,
                                'courses' => $courses,
                                'usertype' => $usertype,
                                'loginOpt' => 'pin',
                                'siteStatus' => 'Development',
                                'status' => 1
                            );
                            
                            //$this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);
    
                        }

//                        $message = "Eform Response student recored created successfully.";
                        $message = "Eform Response has been created student record successfully";

                    }

                    if ($result) {  

                        foreach($fieldName as $key => $fieldNames) {
                        
                            $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                            $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                            $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
                
                            if($id != '0') {
//                                $expiryDate = date('Y-m-d',$expiryDate);

                                $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
                        
                                $conn->execute($sql_update); 
                            }
                        }

                        $txId = 0;
    
                        $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";
                        
                        $conn->execute($sql_response);
                        
                        $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                        
                        $conn->execute($sql_trail);

                        
                    }

                    $data['status'] = true;
                    $data['message'] = $message;
                }
    
            }
           
        }
        else if(isset($customVerify) && $customVerify == "create admin"){
            $this->loadModel('Users');
            
            $filedArray = array();

            foreach($fieldName as $key => $fieldNames) {
                
                $name = isset($fieldNames['name']) ? $fieldNames['name'] : '';

                $filedArray[$key] = $name;    
            }
           
            $usertype = "Admin";            
            $firstname = isset($filedArray['firstname']) ? $filedArray['firstname'] : "";
            $lastname = isset($filedArray['lastname']) ? $filedArray['lastname'] : "";
            $name = $firstname . " " . $lastname;
            $email = isset($filedArray['email']) ? $filedArray['email'] : "";

            if(isset($filedArray['mobile'])) {
                $mobileNumber = $filedArray['mobile'];
            } else if(isset($filedArray['mobile number'])) {
                $mobileNumber = $filedArray['mobile number'];
            }

            $dob = isset($filedArray['firstname']) ? date("Y-m-d",strtotime($filedArray['dateofbirth'])) : "";
            $city = isset($filedArray['birthplace']) ? $filedArray['birthplace'] : "";
            $gender = isset($filedArray['gender']) ? $filedArray['gender'] : "";
            $address = isset($filedArray['address']) ? $filedArray['address'] : "";         
            $active = isset($filedArray['active']) ? $filedArray['active'] : "";
            $faculty = isset($filedArray['faculty']) ? $filedArray['faculty'] : "";
            $courses = isset($filedArray['courses']) ? $filedArray['courses'] : "";
            $countryofresidence = $filedArray['countryofresidence'];
            
            $query_response_countries = "SELECT * FROM countries WHERE country_name = '".$countryofresidence."'";

            $results_countries = $conn->execute($query_response_countries);

            $countries_query = $results_countries->fetch('assoc');

            $country = $countries_query['id'];
            $akcessId = isset($filedArray['akcess id']) ? $filedArray['akcess id'] : "";

            if(!empty($akcessId) && $akcessId != "") {
                $akcessId = $akcessId;
            } else {
                $akcessId = $userAkcessId;
            }

            $check_akcessId = 0;
            
           
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');

            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                $check_akcessId = 1;
            }

             if($check_akcessId == 1) {
                 $append_string = "";
                 $comma = "";                
                 if($check_akcessId == 1){
                     $append_string .= $comma . " Akcess Id";
                 } 
             }

            $check_akcessId = 0;

            if($check_akcessId == 0) {

                $this->loadModel('FieldsResponse');        

                $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

                $results = $conn->execute($query_response);

                $fieldsInformation_query = $results->fetchAll('assoc');
            
                $eformFields = array();
                
                $eformFieldText = array();

                $fields_form = array();
                
                foreach($fieldsInformation_query as $key => $fieldsInformations){
                
                    $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                    $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                    $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                    $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                    $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                    $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                    $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                    $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : '';
                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
//                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : 'false';
                    $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                    $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                    $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                    $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                    $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                    $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                    $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                    $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                    $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                    $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                    $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                    
                    $key_value = $keyfields;
                    if (strpos($keyfields, '_') !== false) { 
                        $key_value_explode = explode("_", $keyfields);                
                        $key_value = $key_value_explode[0];
                    }
                
                    $this->loadModel('FieldsOptions');        
                
                    $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                        'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                    ]);
                    
                    //$array_items = '';
                    
                    $array_items = array();

                    foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                        
                        $optionsid = $fieldsOptionsInformations->uid;
                        $checked = $fieldsOptionsInformations->checked;
                        $lable = $fieldsOptionsInformations->lable;
                        $key = $fieldsOptionsInformations->keyfields;

                        $array_items[] = [
                            "checked"=> $checked,
                            "keyfields"=> strtolower($key),
                            "lable"=> $lable,
                            "uid"=> $optionsid
                        ];
                    }
                
                    $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                    
                    $eformFieldText[] = [                
                        "docuementType" => $docuementType,
                        "expiryDate" => $expiryDate,
                        "isDocFetched" => $isDocFetched,
                        "isverified" => $isverified,
                        "key" => $keyfields,
                        "keytype" => $keytype,
                        "labelname" => $labelname,
                        "options" => $options,
                        "optionsData" => $array_items,
                        "sectionfields" => $sectionfields,
                        "value" => $value,
                        "val" => $value,
                        "verify_status" => $verify_status,
                        //"_id" => $field_id                   
                    ];

                    $fields_form[] = $field_id;
                    
                }
                        
                //$eformFieldText = rtrim($eformFieldText, ',');
                //$fields_form = rtrim($fields_form, ',');

                $dir  = WWW_ROOT . "/img/logo.png";
                // A few settings
                $img_file = $dir;
                // Read image path, convert to base64 encoding
                $imgData = base64_encode(file_get_contents($img_file));

                // Format the image SRC:  data:{mime};base64,{data};
                $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

                $date = date("Y-m-d\TH:i:s.000\Z");

                if($check_akcessId == 1){
                    $response_msg = "EForm response has been verified and updated admin record successfully.";
                } else {
                    $response_msg = "EForm response has been verified and created admin record successfully.";
                }

                $data_array_eform_response =array(
                    "eformsFields" => $eformFieldText,
                    "fields_form"  => $fields_form,
                    "eformStatus" =>$customVerify,
                    "akcessId"  => $userAkcessId,
                    "formName" => $formName,
                    "eformId" => $mobile_local_id,
                    "eFormResponseId" =>$erid,
                    "response_msg"=> $response_msg,
                    "date" => $date,
                    "logo" => $src,
                    "name" => ORIGIN_URL,
                    "verifier_name" => $verifierName,
                    "verifier_grade" =>$verifierGrade
                );               
            
                $response_data_akcess_eform_response = "";

                $type = 'eform-response-verification';
            
                $method = "POST";

                if($userAkcessId != "") { 

                    $origin_array_response = array(
                        'authorization:'.$token,
                        'apikey:'.$api,
                        'origin:'.$origin_url,
                        'Content-Type: application/json'
                    );

                    $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);
                }
            
                $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
                if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                    $check_akcessId = 1;
                }

                if($response_data_akcess_eform_response->status == 1) {
                    
                    $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;
                        
                    if($check_akcessId == 1){

                        $idSingleusertype = "";
                        if($check_akcessId == 1) {
                            $akcessId_data = $conn->execute("SELECT id, usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                            $akcessIdDetails = $akcessId_data->fetch('assoc');
                            $idSingle = $akcessIdDetails['id'];
                            $idSingleusertype = $akcessIdDetails['usertype'];
                        }

                        $user = $this->Users->get($idSingle, [
                            'conditions' => ['soft_delete' => 0]
                        ]);

                        $user = $this->Users->patchEntity($user, $this->request->getData());

                        $utype = $idSingleusertype . "," .$usertype;
                        $string = implode(',', array_unique(explode(',', $utype)));
                        $string = trim($string,",");

                        $user->name = $firstname . " " . $lastname;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->email = $email;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->usertype = $string;

                        $this->loadModel('Users');

                        $result = $this->Users->save($user);

//                        $message = "Eform Response admin recored update successfully.";
                        $message = "Eform Response has been updated admin record successfully";

                    } else {
                        $user = $this->Users->newEntity();

                        $user->name = $firstname . " " . $lastname;
                        $user->akcessId = $akcessId;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->email = $email;
                        $user->mobileNumber = $mobileNumber;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->usertype = $usertype;
                        $user->loginOpt = 'pin';
                        $user->siteStatus = 'Development';
                        $user->status = 1;

                        $this->loadModel('Users');

                        $result = $this->Users->save($user);    

                        if ($result) {  
                        
                            $insertedId = $result->id;
                            
                            $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                            
                            $this->Users->updateAll(
                                [
                                    'idcardno' => $idcard_randon
                                ], 
                                [
                                    'id' => $insertedId
                                ]
                            );
                        
                            $after = array(                    
                                'akcessId' => $akcessId,
                                'idcardno' => $idcardno,
                                'user_id' => $user_id,
                                'role_id' => $role_id,            
                                'name' => $firstname . " " . $lastname,
                                'address' => $address,
                                'city' => $city,
                                'country' => $country,
                                'email' => $email,
                                'mobileNumber' => $mobileNumber,
                                'gender' => $gender,
                                'dob' => $dob,            
                                'active' => $active,
                                'usertype' => $usertype,
                                'loginOpt' => 'pin',
                                'siteStatus' => 'Development',
                                'status' => 1
                            );
                            
                            //$this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);
    
                        }
                        $message = "Eform Response has been created admin record successfully";
//                        $message = "Eform Response admin recored created successfully.";
                        
                    }

                    if ($result) {  

                        foreach($fieldName as $key => $fieldNames) {
                        
                            $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                            $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                            $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
                
                            if($id != '0') {
//                                $expiryDate = date('Y-m-d',$expiryDate);

                                $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
                        
                                $conn->execute($sql_update); 
                            }
                        }

                        $txId = 0;

                        $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";
                            
                        $conn->execute($sql_response);
                        
                        $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                        
                        $conn->execute($sql_trail);

                        
                    }

                    $data['status'] = true;
                    $data['message'] = $message;
                }
    
            }
           
        }
        else if(isset($customVerify) && $customVerify == "create teacher"){

            $this->loadModel('Users');
            
            $filedArray = array();
           
            foreach($fieldName as $key => $fieldNames) {
                
                $name = isset($fieldNames['name']) ? $fieldNames['name'] : '';

                $filedArray[$key] = $name;    
            }
            
            $usertype = "Teacher";            
            $firstname = isset($filedArray['firstname']) ? $filedArray['firstname'] : "";
            $lastname = isset($filedArray['lastname']) ? $filedArray['lastname'] : "";
            $name = $firstname . " " . $lastname;
            $email = isset($filedArray['email']) ? $filedArray['email'] : "";
            if(isset($filedArray['mobile'])) {
                $mobileNumber = $filedArray['mobile'];
            } else if(isset($filedArray['mobile number'])) {
                $mobileNumber = $filedArray['mobile number'];
            }
            $dob = isset($filedArray['dateofbirth']) ? date("Y-m-d",strtotime($filedArray['dateofbirth'])) : "";
            $city = isset($filedArray['birthplace']) ? $filedArray['birthplace'] : "";
            $gender = isset($filedArray['gender']) ? $filedArray['gender'] : "";
            $address = isset($filedArray['address']) ? $filedArray['address'] : "";         
            $active = isset($filedArray['active']) ? $filedArray['active'] : "";
            $academic_personal_type = isset($filedArray['academic personal type']) ? $filedArray['academic personal type'] : "";
            $countryofresidence = isset($filedArray['countryofresidence']) ? $filedArray['countryofresidence'] : "";
            
            $query_response_countries = "SELECT * FROM countries WHERE country_name = '".$countryofresidence."'";

            $results_countries = $conn->execute($query_response_countries);

            $countries_query = $results_countries->fetch('assoc');

            $country = $countries_query['id'];
            $akcessId = isset($filedArray['akcess id']) ? $filedArray['akcess id'] : "";

            if(!empty($akcessId) && $akcessId != "") {
                $akcessId = $akcessId;
            } else {
                $akcessId = $userAkcessId;
            }

            $check_akcessId = 0;
           
            $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0' AND id != '" . $id . "'");
            $akcessIdDetails = $akcessId_data->fetch('assoc');

            if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                $check_akcessId = 1;
            }

            if($check_akcessId == 1) {
                $append_string = "";
                $comma = "";                
                if($check_akcessId == 1){
                    $append_string .= $comma . " Akcess Id";
                }   
            }

            $check_akcessId = 0;

            if($check_akcessId == 0) {

                $this->loadModel('FieldsResponse');        

                $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

                $results = $conn->execute($query_response);

                $fieldsInformation_query = $results->fetchAll('assoc');
            
                $eformFields = array();
                
                $eformFieldText = array();

                $fields_form = array();
                
                foreach($fieldsInformation_query as $key => $fieldsInformations){
                
                    $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                    $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                    $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                    $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                    $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                    $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                    $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                    $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : '';
                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
//                    $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : 'false';
                    $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                    $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                    $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                    $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                    $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                    $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                    $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                    $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                    $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                    $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                    $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                    
                    $key_value = $keyfields;
                    if (strpos($keyfields, '_') !== false) { 
                        $key_value_explode = explode("_", $keyfields);                
                        $key_value = $key_value_explode[0];
                    }
                
                    $this->loadModel('FieldsOptions');        
                
                    $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                        'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                    ]);
                    
                    //$array_items = '';
                    
                    $array_items = array();

                    foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                        
                        $optionsid = $fieldsOptionsInformations->uid;
                        $checked = $fieldsOptionsInformations->checked;
                        $lable = $fieldsOptionsInformations->lable;
                        $key = $fieldsOptionsInformations->keyfields;

                        $array_items[] = [
                            "checked"=> $checked,
                            "keyfields"=> strtolower($key),
                            "lable"=> $lable,
                            "uid"=> $optionsid
                        ];
                    }
                
                    $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                    
                    $eformFieldText[] = [                
                        "docuementType" => $docuementType,
                        "expiryDate" => $expiryDate,
                        "isDocFetched" => $isDocFetched,
                        "isverified" => $isverified,
                        "key" => $keyfields,
                        "keytype" => $keytype,
                        "labelname" => $labelname,
                        "options" => $options,
                        "optionsData" => $array_items,
                        "sectionfields" => $sectionfields,
                        "value" => $value,
                        "val" => $value,
                        "verify_status" => $verify_status,
                        //"_id" => $field_id                   
                    ];

                    $fields_form[] = $field_id;
                    
                }
                        
                //$eformFieldText = rtrim($eformFieldText, ',');
                //$fields_form = rtrim($fields_form, ',');

                $dir  = WWW_ROOT . "/img/logo.png";
                // A few settings
                $img_file = $dir;
                // Read image path, convert to base64 encoding
                $imgData = base64_encode(file_get_contents($img_file));

                // Format the image SRC:  data:{mime};base64,{data};
                $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

                $date = date("Y-m-d\TH:i:s.000\Z");

                if($check_akcessId == 1){
                    $response_msg = "EForm response has been verified and updated academic personnel record successfully.";
                } else {
                    $response_msg = "EForm response has been verified and created academic personnel record successfully.";
                }

                $data_array_eform_response =array(
                    "eformsFields" => $eformFieldText,
                    "fields_form"  => $fields_form,
                    "eformStatus" =>$customVerify,
                    "akcessId"  => $userAkcessId,
                    "formName" => $formName,
                    "eformId" => $mobile_local_id,
                    "eFormResponseId" =>$erid,
                    "response_msg"=> $response_msg,
                    "date" => $date,
                    "logo" => $src,
                    "name" => ORIGIN_URL,
                    "verifier_name" => $verifierName,
                    "verifier_grade" =>$verifierGrade
                );               
            
                $response_data_akcess_eform_response = "";

                $type = 'eform-response-verification';
            
                $method = "POST";

                if($userAkcessId != "") { 

                    $origin_array_response = array(
                        'authorization:'.$token,
                        'apikey:'.$api,
                        'origin:'.$origin_url,
                        'Content-Type: application/json'
                    );

                    $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);
                }
               
              
                $akcessId_data = $conn->execute("SELECT count(*) as count_akcessId FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                $akcessIdDetails = $akcessId_data->fetch('assoc');
              
                if((isset($akcessIdDetails['count_akcessId']) && $akcessIdDetails['count_akcessId'] > 0) && (isset($akcessId) && $akcessId != "")) {          
                    $check_akcessId = 1;
                }

                if($response_data_akcess_eform_response->status == 1) {

                    $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;

                    if($check_akcessId == 1){

                        $idSingleusertype = "";
                        if($check_akcessId == 1) {                           
                            $akcessId_data = $conn->execute("SELECT id, usertype FROM users WHERE akcessId = '" . $akcessId . "' AND soft_delete = '0'");
                            $akcessIdDetails = $akcessId_data->fetch('assoc');
                            $idSingle = $akcessIdDetails['id'];
                            $idSingleusertype = $akcessIdDetails['usertype'];
                        }

                        $user = $this->Users->get($idSingle, [
                            'conditions' => ['soft_delete' => 0]
                        ]);

                        $query_response_academic_personal_type = "SELECT * FROM academiclist WHERE name = '".$academic_personal_type."'";

                        $results_academic_personal_type = $conn->execute($query_response_academic_personal_type);

                        $academic_personal_type_query = $results_academic_personal_type->fetch('assoc');

                        $academic_personal_type_staff_type = $academic_personal_type_query['id'];

                        $user = $this->Users->patchEntity($user, $this->request->getData());

                        $utype = $idSingleusertype . "," .$usertype;
                        $string = implode(',', array_unique(explode(',', $utype)));
                        $string = trim($string,",");
                    
                        $user->name = $firstname . " " . $lastname;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->academic_personal_type = $academic_personal_type_staff_type;
                        $user->usertype = $string;

                        $this->loadModel('Users');

                        $result = $this->Users->save($user);

//                        $message = "Eform Response teacher recored update successfully.";
                        $message = "Eform Response has been updated academic personnel record successfully";



                    } else {

                        $user = $this->Users->newEntity();

                        $query_response_academic_personal_type = "SELECT * FROM academiclist WHERE name = '".$academic_personal_type."'";

                        $results_academic_personal_type = $conn->execute($query_response_academic_personal_type);

                        $academic_personal_type_query = $results_academic_personal_type->fetch('assoc');

                        $academic_personal_type_staff_type = $academic_personal_type_query['id'];
                    
                        $user->name = $firstname . " " . $lastname;
                        $user->akcessId = $akcessId;
                        $user->firstname = $firstname;
                        $user->lastname = $lastname;
                        $user->address = $address;
                        $user->city = $city;
                        $user->country = $country;
                        $user->email = $email;
                        $user->mobileNumber = $mobileNumber;
                        $user->gender = $gender;
                        $user->dob = $dob;
                        $user->active = $active;
                        $user->academic_personal_type = $academic_personal_type_staff_type;
                        $user->usertype = $usertype;
                        $user->loginOpt = 'pin';
                        $user->siteStatus = 'Development';
                        $user->status = 1;

                        $this->loadModel('Users');

                        $result = $this->Users->save($user);

                        if ($result) {  
                        
                            $insertedId = $result->id;
                            
                            $idcard_randon = $this->Global->random_string('numeric', 10) . $insertedId;
                            
                            $this->Users->updateAll(
                                [
                                    'idcardno' => $idcard_randon
                                ], 
                                [
                                    'id' => $insertedId
                                ]
                            );
                        
                            $after = array(                    
                                'akcessId' => $akcessId,
                                'idcardno' => $idcardno,
                                'user_id' => $user_id,
                                'role_id' => $role_id,            
                                'name' => $firstname . " " . $lastname,
                                'address' => $address,
                                'city' => $city,
                                'country' => $country,
                                'email' => $email,
                                'mobileNumber' => $mobileNumber,
                                'gender' => $gender,
                                'dob' => $dob,            
                                'active' => $active,
                                'staff_type' => $staff_type,
                                'usertype' => $usertype,
                                'loginOpt' => 'pin',
                                'siteStatus' => 'Development',
                                'status' => 1
                            );
                            
                            $this->Global->auditTrailApi($insertedId, 'users', 'insert', null, $after);

//                            $message = "Eform Response teacher recored created successfully.";
                            $message = "Eform Response has been created academic personnel record successfully";

                        }
                    }
                    
                    if ($result) {  

                        foreach($fieldName as $key => $fieldNames) {
                        
                            $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                            $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                            $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
                
                            if($id != '0') {
//                                $expiryDate = date('Y-m-d',$expiryDate);

                                $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
                        
                                $conn->execute($sql_update); 
                            }
                        }

                        $txId = 0;

                        $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";
        
                        $conn->execute($sql_response);
        
                        $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                        
                        $conn->execute($sql_trail);
                        
                    }
                    
                    $data['status'] = true;
                    $data['message'] = $message;
                }
    
            }
            
        }
        else if(isset($customVerify) && $customVerify == "return") {

            $this->loadModel('FieldsResponse');        

            $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

            $results = $conn->execute($query_response);

            $fieldsInformation_query = $results->fetchAll('assoc');
           
            $eformFields = array();
            
            $eformFieldText = array();

            $fields_form = array();
            
            foreach($fieldsInformation_query as $key => $fieldsInformations){
            
                $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : '';
                $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
//                $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : 'false';
                $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                
                $key_value = $keyfields;
                if (strpos($keyfields, '_') !== false) { 
                    $key_value_explode = explode("_", $keyfields);                
                    $key_value = $key_value_explode[0];
                }
            
                $this->loadModel('FieldsOptions');        
            
                $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                    'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                ]);
                
                //$array_items = '';
                
                $array_items = array();

                foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                    
                    $optionsid = $fieldsOptionsInformations->uid;
                    $checked = $fieldsOptionsInformations->checked;
                    $lable = $fieldsOptionsInformations->lable;
                    $key = $fieldsOptionsInformations->keyfields;

                    $array_items[] = [
                        "checked"=> $checked,
                        "keyfields"=> strtolower($key),
                        "lable"=> $lable,
                        "uid"=> $optionsid
                    ];
                }
             
                $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                
                $eformFieldText[] = [                
                    "docuementType" => $docuementType,
                    "expiryDate" => $expiryDate,
                    "isDocFetched" => $isDocFetched,
                    "isverified" => $isverified,
                    "key" => $keyfields,
                    "keytype" => $keytype,
                    "labelname" => $labelname,
                    "options" => $options,
                    "optionsData" => $array_items,
                    "sectionfields" => $sectionfields,
                    "value" => $value,
                    "val" => $value,
                    "verify_status" => $verify_status,
                    //"_id" => $field_id                   
                ];

                $fields_form[] = $field_id;
                
            }
                    
            //$eformFieldText = rtrim($eformFieldText, ',');
            //$fields_form = rtrim($fields_form, ',');

            $dir  = WWW_ROOT . "/img/logo.png";
            // A few settings
            $img_file = $dir;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($img_file));

            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

            $date = date("Y-m-d\TH:i:s.000\Z");

            $response_msg = "EForm response has been returned successfully";

            $data_array_eform_response =array(
                "eformsFields" => $eformFieldText,
                "fields_form"  => $fields_form,
                "eformStatus" =>$customVerify,
                "akcessId"  => $userAkcessId,
                "formName" => $formName,
                "eformId" => $mobile_local_id,
                "eFormResponseId" =>$erid,
                "response_msg"=> $response_msg,
                "date" => $date,
                "logo" => $src,
                "name" => ORIGIN_URL,
                "verifier_name" => $verifierName,
                "verifier_grade" =>$verifierGrade
            );               
           
            $response_data_akcess_eform_response = "";

            $type = 'eform-response-verification';
        
            $method = "POST";

            if($userAkcessId != "") { 

                $origin_array_response = array(
                    'authorization:'.$token,
                    'apikey:'.$api,
                    'origin:'.$origin_url,
                    'Content-Type: application/json'
                );

                $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);
            }
            
            if($response_data_akcess_eform_response->status == 1) {

                $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;
               
                $type = 'field/Verify';
            
                $method = "POST";

                $response_data_akcess_verify = "";

                $data_array_verify = array(
                    'userAkcessId'  => $userAkcessId, 
                    'verifierAkcessId' => $verifierAkcessId,
                    'sessionId' => $sessionId,
                    'profileField'  => $profile_field_string,
                    'expiryDate' => $expiry_date_field_string,
                    'fileName'      => $fileName_ak,
                    'akData'        => $akData
                );

                if($userAkcessId != "" && $profile_field_string != "") { 
                    $response_data_akcess_verify = $this->Global->curlPostEformResponseVerifyField($method, $type, $api, $origin_url, $api_url, $data_array_verify, $origin_array);
                }

                $response = json_decode($response_data_akcess_verify);
            
                if($response->statusCode == '200'){

                    $txId = 0;
                    $txId_array = json_decode($response->txId);
                    if (json_last_error() === 0 && isset($txId_array->txId) && $txId_array->txId)
                    {
                        $txId = $txId_array->txId;
                    }
                    else
                    {
                    if (!empty($response->txId->txId) && $response->txId->txId != "") {
                        $txId = $response->txId->txId;
                    } else {
                        $txId = $response->txId;
                    }
                    }

                    $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";
                    
                    $conn->execute($sql_response);

                    $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                    
                    $conn->execute($sql_trail);

                    foreach($fieldName as $key => $fieldNames) {
                    
                        $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                        $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                        $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
            
                        if($id != '0') {
//                            $expiryDate = date('Y-m-d',$expiryDate);

                            $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
                    
                            $conn->execute($sql_update); 
                        }
                    }

                    $status_response = $response->status;

                    $data['status'] = true;
//                    $data['message'] = "Eform Response Returned";
                    $data['message'] = "EForm response has been returned successfully";
                } 
            }
            
        } else if(isset($customVerify) && $customVerify == "reject"){

            $this->loadModel('FieldsResponse');        

            $query_response = "SELECT * FROM fieldsresponse WHERE fk_eformresponse_id = '".$erid."' and soft_delete=0";

            $results = $conn->execute($query_response);

            $fieldsInformation_query = $results->fetchAll('assoc');
           
            $eformFields = array();
            
            $eformFieldText = array();

            $fields_form = array();
            
            foreach($fieldsInformation_query as $key => $fieldsInformations){
            
                $field_id = isset($fieldsInformations['id']) ? $fieldsInformations['id'] : ''; 
                $fk_eformresponse_id = isset($fieldsInformations['fk_eformresponse_id']) ? $fieldsInformations['fk_eformresponse_id'] : ''; 
                $fk_eform_id = isset($fieldsInformations['fk_eform_id']) ? $fieldsInformations['fk_eform_id'] : ''; 
                $labelname = isset($fieldsInformations['labelname']) ? $fieldsInformations['labelname'] : ''; 
                $keyfields = isset($fieldsInformations['keyfields']) ? $fieldsInformations['keyfields'] : ''; 
                $keytype = isset($fieldsInformations['keytype']) ? $fieldsInformations['keytype'] : ''; 
                $value = isset($fieldsInformations['value']) ? $fieldsInformations['value'] : ''; 
                $file = isset($fieldsInformations['file']) ? $fieldsInformations['file'] : '';
                $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : ( isset($fieldNames['verify_status']) ? 'true' : 'false' );
//                $verify_status = isset($fieldsInformations['verify_status']) && $fieldsInformations['verify_status'] ? 'true' : 'false';
                $file_verified = isset($fieldsInformations['file_verified']) ? $fieldsInformations['file_verified'] : ''; 
                $expiryDate = isset($fieldsInformations['expiryDate']) ? $fieldsInformations['expiryDate'] : ''; 
                $isverified = isset($fieldsInformations['isverified']) && $fieldsInformations['isverified'] ? 'true' : 'false';
                $docuementType = isset($fieldsInformations['docuementType']) ? $fieldsInformations['docuementType'] : 0; 
                $isDocFetched = isset($fieldsInformations['isDocFetched']) && $fieldsInformations['isDocFetched'] ? 'true' : 'false';
                $signature_required = isset($fieldsInformations['signature_required'] ) && $fieldsInformations['signature_required'] != "" ? $fieldsInformations['signature_required'] : 'no'; 
                $options = isset($fieldsInformations['options']) ? json_decode($fieldsInformations['options']) : ''; 
                $verification_grade = isset($fieldsInformations['verification_grade']) ? $fieldsInformations['verification_grade'] : ''; 
                $section_id = isset($fieldsInformations['section_id']) ? $fieldsInformations['section_id'] : ''; 
                $section_color = isset($fieldsInformations['section_color']) ? $fieldsInformations['section_color'] : ''; 
                $sectionfields = isset($fieldsInformations['sectionfields']) && $fieldsInformations['sectionfields'] != 's:0:"";' ? $fieldsInformations['sectionfields'] : ''; 

                
                $key_value = $keyfields;
                if (strpos($keyfields, '_') !== false) { 
                    $key_value_explode = explode("_", $keyfields);                
                    $key_value = $key_value_explode[0];
                }
            
                $this->loadModel('FieldsOptions');        
            
                $fieldsOptionsInformation = $this->FieldsOptions->find('all', [
                    'conditions' => ['fk_fields_id' => $field_id, 'fk_eform_id' => $id, 'soft_delete' => 0]
                ]);
                
                //$array_items = '';
                
                $array_items = array();

                foreach($fieldsOptionsInformation as $fieldsOptionsInformations) {
                    
                    $optionsid = $fieldsOptionsInformations->uid;
                    $checked = $fieldsOptionsInformations->checked;
                    $lable = $fieldsOptionsInformations->lable;
                    $key = $fieldsOptionsInformations->keyfields;

                    $array_items[] = [
                        "checked"=> $checked,
                        "keyfields"=> strtolower($key),
                        "lable"=> $lable,
                        "uid"=> $optionsid
                    ];
                }
             
                $expiryDate = date("Y-m-d\TH:i:s.000\Z", strtotime($expiryDate));
                
                $eformFieldText[] = [                
                    "docuementType" => $docuementType,
                    "expiryDate" => $expiryDate,
                    "isDocFetched" => $isDocFetched,
                    "isverified" => $isverified,
                    "key" => $keyfields,
                    "keytype" => $keytype,
                    "labelname" => $labelname,
                    "options" => $options,
                    "optionsData" => $array_items,
                    "sectionfields" => $sectionfields,
                    "value" => $value,
                    "val" => $value,
                    "verify_status" => $verify_status,
                    //"_id" => $field_id                   
                ];

                $fields_form[] = $field_id;
                
            }
                    
            //$eformFieldText = rtrim($eformFieldText, ',');
            //$fields_form = rtrim($fields_form, ',');

            $dir  = WWW_ROOT . "/img/logo.png";
            // A few settings
            $img_file = $dir;
            // Read image path, convert to base64 encoding
            $imgData = base64_encode(file_get_contents($img_file));

            // Format the image SRC:  data:{mime};base64,{data};
            $src = 'data:' . mime_content_type($img_file) . ';base64,' . $imgData;

            $date = date("Y-m-d\TH:i:s.000\Z");

            $data_array_eform_response =array(
                "eformsFields" => $eformFieldText,
                "fields_form"  => $fields_form,
                "eformStatus" =>$customVerify,
                "akcessId"  => $userAkcessId,
                "formName" => $formName,
                "eformId" => $mobile_local_id,
                "eFormResponseId" =>$erid,
                "response_msg"=> "EForm response has been rejected successfully",
                "date" => $date,
                "logo" => $src,
                "name" => ORIGIN_URL,
                "verifier_name" => $verifierName,
                "verifier_grade" =>$verifierGrade
            );               
           
            $response_data_akcess_eform_response = "";

            $type = 'eform-response-verification';
        
            $method = "POST";

            if($userAkcessId != "") { 

                $origin_array_response = array(
                    'authorization:'.$token,
                    'apikey:'.$api,
                    'origin:'.$origin_url,
                    'Content-Type: application/json'
                );

                $response_data_akcess_eform_response = $this->Global->curlPostEformResponseEformReponseVerify($method, $type, $api, $origin_url, $api_url, json_encode($data_array_eform_response), $origin_array_response);
            }
            
            if($response_data_akcess_eform_response->status == 1) {

                $eform_response_verification_id = $response_data_akcess_eform_response->data->_id;

                $txId = 0;

                $sql_response = "INSERT INTO `eformresponsesubmit` (`fk_eformresponseId`,`eform_response_verification_id`, `txId`) VALUES (".$eform_response_id.", '".$eform_response_verification_id."','".$txId."')";
                
                $conn->execute($sql_response);

                $sql_trail = "UPDATE `eformresponse` SET `status`='".$customVerify."' WHERE `id` = ".$eform_response_id;
                
                $conn->execute($sql_trail);

                foreach($fieldName as $key => $fieldNames) {
                
                    $verify_status = isset($fieldNames['verify_status']) ? 'Yes' : '';
                    $expiryDate = isset($fieldNames['expiry_date']) ? $fieldNames['expiry_date'] : '';
                    $id = isset($fieldNames['d']) ? $this->Global->userIdDecode($fieldNames['d']) : '0';
        
                    if($id != '0') {
//                        $expiryDate = date('Y-m-d',$expiryDate);
                        
                        $sql_update = "UPDATE `fieldsresponse` SET `verify_status`='".$verify_status."',`expiryDate`='".$expiryDate."'  WHERE `id` = ".$id;
                
                        $conn->execute($sql_update); 
                    }
                }

                $status_response = $response->status;

                $data['status'] = true;
//                $data['message'] = "Eform Response Rejected";
                $data['message'] = "EForm response has been rejected successfully";

            }
            
        }
        $res = json_encode($data);
        $this->response->type('json');
        $this->response->body($res);

        return $this->response;
        
    }

    public function postEformResponseVerify(){
       

        $conn = ConnectionManager::get("default"); // name of your database connection       
        
        $api = isset($_POST['api']) ? $_POST['api'] : SITE_API_KEY_URL;
        $origin_url = isset($_POST['orurl']) ? $_POST['orurl'] : ORIGIN_URL;
        $api_url = isset($_POST['apiurl']) ? $_POST['apiurl'] : AK_ORIGIN_URL;

        $field_id = isset($_POST['f']) ? $this->Global->userIdDecode($_POST['f']) : "";
        $eform_response_id = isset($_POST['e']) ? $this->Global->userIdDecode($_POST['e']) : "";

        $eform_response_data = $conn->execute("SELECT fr.*, er.formName, er.akcessId, er.eformId, er.id as erid FROM fieldsresponse as fr
        LEFT JOIN eformresponse as er ON fr.fk_eformresponse_id = er.id
        WHERE fr.id = '".$field_id."' AND er.id = '".$eform_response_id."'");

        $eform_response = $eform_response_data->fetch('assoc');

        $userAkcessId = isset($eform_response['akcessId']) ? $eform_response['akcessId'] : '';
        $profileField = isset($eform_response['keyfields']) ? $eform_response['keyfields'] : '';

        $response_token = $this->Global->getToken();
           
        $token = $response_token;
        
        $origin_array = array(
            'authorization: ' . $token,
            'apikey: ' . $api,
            'origin: ' . $origin_url
        );

        $verifiers_data = $conn->execute("SELECT * FROM verifiers");
            
        $verifiers = $verifiers_data->fetch('assoc');

        $verifierAkcessId = $verifiers['verifierAkcessId'];
        $verifierName = $verifiers['verifierName'];
        $fileName_ak = $verifiers['fileName'];
        $akData = $verifiers['akData']; 
       
        $data_array = array(
            'userAkcessId'  => $userAkcessId, 
            'profileField'  => $profileField,
            'fileName'      => $fileName_ak,
            'akData'        => $akData
        );
      
        $type = 'verifier/getFieldVerifierOnPortal';
        
        $method = "POST";

        $response_data_akcess_verify = "";

        if($userAkcessId != "" && $profileField != "") { 
            $response_data_akcess_verify = $this->Global->curlPostEformResponseVerify($method, $type, $api, $origin_url, $api_url, $data_array, $origin_array, $userAkcessId, $profileField, $fileName_ak, $akData, $token);
        } 
                
        return $response_data_akcess_verify;
        
    }
}
