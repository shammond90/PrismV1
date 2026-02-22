<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CataloguePaperwork extends Model
{
    use HasFactory;

    protected $table = 'catalogue_paperwork';

    protected $fillable = [
        'show_catalogue_id',
        'title',
        'department',
        'file_path',
        'original_filename',
        'notes',
    ];

    public function showCatalogue()
    {
        return $this->belongsTo(ShowCatalogue::class);
    }
}
