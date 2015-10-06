<?php
use Cake\Routing\Router;

Router::plugin('UploadManager', function ($routes) {
    $routes->fallbacks('InflectedRoute');
});
