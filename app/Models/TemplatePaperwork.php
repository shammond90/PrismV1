<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatePaperwork extends Model
{
    use HasFactory;

    protected $table = 'template_paperwork';

    protected $fillable = [
        'production_template_id',
        'title',
        'department',
        'file_path',
        'original_filename',
        'notes',
    ];

    public function productionTemplate()
    {
        return $this->belongsTo(ProductionTemplate::class);
    }
}
