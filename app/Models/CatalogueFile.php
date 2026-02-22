<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueFile extends Model
{
    use HasFactory;

    protected $table = 'catalogue_files';

    protected $fillable = [
        'show_catalogue_id',
        'type',
        'title',
        'file_path',
        'original_filename',
        'notes',
    ];

    public function showCatalogue()
    {
        return $this->belongsTo(ShowCatalogue::class);
    }
}
