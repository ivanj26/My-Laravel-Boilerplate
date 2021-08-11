<?php

namespace App\Http\Requests;

use App\Helper\GeneralHelper;
use Illuminate\Support\Str;

class CollectionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => 'sometimes|required|numeric',
            'limit' => 'sometimes|required|numeric',
            'orderBy' => 'sometimes|required|string',
            'sortBy' => 'sometimes|required|string|in:desc,asc',
            'query' => 'nullable|string'
        ];
    }

    public function validated()
    {
        $validated = $this->validator->validated();
        $validated['orderBy'] = Str::snake($validated['orderBy'] ?? '');

        if (empty($validated['orderBy'])) {
            $validated['orderBy'] = 'created_at';
        }

        if (empty($validated['sortBy'])) {
            $validated['sortBy'] = 'asc';
        }

        if (empty($validated['limit'])) {
            $validated['limit'] = 10;
        }

        $query = $this->query();
        $remove = ['page', 'limit', 'sortBy', 'orderBy'];
        $additionalQuery = array_diff_key($query, array_flip($remove));
        foreach ($additionalQuery as $key => $value) {
            $validated[$key] = Str::snake($value ?? '');
        }

        return GeneralHelper::toSnakeCase($validated);
    }
}