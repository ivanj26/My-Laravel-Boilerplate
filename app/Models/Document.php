<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends BaseModel
{
    use HasFactory;

    /**
     * //////////////////////
     * Defining relationship
     * //////////////////////
     */

    /**
     * Get all of the models that own document.
     */
    public function documentable()
    {
        return $this->morphTo(__FUNCTION__, 'table', 'table_id');
    }
    
     /**
     * Get uploader data info.
     * 
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id', 'id');
    }
}
