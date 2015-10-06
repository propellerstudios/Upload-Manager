<?php
namespace UploadManager\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ImagesTable extends Table {

    
    public function initialize(array $config) {
        $this->addBehavior('Timestamp'); 
        $this->belongsTo('Uploads', [
            'className' => 'UploadManager.Uploads',
            'conditions' => ['Uploads.type LIKE' => 'image%'],
            'dependent' => true
        ]);
    }
    
    
    public static function defaultConnectionName() {
        return 'test';
    } 
    
    /**
     * Change modified value of parent Upload
     */
    public function beforeSave(Event $event, Entity $entity, \ArrayObject $options)  {
        $uploads = TableRegistry::get('UploadManager.Uploads');
        $upload = $uploads->get($entity->get('upload_id'));
        
        $uploads->touch($upload);   
    }

}