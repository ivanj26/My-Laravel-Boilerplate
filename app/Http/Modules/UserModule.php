<?php

namespace App\Http\Modules;

use App\Models\User;

class UserModule extends BaseModule
{
    /**
     * UserModule constructor.
     * 
     * @param User user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
        $this->eagers = [];
        $this->query = $this->model->newQuery();
    }
}