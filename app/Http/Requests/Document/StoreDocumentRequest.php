<?php

namespace App\Http\Requests\Document;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\File;

class StoreDocumentRequest extends BaseRequest
{
    public function documentableKeys()
    {
        $results = [];
        $files = File::allFiles(app_path() . '/Models');
        foreach ($files as $file) {
            $name = $file->getFilenameWithoutExtension();
            $modelPath = 'App\Models\\'. $name;

            try {
                $model = app($modelPath);
                if ($model->hasDocumentable()) {
                    $results[] = $modelPath;
                }
            } catch (\Exception $e) {
                // ignore error
                // if its not instantiable
                continue;
            }
        }

        return implode(',', $results);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => [
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
            'table' => "sometimes|string|in:{$this->documentableKeys()}",
            'tableId' => "required_with:table|numeric",
            'filename' => 'required|string'
        ];
    }
}
