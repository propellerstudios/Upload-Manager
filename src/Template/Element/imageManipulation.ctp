<!--
    This element provides the end-user an interface to manipulate an
    image and observe changes before committing them. 
-->
<div class="image-man" >
    
    <!-- The file controls consist of:
        undo, redo, save, cancel
    -->
    <div class="file-controls">
        <div class="text-right">
            <?=
                $this->Form->postButton(
                    $this->Html->icon('save'),
                    [
                        'action' => 'edit',
                        $image->id
                    ],
                    [ 'class' => 'save-man', 'id' => 'save-man' ]
                )
            ?>
            <?=
                $this->Html->link(
                    $this->Html->icon('ban'),  
                    $route,
                    [
                        'id' => 'cancel-man',
                        'class' => 'btn btn-default cancel-man',
                        'escape' => false
                    ] 
                );
            ?>
        </div>
    </div>
    
    <!--
        Image view will have a grid, zoom in/out capabilites,
        and the image to be manipulated
    -->
    <div class="image-man-view">
        <div id="image-man-frame" class="image-man-frame">
            <?php    
                // Specify path to begin at webroot with DS
                echo $this->Html->image(DS . $image->tmpPath, [
                    'alt' => $image->upload->original_name,
                    'tite' => $image->upload->original_name,
                    'class' => 'image-man-image',
                    'id' => 'image-man-image'
                ]);
            ?>
        </div>
    </div>
    
    <!--
        Image controls consist of:
            flip, resize, stretch, skew, and rotate 
    -->
    <div class="image-controls" >
        <!--<ul>-->
            <div class="flip-rotate">
                <?=
                    $this->Html->link(
                        $this->Html->icon('shield fa-lg'),
                        [
                            'action' => 'manipulate',
                            $image->id,
                            'fliph'
                        ], 
                        [
                            'escape' => false,
                            'class' => 'man-action'
                        ]
                    )
                ?>
                <?=
                    $this->Html->link(
                        $this->Html->icon('shield fa-rotate-270 fa-lg'),
                        [
                            'action' => 'manipulate',
                            $image->id,
                            'flipv'
                        ], 
                        [
                            'escape' => false,
                            'class' => 'man-action'
                        ]
                    )
                ?>
                
                <?=
                    $this->Html->link(
                        $this->Html->icon('rotate-left fa-lg'),
                        [
                            'action' => 'manipulate',
                            $image->id,
                            'rotate',
                            'degree' => '-90'
                        ], 
                        [
                            'escape' => false,
                            'class' => 'man-action'
                        ]
                    )
                ?>
                <?=
                    $this->Html->link(
                        $this->Html->icon('rotate-right fa-lg'),
                        [
                            'action' => 'manipulate',
                            $image->id,
                            'rotate',
                            'degree' => '90'
                        ], 
                        [
                            'escape' => false,
                            'class' => 'man-action'
                        ]
                    )
                ?>
            </div>
            
            <!--
                Resize consists of two fields and a clickable
                link that determines whether or not the aspect
                ratio is to be preserved.
            -->
            <div class="sizing">
                <div class="resize-control">
                    <span class="resize btn">
                        <?= $this->Html->icon('arrows-alt') ?>
                    </span>
                </div>
                <div class="size-fields">
                    <?=
                        $this->Form->create(null, [
                            'id' => 'resize-form',
                            'url' => [
                                'action' => 'manipulate',
                                $image->id,
                                'resize'
                            ]
                        ])
                    ?>
                    <?= $this->Form->input('width', [
                        'label' => false,
                        'value' => $image->width,
                        'placeholder' => 'Width'
                    ]) ?>
                    <span id="resize-link-btn">
                        <?=
                            $this->Html->icon('link',[
                                'class' => 'resize-link link',
                            ])
                        ?>
                    </span>
                    <?= $this->Form->input('height', [
                        'label' => false,
                        'value' => $image->height,
                        'placeholder' => 'Height'
                    ]) ?>
                    <?= $this->Form->end() ?>
                </div>
                <div class="crop-control">
                    <span class="crop active btn">
                        <?= $this->Html->icon('crop') ?>
                    </span>
                </div>
            </div>
        <!--</ul>-->
    </div>
</div>

<?= $this->Html->css('UploadManager.lib/imgareaselect/imgareaselect-default') ?>
<?= $this->Html->css('UploadManager.image_manipulation') ?>
<?= $this->Html->script('UploadManager.lib/jquery.imgareaselect.min') ?>
<?= $this->Html->script('UploadManager.toggle') ?>
<?= $this->Html->script('UploadManager.resize') ?>
<?= $this->Html->script('UploadManager.link') ?>



