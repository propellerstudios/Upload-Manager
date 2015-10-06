<?php
namespace UploadManager\Model\Entity;

use Cake\ORM\Entity;

class Upload extends Entity{
    
    public function isImage() {
        return strstr($this->_properties['type'], 'image');
    }
    
    public function _getFullPath() {
        return WWW_ROOT . DS . $this->_properties['path'];
    }
    
}
