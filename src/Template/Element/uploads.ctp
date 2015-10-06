<!--
   The uploads element is used to display all uploads
   belonging to an associated table in a div.  The 
   variable $owner must be set before use.
   
   The owner will be checked to have a user_id. If this
   is true, the current Authed user will be tested against
   said user_id.  If the id's match, the user is authed to
   delete the file and, if the file is an image, edit it.
   Feel free to manipulate the code to work in your favor.
-->
<?php
    if(isset($owner)) {
        if($owner['uploads']){
            
            $uploads = $owner->uploads;
        
            $uploadsHtml = '';
            
            foreach($uploads as $upload){
                    
                // Set action and options        
                $action = [ 
                    'plugin' => 'UploadManager',
                    'controller' => 'Uploads', 
                    'action' => 'download',
                    $upload['id'] 
                ];   
                $options = [ 
                    'class' => 'btn btn-primary btn-upload', 
                    'escape' => false, 
                ]; 
                 
                // File is a PDF
                if(strstr($upload->type, 'pdf')) {
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-pdf-o'),
                        $action,
                        $options
                    );
                }
                // Word file
                else if(strstr($upload->type, 'word')) {
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-word-o'),
                        $action,
                        $options
                    );
                }
                // Image file
                else if(strstr($upload->type, 'image')) {
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-image-o'),
                        $action,
                        $options
                    );
                // Audio file
                }else if(strstr($upload->type, 'audio')) {
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-audio-o'),
                        $action,
                        $options
                    );
                }
                // Video file
                else if(strstr($upload->type, 'video')) {
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-video-o'),
                        $action,
                        $options
                    );
                }
                // Compressed file
                else if(strstr($upload->type, 'compressed')) {
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-archive-o'),
                        $action,
                        $options
                    );
                }
                // Compressed File
                else if(strstr($upload['type'], 'tar')) {
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-archive-o'),
                        $action,
                        $options
                    );
                }
                // Generic File
                else{
                    $uploadBlock = $this->Html->link(
                        $this->Html->icon('file-text-o'),
                        $action,
                        $options
                    );
                }
                
                /* Upload Details */
                // Original name to be displayed, not saved file name
                $name = $this->Text->truncate(
                $upload->original_name, 15);
                
                /**
                * Files are displayed by their original name, but will be
                * appended with a number not the first instance 
                */
                if(isset($lastName)) {
                    if($name == $lastName) {
                        $count = isset($count) ? $count++ : 1;
                        $name = '(' . $count . ')' . $name;
                    }
                }
                
                // Save name to compare with next file for redundancy
                $lastName = $name;
                
                // Parse size to human readable form
                $size = $this->Number->toReadableSize($upload['size']);
                
                $details = array($name, $size);
                
                /**
                 * Check for user auth to add delete/edit/view buttons,
                 * this may need to be changed depending on the owner
                 * model
                 */
                $userId = isset($owner->user_id) ?
                    $owner->user_id : ($owner->source() === 'Users' ?
                        $owner->id : null);
            
                $authId = $this->request->session()->read('Auth.User.id');
                
                // Only display action buttons if user is authed
                if($userId && $userId === $authId) {
                    $delete = $this->Html->link($this->Html->icon('times'),[
                            'plugin' => 'UploadManager',
                            'controller' => 'Uploads',
                            'action' => 'delete',
                            $upload->id
                        ],
                        [
                            'class' => 'upload-action upload-delete btn-xs',
                            'escape' => false
                        ]
                    );
                    
                    $actions = $delete;
                    
                    // Actions for image files
                    if($upload->isImage()) {
                        $edit = $this->Html->link(
                            $this->Html->icon('pencil-square'),
                            [
                                'plugin' => 'UploadManager',
                                'controller' => 'Images',
                                'action' => 'edit',
                                $upload->image->id
                            ],
                            [
                                'class' => 'upload-action image-edit btn-xs',
                                'escape' => false
                            ]
                        );
                        
                        $view = $this->Html->link(
                            $this->Html->icon('eye'),
                            [
                                'plugin' => 'UploadManager',
                                'controller' => 'Images',
                                'actions' => 'view',
                                $upload->image->id
                            ],
                            [
                                'class' => 'upload-action image-view btn-xs',
                                'escape' => false
                            ]
                        );
                        
                        $actions .= $edit . $view;
                        
                        $actions = $this->Html->div(
                            'upload-action-group', $actions
                        );
                    }
                    
                    $details = array_merge($details, [$actions]);
                }
                
                $detailList = $this->Html->nestedList($details,[
                    'class' => 'upload-details'
                     
                ]);
                
                // Append details and icon in a div
                $uploadBlock = $uploadBlock . $detailList;
                
                $uploadsHtml .= $this->Html->div(
                    'upload-block', $uploadBlock);
    
            }// End of loop
    
            // Create div to throw upload icons in
            echo $this->Html->div('upload-icons', $uploadsHtml);
        }
        
        // See CSS to tweak and add styles
        echo $this->Html->css('UploadManager.uploads');
    }
?>