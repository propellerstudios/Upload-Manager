<?php
namespace UploadManager\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use UploadManager\Model\Entity\Upload;

class UploaderComponent extends Component {
        
    private $maxFileSize;
    private $storagePath;
    private $storeOwner;    
    
    public $components = [
        'UploadManager.Image',
        'Flash'
    ];
    
    // Load configurations  and set variables
    public function initialize(array $config) {
        Configure::load('UploadManager.uploads', 'default');
        $this->maxFileSize = Configure::read('Uploads.maxFileSize');
        $this->maxFileSize *= 1048576; // Multiply by bytes per mb
        $this->storagePath = Configure::read('Uploads.storagePath');
        $this->storeOwner = Configure::read('Uploads.storeOwner');  
    }
    
    /**
     * Upload a file to the server
     * @param upload - file to upload
     * @param owner - contains model name and id associated with the file
     * @return array - uploaded file information or null for failure
     */
    public function upload($upload, $owner = null) {
            
        // Filesize verification    
        if($upload['size'] == 0) 
            return null;

        if($upload['size'] > $this->maxFileSize)
            return null;

        $path = $this->storagePath;
        
        // Owner separated storage
        if($this->storeOwner == true && $owner) {
            // Directory should be lower case
            $ownerTable = strtolower($owner->source());
            // Owner specific directory must be unique (uuid)
            $ownerDirectory = Inflector::singularize($ownerTable) . $owner->id;
            $path .= DS . $ownerTable . DS . $ownerDirectory; 
        }
        
        // If types do not match, default subdir is 'document'
        $subdir = 'document'; 
        $types = ['image', 'audio', 'video'];   
        
        // Check for filetype
        foreach ($types as $type) {
            if(strstr($upload['type'], $type))
                $subdir = $type;
        }
 
        // Append the subdirectory (filtype directory)
        $path .= DS . $subdir;
        
        // Make directory if there is none
        $directory = new Folder();
        if(!$directory->create(WWW_ROOT . DS . $path))
            return null;
        
        // Find file in tmp
        $file = new File($upload['tmp_name']);
        
        // File's name must be unique, making the path unique as well
        $name = time() . '_' . Inflector::slug($upload['name']); 
        $path .= DS . $name;
        
        // Copy from tmp to perm (create)
        if($file->copy($path)) 
            return [
                'original_name' => $upload['name'],
                'name' => $name,
                'path' => $path,
                'type' => $upload['type'],
                'size' => $upload['size']
            ];
        else 
            return null;
    }


    /**
     * Download a single file
     * 
     * @param upload entity to be downloaded
     * @return response - file download on success or error message
     */
    public function download(Upload $upload) {
        
        $path = $upload->path;
        $type = $upload->type;
        $name = $upload->original_name;
        
        $file = new File(WWW_ROOT . DS . $path);
        
        if(!$file->exists()) 
            return $this->Flash->error('Could not retrieve file');
        
        $this->response->type($type);    
            
        $this->response->file(
            $path,[
                'download' => true,
                'name' =>  $name
            ]    
        );
        
        return $this->response;
    }
    
    
    /**
     * Delete an existing file on the server, if this causes
     * the directory to be empty, delete it; provided it is
     * not the root of uploads.
     * 
     * @param path - location of file
     * @return result of delete (success/fail)
     */
    public function delete($path) { 
        $file = new File($path);
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
            return false;
    }   
     
    /**
     * Save uploads from form to the server and in the UploadsTable,
     * uploads will be associated with the table of the owner entity 
     * provided.  If any uploads fail, a Flash error is created,
     * containing the filenames that failed and some error messages.
     * 
     * @param uploads to save to server and associate with owner
     * @param owner entity for files to belong to
     */
    public function saveUploads($uploads, $owner) {
    
        // Owner information for UploadsBehavior association
        $ownerTable = $owner->source();  // alias of table
        $ownerId = $owner->id;       

        $uploadsTable = TableRegistry::get('UploadManager.Uploads');
        
        foreach($uploads as $upload) {   
            /**
             * Initial Verification:
             *  Check error code returned in the file objects by
             *  the form before advancing.  
             *  http://php.net/manual/en/features.file-upload.errors.php
             */ 
            switch($upload['error']){  
                // Upload exceeds upload_max_filesize in php.ini    
                case 1:    
                // Upload exceeds MAX_FILE_SIZE in HTML form
                case 2:
                    $errorMessage = 'File exceeds max size';
                    break;
                // Upload has been partially uploaded
                case 3:
                    $errorMessage = 'File was only partially uploaded';
                    break;
                // No file was uploaded (will happen if no file is submitted)
                case 4:
                    return;
                // Missing a temporary folder
                case 5;
                    $errorMessage = 'Missing temporary folder';
            }
            
            // log any errors to failed uploads string and continue
            if(isset($errorMessage)) {
                if(isset($failedUploads))
                    $failedUploads .= $upload['name'] . ': ' 
                        . $errorMessage.'<br>';
                else 
                    $failedUploads = $upload['name'] . ': ' 
                        . $errorMessage.'<br>';
                unset($errorMessage);
                continue;
            }
            
            // Upload the file, returning file information
            $fileInfo = $this->upload($upload, $owner);
        
            // Log error if the upload was a fail
            if(!$fileInfo){
                if(isset($failedUploads))
                    $failedUploads .= $upload['name'] . '<br>';
                else 
                    $failedUploads = $upload['name'] . '<br>';
            }  
            // Record owner information and continue 
            else{
                $fileInfo['owner_table'] = $ownerTable; 
                $fileInfo['owner_id'] = $ownerId;
        
                $file = $uploadsTable->newEntity($fileInfo);

                if(!$file->errors()) {
                    // Save upload and pass owner table name as an option so
                    // an association may be made in afterSave()
                    if($uploadsTable->save($file))
                        $this->Flash->success('Upload successful');
                        // Check file type for Images table
                        if(strstr($file->type, 'image')) {
                            if(!$this->Image->saveAsImage($file)) 
                                $this->Flash->error('An error occured');
                        }
                    else 
                        $this->Flash->error('An error occured');
                }
                else
                    $this->Flash->error('An error occured');
            }       
        }

        // Report unsuccessful uploads if any
        if(isset($failedUploads)) {
            $failedUploads = 'Could not upload the following files:<br>' .
                $failedUploads;
            return $this->Flash->error($failedUploads); 
        }
        else
            return $this->Flash->success('Upload successful');
    }

    
}
