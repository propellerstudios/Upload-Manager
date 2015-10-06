<?php   
namespace UploadManager\Model\Entity;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

class Image extends Entity {
    
    protected $path; // Variable holds path of file focused for manipulation
    protected $tmpDirPath; // Relative directory holding temporary file
    protected $tmpDirFull; // Absolute path to directory holding tmp file
    protected $tmpPath; // Relative path to temporary file 
    protected $tmpPathFull; // Full path to temporary file
    
    /**
     *  Preliminary check for alternate/tmp file must be made 
     */
    public function __construct( array $properties, array $options) {
        parent::__construct($properties, $options);
        
        Configure::load('UploadManager.images', 'default');
        $this->tmpDirPath = Configure::read('Images.tmpStoragePath');
        $this->tmpDirFull = WWW_ROOT . $this->tmpDirPath;
        
        $tmpPath = $this->tmpDirPath . DS . $this->source() . '-' . $this->_properties['id'];
        
        $tmpPathFull = WWW_ROOT . $tmpPath;
        $tmp = new File($tmpPathFull);
        
        if($tmp->exists()){
            $this->tmpPath = $tmpPath;
            $this->tmpPathFull = $tmpPathFull;
        }
    }
    
    
    /**
     *  Virtual property describing the original path of this entity,
     *  relative to the webroot
     *
     *  @return string Relative path of original image file
     */
    protected function _getOriginalPath() {
        $uploads = TableRegistry::get('UploadManager.Uploads');
        $upload = $uploads->get($this->_properties['upload_id']);
        return $upload->path;
    }
    
    
    /**
     *  Virtual property describing the original, full path of this entity
     *
     *  @return string Full path of original image file
     */
    protected function _getOriginalFullPath() {
        $uploads = TableRegistry::get('UploadManager.Uploads');
        $upload = $uploads->get($this->_properties['upload_id']);
        return $upload->full_path;
    }
    
    
    /**
     *  Return path to file, relative to webroot, if set.
     *
     *  @return string Relative path to temporary file
     */
    protected function _getTmpPath() {
        return $this->tmpPath;
    }
    
    
    /**
     *  Return full path to temporary file, if set
     *
     *  @return string Absolute path to temporary file
     */
    protected function _getTmpPathFull() {
        return $this->tmpPathFull;
    }
    
    
    /**
     *  This method can be used to return the full path of
     *  the upload that owns this entity.  If the class
     *  variable $path has been set, that will be returned
     *  instead.
     *
     *  This method can also be used to set the path variable,
     *  allowing the entity to hold a different path which may
     *  be used to point to a temporary file.
     *
     *  The Image manipulation component uses Image::path() to
     *  fetch the path of the image file to be manipulated.
     *  This is done incase a working file or tmp file is to be
     *  used instead of the original file.
     *
     *  @param path Path associated with entity
     */
    public function path($path = null) {
        if(!$path && !$this->path)
            return $this->_getOriginalFullPath();
        else if(!$path)
            return $this->path;
        else
            $this->path = $path;
    }
    
    /**
     * Make copy of the original file as a temporary file or working
     * file.  This is used to prevent uncommitted changes affecting
     * the original file.
     *
     * @return boolean Success
     */
    public function makeTmp() {
        // Create tmp folder if not found
        $tmpDir = new Folder($this->tmpDirFull, true, 0755);
        
        $tmpPath = $this->tmpDirPath . DS . $this->source() . '-' . $this->_properties['id'];
        
        $tmpPathFull = WWW_ROOT . $tmpPath;
        
        $tmpFile = new File($tmpPathFull);
        $tmpFile->create();
        
        $original = new File($this->_getOriginalPath());
        
        if($original->copy($tmpPathFull)) {
            $this->tmpPath = $tmpPath;
            $this->tmpPathFull = $tmpPathFull;
            return true;
        }
    }
    
    /**
     * Delete the temporary file associated with this entity.
     *
     * @return boolean Success
     */
    public function deleteTmp() {
        $tmp = new File($this->tmpPathFull);
        return $tmp->delete();
    }
    
    /**
     *  Check to see if a temporary file for this entity's original
     *  file exists.
     *
     *  @return boolean Success
     */
    public function hasTmp() {
        $tmp = new File($this->tmpPathFull);
        return $tmp->exists();
    }
    
}
