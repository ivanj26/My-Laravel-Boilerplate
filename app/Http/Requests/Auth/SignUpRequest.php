<?php

namespace App\Http\Requests\Auth;

use App\Helper\GeneralHelper;
use App\Http\Requests\BaseRequest;

class SignUpRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'required|phone:AUTO,mobile|unique:users,phone',
            'email' => 'email:filter|string|unique:users,email',
            'password' => 'required||min:8',
            'confirmPassword' => 'required|same:password',
        ];
    }

    public function validated()
    {
        $validated = $this->validator->validated();
        unset($validated['confirmPassword']);

        $validated = GeneralHelper::toSnakeCase($validated);
        $validated['password'] = bcrypt($validated['password']);
        return $validated;
    }
}
