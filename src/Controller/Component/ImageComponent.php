<?php
namespace UploadManager\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use UploadManager\Model\Entity\Image;
use UploadManager\Model\Entity\Upload;

class ImageComponent extends Component {
    
    private $imagine;
    private $imagesTable;
    
    public function initialize(array $config) {
        $this->imagine = new Imagine();
        //$this->imagesTable = TableRegistry::get('UploadManager.Images');
    }
    
    /**
     * Flip image horizontally
     *
     * @param Image image - image to manipulate
     *
     * @return Image Image entity with new dimensions and size
     */
    public function flipHorizontally(Image $image) {
        $path = $image->path();
        
        $edit = $this->imagine->open($path);
        $edit->flipHorizontally()
            ->save();
        
        $file = new File($path);
        $size = $file->size();
        $image->upload->size = $size;
        
        return $image;
    }
    
    /**
     * Flip image vertically
     *
     * @param Image image - image to manipulate
     *
     * @return Image Image entity with new dimensions and size
     */
    public function flipVertically(Image $image) {
        $path = $image->path();
        
        $edit = $this->imagine->open($path);
        $edit->flipVertically()
            ->save();
        
        $file = new File($path);
        $size = $file->size();
        $image->upload->size = $size;
        
        return $image;
    }
    
    /**
     * Crop section of image
     *
     * @param Image image - image to manipulate
     * @params array params - manipulation parameters
     *      [startX] start cropping on x axis
     *      [startY] start cropping on y axis
     *      [width] of crop
     *      [height] of crop
     * 
     * @return Image Image entity with new dimensions and size
     */
    public function crop(Image $image, array $params) {
        $path = $image->path();
        
        $edit = $this->imagine->open($path);
        $edit->crop(new Point($params['startX'], $params['startY']),
                    new Box($params['width'], $params['height']))
            ->save($path);
        
        // Set new dimensions
        $image->width = $edit->getSize()->getWidth();
        $image->height = $edit->getSize()->getHeight();  
        
        // Set new size
        $file = new File($path);
        $size = $file->size();
        $image->upload->size = $size;
        $image->width = $edit->getSize()->getWidth();
        $image->height = $edit->getSize()->getHeight();
            
        return $image;
    }
    
    /**
     * Resize image to particular width and height
     * 
     * @param Image image - image to manipulate
     * @params array params - manipulation parameters
     *      [width] - desired width
     *      [height] - desired height
     * 
     * @return Image Image entity with new dimensions and size
     */
    public function resize(Image $image, array $params) {
        $path = $image->path();
        
        $edit = $this->imagine->open($path);
        $edit->resize(new Box($params['width'], $params['height']))
            ->save($path);
        
        // Set new dimensions
        $image->width = $edit->getSize()->getWidth();
        $image->height = $edit->getSize()->getHeight();  
        
        // Set new size
        $file = new File($path);
        $size = $file->size();
        $image->upload->size = $size;
        $image->width = $edit->getSize()->getWidth();
        $image->height = $edit->getSize()->getHeight();
        
        return $image;
    }
    
    
    /**
     * Stretching an image can be done by percentage, 
     * this function passes appropriate argument values
     * to resize() based on that percentage
     * 
     * @param Image image - image to manipulate
     * @params array params - manipulation parameters
     *      [percentage] - percentage to stretch by
     * 
     * @return value of resize()
     */
    public function stretch(Image $image, array $params) {
        $width = $image->width;
        $height = $image->height;
        
        $width += $width * $params['percentage'];
        $height += $height * $params['percentage'];
        
        $dimensions = [
            'width' => $width,
            'height' => $height
        ];
        
        return $this->resize($image, $dimensions);
    }
    
    /**
     * Similar to stretch, but the image is to be
     * decreased by that percentage. 
     * 
     * @param Image image - image to manipulate
     * @params array params - manipulation parameters
     *      [percentage] - percentage to skew by
     * 
     * @return value of resize()
     */
    public function skew(Image $image, array $params) {
        $width = $image->width;
        $height = $image->height;
        
        $width -= $width * $params['percentage'];
        $height -= $height * $params['percentage'];
        
        $dimensions = [
            'width' => $width,
            'height' => $height
        ];
        
        return $this->resize($image, $dimensions);
    }
    
    /**
     * Rotate image based on degree value
     * 
     * @param Image image - image to manipulate
     * @params array params - manipulation parameters
     *      [degree] - degree to rotate by
     * 
     * @return Image Image entity with new dimensions and size
     */
    public function rotate(Image $image, array $params) {
        $path = $image->path();
        
        $edit = $this->imagine->open($path);
        $edit->rotate($params['degree'])
            ->save($path);
        
        // Set new size
        $file = new File($path);
        $size = $file->size();
        $image->upload->size = $size;
        $image->width = $edit->getSize()->getWidth();
        $image->height = $edit->getSize()->getHeight();
        
        return $image;
    }
    
    /**
     * Get width of an image
     * 
     * @param path to image file
     * 
     * @return width of image
     */
    public function width($path) {
        return $this->imagine->open($path)->getSize()->getWidth();
    }
    
    /**
     * Get height of image
     * 
     * @param path to image file
     * 
     * @return height of image
     */
    public function height($path) {
        return $this->imagine->open($path)->getSize()->getHeight();
    }
    
    
    /**
     * Create a new image entity based on an existing file,
     * Upload entity and save it in the table; Associate Image
     * to Upload
     * 
     * @param image to be recorded in the table
     * 
     * @return Image Image entity with new dimensions and size
     */
    public function saveAsImage(Upload $upload) {
        // Open the file as an imagine instance    
        $path = $upload->path;
        $imageFile = $this->imagine->open($path);
        
        // Get dimensions for entity record
        $width = $imageFile->getSize()->getWidth();
        $height = $imageFile->getSize()->getHeight();  
        
        // Create and set entity
        $imageEntity = $this->imagesTable->newEntity();
        $imageEntity->upload_id = $upload->id;
        $imageEntity->width = $width;
        $imageEntity->height = $height;
        
        return $image;
    }
}
