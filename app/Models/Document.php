<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * Automatically fill and update timestamps.
     *
     * @var array
     */
    public $timestamps = true;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     *
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss');
            $model->updated_at = Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss');
        });
    }

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
