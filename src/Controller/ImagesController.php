<?php

namespace UploadManager\Controller;

use Cake\Filesystem\File;
use UploadManager\Controller\AppController;

class ImagesController extends AppController {

    public function initialize() {
        parent::initialize();
        $this->loadComponent('UploadManager.Image');     
    }

    public function index() {
        
    }
    
    public function add() {
        
    }
    
    /**
     *  Image manipulation view - Includes a small toolbox for basic
     *  image editing before saving or discarding changes
     */
    public function edit($id, $action = null, array $params = null) {
        $image = $this->Images->get($id, ['contain' => 'Uploads']);
        
        
        if($this->request->is(['post', 'put'])) {
            
            $imgFile = new File($image->upload->full_path);
            
            // Backup original
            $backupFile = new File(TMP . DS . time() . 'bak');
            $imgFile->copy($backupFile->path);
            
            if(!$image->hasTmp()) 
                return $this->Flash->error('Sorry, we lost your edit...');
            
            // Open tmp file
            $tmpFile = new File($image->tmp_path_full);
            
            // Copy tmp file to permanent location
            if(!$tmpFile->copy($imgFile->path)) 
                return $this->Flash->error('Could not save image');
                
            // Change recorded image dimentions and size
            $image->upload->size = $tmpFile->size();
            $image->width = $this->Image->width($tmpFile->path);
            $image->height = $this->Image->height($tmpFile->path);
            
            // Delete tmp file
            $image->deleteTmp(); 
            
            if(!$this->Images->save($image)){
                // Restore file with matching, original size and dimentions
                $backupFile->copy($imgFile->path);
                $backupFile->delete();
                $this->Flash->error('Could not save image.');
                return $this->redirect();
            }
            else {
                $this->Flash->success('Image edited successfully.');
                return $this->redirect();
            }
        }
        else{
            if(!$image->hasTmp())
                $image->makeTmp();
        }
        
        $this->set([
            'image' => $image,
            'route' => $this->RedirectUrl->load()
        ]);
    }
    
    /**
     * Execute manipulation operations on entity based on action passed
     *
     * @param id - id of image to be manipulated 
     * @param action - manipulation action to execute from component
     * @param params - parameters to pass to manipulation component
     */ 
    public function manipulate($id, $action) {
        $image = $this->Images->get($id, ['contain' => 'Uploads']);
        if($this->request->is['post'])
            $params = $this->request->data;
        else
            $params = $this->request->query;
        /**
         * Set the path to be used as the tmp path
         */
        if($image->hasTmp()) 
            $image->path($image->tmp_path_full);
            
        switch($action){
            case 'fliph':
                $this->Image->flipHorizontally($image);
                $this->set([
                    'image' => $image 
                ]);
                break;
            case 'flipv':
                $this->Image->flipVertically($image);
                break;
            case 'resize':
                $params = $this->request->data;
                $this->Image->resize($image, $params);
                break;
            case 'rotate':
                $this->Image->rotate($image, $params);
                break;
            case 'skew':
                $this->Image->skew($image, $params);
                break;
            case 'stretch':
                $this->Image->stretch($image, $params);
            
        }
        
        $this->set([
            'image' => $image,
            'route' => $this->RedirectUrl->load()
        ]);
        
        $this->render('UploadManager.Images/edit');
    }
    
    public function delete() {
        
    }

}
