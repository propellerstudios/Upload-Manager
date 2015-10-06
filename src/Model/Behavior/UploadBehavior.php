<?php
namespace UploadManager\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;

class UploadBehavior extends Behavior {
    
    /**
     * Dynamically create a belongsTo relationship to any model 
     * passed through, and have that passed model develop a hasMany
     * relationship with Uploads
     * 
     * @param table name that owns the entity
     */
    public function initialize(array $config) {
        
        $owners = $this->_table;
        $owner = $owners->alias();
        
        $uploads = TableRegistry::get('UploadManager.Uploads');
        
        $uploads->belongsTo($owner, [
            'foreignKey' => 'owner_id',
            'conditions' => [
                'Uploads.owner_table' => $owner
            ]
        ]);
            
        // Owner relationship should be specified in owner table class
        // though this may prove useful here..
        $owners->hasMany('Uploads', [
            'className' => 'UploadManager.Uploads',
            'foreignKey' => 'owner_id',
            'conditions' => [
                'Uploads.owner_table' => $owner
            ], 
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);
    }
}
