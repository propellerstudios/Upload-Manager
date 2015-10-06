<!--
    This element must be wrapped with a form.  It is done
    this way to support forms with fields other than this one.
-->
<?php if(isset($owner)): ?>
    <input id="owner" type="hidden" 
        name="owner" value="<?= $owner ?>" />
<?php endif; ?>

<div class="fileUpload btn">
    <span class="upload-span">
      <i class="fa fa-upload"></i>
    </span>
    <input 
        id="uploads" class="upload" type=file multiple
        name="uploads[]" />
</div>
<?= $this->Html->css('UploadManager.upload_button') ?>
