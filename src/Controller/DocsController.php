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
use Cake\Datasource\ConnectionManager;
use Helper;
use HtmlHelper;
use DateTime;

/**
 * Docs Controller
 *
 * @property \App\Model\Table\DocsTable $Docs
 *
 * @method \App\Model\Entity\Doc[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class DocsController extends AppController
{
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
        
        $this->viewBuilder()->setLayout('ajax');
    }
    
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $docs = $this->Docs->find('all' , array('conditions' => ['soft_delete' => 0]));

        $this->set(compact('docs'));
    }

    /**
     * View method
     *
     * @param string|null $id Doc id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($idEncode = null)
    {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $doc = $this->Docs->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);

        $this->set('doc', $doc);
    }
    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($userId = null)
    {
        $userId = $this->Global->userIdEncode($userId);
        $doc = $this->Docs->newEntity();        
        $this->set(compact('doc', 'userId'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Doc id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($idEncode = null)
    {
        $id = $this->Global->userIdDecode($idEncode);
        
        $doc = $this->Docs->get($id, [
            'conditions' => ['soft_delete' => 0]
        ]);
        
        $userID = isset($doc->fk_users_id) ? $this->Global->userIdEncode($doc->fk_users_id) : '';
        $did = isset($doc->id) ? $this->Global->userIdEncode($doc->id) : '';
        $name = isset($doc->name) ? $doc->name : '';
               
        $this->set(compact('doc', 'did', 'userID', 'name'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Doc id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($idEncode = null, $userIdEncode = null)
    {
        
        $id = $this->Global->userIdDecode($idEncode);
        
        $userId = $this->Global->userIdDecode($userIdEncode);
        
        $url = $_SERVER['HTTP_REFERER'];
        
        $this->request->allowMethod(['post', 'delete']);
        
        $doc = $this->Docs->get($id);
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $after = array(
            'user_id' => $user_id,
            'role_id' => $role_id,
            'fk_user_id' => $userId,     
            'soft_delete' => 1
        );
        
        $before = array(
            'soft_delete' => 0
        );
        
        $lastInsertedId = $this->Global->auditTrailApi($id, 'docs', 'delete', $before, $after);

        $updateDoc = $this->Docs->updateAll(
            [
                'soft_delete' => 1
            ], 
            [
                'id' => $id,
                'fk_users_id' => $userId
            ]
        );
        
        $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
         
        if ($updateDoc) {            
            $this->Flash->success(__('The Document has been deleted.'));
        } else {
            $this->Flash->error(__('The Document could not be deleted. Please, try again.'));
        }
        
        $uIdEncode = $this->Global->userIdEncode($userId);
        
        if(strpos($url, 'teacher') !== false) {
            if(strpos($url, 'edit') !== false) {
                return $this->redirect(['controller' => 'Users', 'action' => 'edit-teacher', $uIdEncode]);
            } else if(strpos($url, 'view') !== false) {
                return $this->redirect(['controller' => 'Users', 'action' => 'view-teacher', $uIdEncode]);
            }
        } else if(strpos($url, 'staff') !== false) {
            if(strpos($url, 'edit') !== false) {
                return $this->redirect(['controller' => 'Users', 'action' => 'edit-staff', $uIdEncode]);
            } else if(strpos($url, 'view') !== false) {
                return $this->redirect(['controller' => 'Users', 'action' => 'view-staff', $uIdEncode]);
            }
        } else {
            if(strpos($url, 'edit') !== false) {
                return $this->redirect(['controller' => 'Users', 'action' => 'edit', $uIdEncode]);
            } else if(strpos($url, 'view') !== false) {
                return $this->redirect(['controller' => 'Users', 'action' => 'view', $uIdEncode]);
            }
        }
        

    }
    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addidcard($userId = null)
    {
        $doc = $this->Docs->newEntity();
        
        $this->set(compact('doc'));

    }
    
    public function docGenrate() {
       
        $conn = ConnectionManager::get("default"); // name of your database connection     
        
        $user_id = $this->Auth->user( 'id' );
        $role_id = $this->Auth->user( 'usertype' );
        
        $this->loadModel('Users');
          
        $status_sucess = 0;
       
        if($this->request->data('a') != "") {
           
            $name = $this->request->data('name');
            $expiyDate = $this->request->data('idCardExpiyDateDoc');
            $expiyDate = !empty($expiyDate) ? $expiyDate: date('Y-m-d', strtotime($date. ' + 10 years'));
            $name_org = $this->request->data('name');
            $fk_documenttype_id = $this->request->data('fk_documenttype_id');
            $id = $this->Global->userIdDecode($this->request->data('a'));
            $docid = $this->Global->userIdDecode($this->Global->userIdDecode($this->request->data('b')));
            
            if($this->request->data('b') != "") {
                $query_data = $conn->execute('SELECT * FROM docs where id='.$docid);
                $query_docid = $query_data->fetch('assoc');
            }
                        
            $this->loadModel('VerifyDocs');
            
            $userDetails = $this->Users->get($id, array('conditions' => ['status' => 1,'soft_delete' => 0, 'id' => $id]));
            
            $flname = $userDetails->id;
            
            $dir  = WWW_ROOT . "uploads/attachs/" . $flname;
            
            if($userDetails) {
           
                $name = isset($name) ? $name : '';              
                $fk_users_id =  isset($id) ? $id : $userDetails->id;

                if (!empty($this->request->data['attachs']['name'])) {

                    //$name = str_replace(' ', '_', $name);

                    $file = $this->request->data['attachs']; // Creating a variable to handle upload
                    
                    $ext = substr(strtolower(strrchr($file['name'], '.')), 1); 
                    
                    $fileName = $name . " " . $file['name'];

                    //$fileName = $name . "_" . rand() . "." . $ext;

                    $fileSize = $this->request->data('attachs')['size'];           
                    //get the extension
                    $arr_ext = array('jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx'); //processing file extension
                    //if extension is valid
                    if (in_array($ext, $arr_ext)) {
                        //do the actual uploading of the file. First arg is the tmp name, second arg is
                        //where we are putting it      
                        
                        if( is_dir($dir) === false )
                        {
                            mkdir($dir, 0777, true);
                        }                        

                        move_uploaded_file($file['tmp_name'], $dir . "/" . $fileName);
                        //saving file on database
                        $this->request->data['attachs']['name'] = $fileName;
                    } else {
                        $resultJ = json_encode(array('result' => 'error','msg' => "Please upload 'jpg', 'jpeg', 'gif', 'png', 'pdf', 'doc', 'docx' files only. Please, try again."));
                        $this->response->type('json');
                        $this->response->body($resultJ);
                        return $this->response;
                    }

                    $root_file = $dir . "/" . $fileName;
                    $photo = $this->request->data['attachs']['name'];
                    $fileSize = $this->request->data('attachs')['size'];
                }

                $temp_file_location = $root_file;

                $fileNameHash = hash('sha256', $root_file);

                $verifiers_data = $conn->execute("SELECT * FROM verifiers");

                $verifiers = $verifiers_data->fetch('assoc');

                $verifierAkcessId = $verifiers['verifierAkcessId'];
                $verifierName = $verifiers['verifierName'];
                $fileName_ak = $verifiers['fileName'];
                $akData = $verifiers['akData'];

                $file_ak_link = WWW_ROOT . "uploads/tempfile/" . $fileName_ak;
                $fp = fopen($file_ak_link, "w");
                fwrite($fp, $akData);
                fclose($fp);

                $file_Ak = WWW_ROOT . "uploads/tempfile/" . $fileName_ak;

                $documentID = $verifierAkcessId;
                //$documentID = $verifierAkcessId . "-" . strtotime("now");

                $akcessIdHash = hash('sha256', $verifierAkcessId);

                $data = array(
                    "response"          => $response, 
                    "verifierAkcessId"  => $verifierAkcessId, 
                    "verifierName"      => $verifierName, 
                    "file_Ak"           => $file_Ak, 
                    "akData"            => $akData, 
                    "documentID"        => $documentID, 
                    "akcessIdHash"      => $akcessIdHash, 
                    "fileNameHash"      => $fileNameHash, 
                    "temp_file_location"=> $temp_file_location,
                    'channelName'       => 'akcessglobal',
                    'recievedType'      => 'document',
                    'domainName'        => ORIGIN_URL,
                    'company_name'      => COMP_NAME,
                    'fileName_ak'       => $fileName_ak
                );
               
                $response_document_add_data = $this->Global->getDocumentAdd($data);

                $response_ddate = json_decode($response_document_add_data);

                //print_R($response_ddate);

                if($response_ddate->status == 1) {                

    //                if($this->request->data('b') != "") {
    //                    $responseS3Delete = $this->Global->S3BucketDelete($query_docid['fileName']);
    //                }
    //                                
    //                $responseS3 = $this->Global->S3Bucket($fileName, $temp_file_location, $ext);

                    $fileUrl = $fileName;
                    
                    $transactionID = $response_ddate->data->txId[0];

                    $data['expiryDate'] = date("Y-m-d\TH:i:s.000\Z", strtotime($expiyDate));

                    $response_document_verify = $this->Global->getVerifyDoc($data);   

                    $response_dverify = json_decode($response_document_verify);    
                    
                    if($response_dverify->statusCode == 200) {
                        
                        $transactionIDV = $response_dverify->txId[0];
                       
                        $verifyDocs = $this->VerifyDocs->newEntity();
                        $verifyDocs->verifierAkcessId = $verifierAkcessId;
                        $verifyDocs->userAkcessId = $verifierAkcessId;
                        $verifyDocs->expiryDate = $expiyDate;
                        $verifyDocs->txId = $transactionIDV;
                        $verifyDocs->verifierName = $verifierName;
                        $verifyDocs->documentId = $documentID;

                        $saveVerifyDocs = $this->VerifyDocs->save($verifyDocs);
                       
                        $saveVerifyDocsId = $saveVerifyDocs->id;
               
                        $after = array(
                            'user_id' => $user_id,
                            'role_id' => $role_id, 
                            'fk_user_id' => $fk_users_id,
                            'verifierAkcessId' => $verifierAkcessId,
                            'userAkcessId' => $verifierAkcessId,
                            'expiryDate' => $expiyDate,
                            'txId' => $transactionIDV,
                            'verifierName' => $verifierName,
                            'documentId' => $documentID
                        );
                        
                        $this->Global->auditTrailApi($saveVerifyDocsId, 'verifeddocs', 'insert', null, $after);
                       
                        $status_sucess = 1;
                        
                        //unlink(WWW_ROOT . '/uploads/attachs/' . $fileName);

                        unlink(WWW_ROOT . "/uploads/tempfile/" . $fileName_ak);

                        if($this->request->data('b') != "") {
                            
                            $docsUpdate = $this->Docs->get($docid, [
                                'conditions' => ['soft_delete' => 0]
                            ]);
                            
                            $before = array(           
                                'fileName'          => $docsUpdate->fileName,
                                'fileUrl'           => $docsUpdate->fileUrl,
                                'documentHash'      => $docsUpdate->documentHash,
                                'transactionID'     => $docsUpdate->transactionID,
                                'timeStamp'         => $docsUpdate->timeStamp,
                                'channelName'       => $docsUpdate->channelName,
                                'documentId'        => $docsUpdate->documentId,
                                'AkcessID'          => $docsUpdate->AkcessID,
                                'size'              => $docsUpdate->size,
                                'name'              => $docsUpdate->name,
                                'idCardExpiyDate'   => $docsUpdate->idCardExpiyDate,
                                'fk_documenttype_id' => $docsUpdate->fk_documenttype_id
                            );
                            
                            $after = array(
                                'user_id'           => $user_id,
                                'role_id'           => $role_id,
                                'fk_user_id'        => $fk_users_id,                
                                'fileName'          => $fileName,
                                'fileUrl'           => $fileUrl,
                                'documentHash'      => $fileNameHash,
                                'transactionID'     => $transactionID,
                                'timeStamp'         => date('Y-m-d H:i:s'),
                                'channelName'       => 'akcessglobal',
                                'documentId'        => $documentID,
                                'AkcessID'          => $verifierAkcessId,
                                'size'              => $fileSize,
                                'name'              => $name_org,
                                'idCardExpiyDate'   => $expiyDate,
                                'fk_documenttype_id' => $fk_documenttype_id
                            );

                            $lastInsertedId = $this->Global->auditTrailApi($docid, 'docs', 'update', $before, $after);

                            $updateIDCard = $this->Docs->updateAll(
                                [
                                    'fileName'          => $fileName,
                                    'fileUrl'           => $fileUrl,
                                    'documentHash'      => $fileNameHash,
                                    'transactionID'     => $transactionID,
                                    'timeStamp'         => date('Y-m-d H:i:s'),
                                    'channelName'       => 'akcessglobal',
                                    'documentId'        => $documentID,
                                    'AkcessID'          => $verifierAkcessId,
                                    'size'              => $fileSize,
                                    'name'              => $name_org,
                                    'idCardExpiyDate'   => $expiyDate,
                                    'fk_documenttype_id' => $fk_documenttype_id
                                ], 
                                [
                                    'id' => $docid
                                ]
                            );
                            
                            $this->Global->auditTrailApiSuccess($lastInsertedId, 1);
                            
                        } 
                        else {

                            $docs = $this->Docs->newEntity();

                            $docs->fk_users_id = $fk_users_id;
                            $docs->fileName = $fileName;
                            $docs->fileUrl = $fileUrl;
                            $docs->documentHash = $fileNameHash;
                            $docs->transactionID = $transactionID;
                            $docs->timeStamp = date('Y-m-d H:i:s');
                            $docs->channelName = 'akcessglobal';
                            $docs->documentId = $documentID;
                            $docs->AkcessID = $verifierAkcessId;
                            $docs->name = $name_org;
                            $docs->idCardExpiyDate = $expiyDate;
                            $docs->size = $fileSize;
                            $docs->fk_documenttype_id = $fk_documenttype_id;
                            
                            $saveDocs = $this->Docs->save($docs);
                            
                            $saveDocsId = $saveDocs->id;
               
                            $after = array(
                                'user_id' => $user_id,
                                'role_id' => $role_id,       
                                'fk_user_id' => $fk_users_id,
                                'fileName' => $fileName,
                                'fileUrl' => $fileUrl,
                                'documentHash' => $fileNameHash,
                                'transactionID' => $transactionID,
                                'timeStamp' => date('Y-m-d H:i:s'),
                                'channelName' => 'akcessglobal',
                                'documentId' => $documentID,
                                'AkcessID' => $verifierAkcessId,
                                'name' => $name_org,
                                'idCardExpiyDate' => $expiyDate,
                                'size' => $fileSize,
                                'fk_documenttype_id' => $fk_documenttype_id,
                            );

                            $this->Global->auditTrailApi($saveDocsId, 'docs', 'insert', null, $after);
                        }

                    }
                }
            }
        }
        
        if ($status_sucess == 1) {
            $resultJ = json_encode(array('result' => 'success','msg' => 'The document has been saved.'));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        } else {
            $resultJ = json_encode(array('result' => 'error','msg' => 'The document could not be saved. Please, try again.'));
            $this->response->type('json');
            $this->response->body($resultJ);
            return $this->response;
        }
    }
}
