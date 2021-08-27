<?php

 namespace App\Http\Modules;

 use App\Models\NotificationTemplate;

 class NotificationTemplateModule extends BaseModule
 {
     /**
      * CategoryModule constructor.
      * 
      */
     public function __construct()
     {
         $this->model = new NotificationTemplate();
         $this->eagers = [];
         $this->query = $this->model->newQuery();
     }
 } 