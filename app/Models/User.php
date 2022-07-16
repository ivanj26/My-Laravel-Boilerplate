<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Automatically fill and update timestamps.
    *
    * @var array
    */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'zipcode',
        'city',
        'province',
        'country'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
    * The attributes that should be cast to native types.
    *
    * @var array
    */
    protected $casts = [

    ];

    /**
     * Return true if the model may owns document in document table.
     *
     * @return bool
     */
    public function hasDocumentable()
    {
        return true;
    }

    /**
     * Check user role, is user 'admin'?
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * //////////////////////
     * Defining relationship
     * //////////////////////
     */
    public function avatar()
    {
        return $this->morphOne(Document::class, 'documentable', 'table', 'table_id')
            ->whereRaw('`documents`.`type` = ?', ['images'])
            ->latest();
    }

    // code here!
}
