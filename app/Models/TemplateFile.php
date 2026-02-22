<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateFile extends Model
{
    use HasFactory;

    protected $table = 'template_files';

    protected $fillable = [
        'production_template_id',
        'type',
        'title',
        'file_path',
        'original_filename',
        'notes',
    ];

    public function productionTemplate()
    {
        return $this->belongsTo(ProductionTemplate::class);
    }
}
