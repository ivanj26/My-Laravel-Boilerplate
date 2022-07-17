<?php

namespace App\Http\Controllers\Api\Captcha;

use App\Http\Controllers\Api\BaseController;

class CaptchaController extends BaseController
{
    /**
     * The service name.
     *
     * @var String
     */
    protected $name = 'captcha service';

    /**
     * Reload captcha image
     *
     */
    public function refreshCaptcha()
    {
        return $this->sendResponse([ 'captcha' => captcha_img() ]);
    }
}
