<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateNote extends Model
{
    use HasFactory;

    protected $table = 'template_notes';

    protected $fillable = [
        'production_template_id',
        'note_type',
        'department',
        'author',
        'note_text',
    ];

    public function productionTemplate()
    {
        return $this->belongsTo(ProductionTemplate::class);
    }
}
