<?php

namespace App\Http\Modules;

use App\Models\Document;

class DocumentModule extends BaseModule
{
    /**
     * DocumentModule constructor.
     * 
     */
    public function __construct()
    {
        $this->model = new Document();
        $this->eagers = [];
        $this->query = $this->model->newQuery();
    }
}