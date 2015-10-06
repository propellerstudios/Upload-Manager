<?php
namespace UploadManager\Model\Table;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class UploadsTable extends Table {
    
    public function initialize(array $config) {
        $this->addBehavior('Timestamp');
        
        $this->hasOne('Images', [
            'className' => 'UploadManager.Images',
            'dependent' => true
        ]);
    }
    
    public static function defaultConnectionName() {
        return 'test';
    }     
    
    public function validationDefault(Validator $validator) {
        $validator
            ->notEmpty('original_name', 'File cannot have no name')
            ->add('size', 'size', [
                'rule' => ['minLength', 1 ],
                'message' => 'File cannot be 0 bytes.'
        ]);
            
        return $validator;
    }
    
      
    /**
     * Delete file on server represented by entity being deleted
     */
    public function beforeDelete(Event $event, Entity $entity, \ArrayObject $options) {
        Configure::load('UploadManager.uploads', 'default');    
        $storagePath = Configure::read('Uploads.storagePath');
            
        $file = new File($entity->path);
        $folder = $file->Folder();
        
        // Check for empty directories on successful delete
        if($file->delete()){
            // Delete type folder if empty
            if(!$folder->find()) {
                $oldFolder = $folder;
                $folder->cd($folder->realpath($folder->pwd() . DS . '..'));
                $oldFolder->delete();
                // Check for other possible empty parent (owner storage)
                if($folder->pwd() !== $storagePath) 
                    if(!$folder->find())
                        $folder->delete();
            }
        }
        else 
            $event->stopPropagation();
    }
}
