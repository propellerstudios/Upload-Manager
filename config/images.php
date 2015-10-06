<?php

use Cake\Core\Configure;

Configure::load('UploadManager.uploads', 'default');

$defaultUploadPath = Configure::read('Uploads.storagePath');
$defaultTmpPath =  $defaultUploadPath . DS . 'tmp'; 

return[
    'Images' => [
        'tmpStoragePath' => $defaultTmpPath
    ]
];
