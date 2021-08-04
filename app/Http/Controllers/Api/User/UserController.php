<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\BaseController;
use App\Http\Modules\UserModule;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    /**
     * The service name.
     * 
     * @var String
     */
    protected $name = 'user service';

    /**
     * User module.
     * 
     * @var UserModule
     */
    private $module;

    public function __construct()
    {
        $this->module = new UserModule(new User);
    }
}
