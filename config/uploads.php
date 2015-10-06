<?php

$defaultFileSize = 30; // 30 MB
$defaultPath =  'uploads'; // WWW_ROOT/uploads/

return[
    'Uploads' => [
        // Max file size in MB ex: 1, 5, 10, 25
        'maxFileSize' =>  $defaultFileSize,
        // Path to upload directory, relative to webroot
        'storagePath' => $defaultPath,
        // Separate uploads into directories (ex: things/thing1)
        'storeOwner' => true
    ]
];

