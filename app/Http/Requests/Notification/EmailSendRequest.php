<?php

namespace App\Http\Requests\Notification;

use App\Http\Requests\BaseRequest;

class EmailSendRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'data' => 'required|array',
            'name' => 'required|string|exists:notification_templates,name',
            'attachments' => 'sometimes|array',
            'attachments.*.content' => [
                'required',
                'string',
                // - validate if value is base64 type or not
                function($attribute, $value, $fail) {
                    // Check base64 string using regex
                    if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
                        return $fail('The ' . $attribute . ' value is an invalid base64');
                    }

                    // Decode the string in strict mode and check the results
                    $decoded = base64_decode($value, true);
                    if(false === $decoded) {
                        return $fail('The ' . $attribute . ' value is an invalid base64');
                    }

                    // Encode the string again
                    if(base64_encode($decoded) != $value) {
                        return $fail('The ' . $attribute . ' value is an invalid base64');
                    }

                    return true;
                }
            ],
            'attachments.*.filename' => 'required|string',
            'to' => 'required|string|email:filter',
            'cc' => 'sometimes|array',
            'cc.*' => 'required|string|email:filter'
        ];
    }
}