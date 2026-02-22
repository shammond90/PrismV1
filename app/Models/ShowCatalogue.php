<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowCatalogue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'version',
        'description',
        'choreography_by',
    ];
}