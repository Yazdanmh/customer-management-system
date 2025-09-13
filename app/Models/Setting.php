<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['site_name', 'email', 'phone', 'address', 'about', 'site_logo', 'site_favicon'];
    protected $casts = [
        'site_name' => 'array',
        'address' => 'array',
        'about' => 'array',
    ];


}
