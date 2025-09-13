<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Backup extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'file_url'];
}
