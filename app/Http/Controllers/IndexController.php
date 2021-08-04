<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\BaseController;

class IndexController extends BaseController
{
    protected $name = 'index';

    /**
     * Route for health checking the service
     * @return json
     */
    public function healthCheck()
    {
        return $this->sendResponse(
            ['Hello' => 'World!']
        );
    }
}
