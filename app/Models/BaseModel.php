<?php

 namespace App\Models;

 use Carbon\Carbon;
 use Illuminate\Database\Eloquent\Model;

 abstract class BaseModel extends Model
 {   
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
     * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [

    ];

    /**
     *
    */
    public static function boot()
    {
        parent::boot();
    }

    public function hasDocumentable()
    {
        return false;
    }
} 