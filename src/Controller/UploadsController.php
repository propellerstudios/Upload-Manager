<?php
namespace UploadManager\Controller;

use Cake\ORM\TableRegistry;
use UploadManager\Controller\AppController;

class UploadsController extends AppController{

    public function initialize() {
        parent::initialize();
        $this->loadComponent('UploadManager.Uploader');     
    }


    /**
     * Upload a file or more and associate it with a particular model
     */
    public function add() {
        if($this->request->is('post')){
            
            $uploads = $this->request->data['uploads'];
            $owner = $this->request->data['owner'];
            
            // Error messages and handling happens in the component    
            $this->Uploader->saveUploads($uplaods, $owner);  
        }     
    }
    
    public function edit($id) {
        
    }
    
    /**
     * Download a single uploaded file
     * 
     * @param id of upload to be downloaded
     */
    public function download($id) {
        if(!$upload = $this->Uploads->get($id)) 
            return $this->Flash->error('File could not be retrieved');
        
        return $this->Uploader->download($upload);
    }
    
    /**
     * Delete a single uploaded file
     * 
     * @param id of upload to delete
     */
    public function delete($id) {
        if(!$this->request->is('delete'))
            return $this->redirect();  
            
        $upload = $this->Uploads->get($id); 
        
        if($upload->owner_table == 'Users') {
            if($this->Auth->user('id') !== $upload->owner_id) 
                $this->Flash->error(
                    'You do not have permission to delete this file');
            else{
                $this->Uploads->delete($upload);
                $this->Flash->danger('File deleted.');
            }
        }
        
        return $this->redirect();
    }
} 
