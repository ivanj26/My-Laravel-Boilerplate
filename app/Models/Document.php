<?php

namespace App\Models;

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
            $model->created_at = $model->freshTimestamp();
            $model->updated_at = $model->freshTimestamp();
        });
    }

    /**
     * //////////////////////
     * Defining relation ship
     * //////////////////////
     */
    
     /**
     * Get uploader data info.
     * 
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id', 'id');
    }
}
