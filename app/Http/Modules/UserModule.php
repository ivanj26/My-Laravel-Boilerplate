<?php

namespace App\Http\Modules;

use App\Models\User;

class UserModule extends BaseModule
{
    /**
     * UserModule constructor.
     * 
     */
    public function __construct()
    {
        $this->model = new User();
        $this->eagers = [];
        $this->query = $this->model->newQuery();
    }
}