<?php

namespace App\Http\Requests;

use App\Helper\GeneralHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
    * Triggered when one of the rules failed.
    * @param  \Illuminate\Contracts\Validation\Validator  $validator
    * ...
    */
    protected function failedValidation(Validator $validator)
    {
        $response = [
            'data' => null,
            'message' => 'failed to pass the validator',
            'errors' => $validator->errors()->all(),
            'statusCode' => 422
        ];

        throw new HttpResponseException(
            response()->json($response, 422)
        );
    }

    public function validated()
    {
        $validated = $this->validator->validated();
        return GeneralHelper::toSnakeCase($validated);
    }
}