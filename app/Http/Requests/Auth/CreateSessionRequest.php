<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class CreateSessionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email:filter',
            'password' => 'required|min:8'
        ];
    }
}
