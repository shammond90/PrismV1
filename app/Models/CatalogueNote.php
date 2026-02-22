<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueNote extends Model
{
    use HasFactory;

    protected $table = 'catalogue_notes';

    protected $fillable = [
        'show_catalogue_id',
        'note_type',
        'department',
        'author',
        'note_text',
    ];

    public function showCatalogue()
    {
        return $this->belongsTo(ShowCatalogue::class);
    }
}
