# UploadManager plugin for CakePHP

###Installation
As of right now, a Composer account is being set up and so using composer to install this is not yet an option.  

In the meantime, there is nothing wrong with cloning this repo in your /path/to/app/plugins directory. 

###Model Relationships
An Upload can be associated with a particular Owner model, the Upload will belongTo that Owner model.  Based on the owner_id and owner_table fields in an Upload entity, the Uploads associated with an Owner can be found.  

The relationship is as follows:  Owner---<Upload  (One to Many) 

In order to benefit from this relationship, it is required that the desired OwnerTable class uses the following [https://github.com/propellerstudios/Upload-Manager/blob/master/src/Model/Behavior/UploadBehavior.php](behavior):

```php
public function initialize(array $config){
  // Your code here
  $this->addBehavior('UploadManager.Upload'); // Upload behavior
  // Or here
}
```
