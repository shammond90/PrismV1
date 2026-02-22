<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionTemplate extends Model
{
    use HasFactory;

    protected $table = 'production_templates';

    protected $fillable = [
        'show_catalogue_id',
        'name',
        'description',
        'notes',
    ];

    public function showCatalogue()
    {
        return $this->belongsTo(ShowCatalogue::class);
    }
}
