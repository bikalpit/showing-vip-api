<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{
    /*
     * The table associated with the model.
     */
    protected $table = 'users';

    protected $fillable = [
        'uuid', 'first_name', 'last_name', 'phone', 'email', 'password', 'role', 'agent_role', 'mls_id', 'mls_name', 'phone_verified', 'phone_verification_token', 'email_verified', 'email_verification_token', 'ip_address', 'image', 'address', 'city', 'zipcode', 'state', 'country', 'about'
    ];

    use SoftDeletes;
    
    protected $hidden = [ 'password'];
    protected $dates = ['deleted_at'];
    protected $appends = ['image_name'];

    public function getImageNameAttribute()
    {
        return basename($this->image);
    }
    public function getImageAttribute($value)
    {
        return env('APP_URL').'public/user-images/'.$value;
    }
}