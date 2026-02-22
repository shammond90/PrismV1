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

    public function paperwork()
    {
        return $this->hasMany(TemplatePaperwork::class);
    }

    public function templateNotes()
    {
        return $this->hasMany(TemplateNote::class);
    }

    public function staffing()
    {
        return $this->hasMany(TemplateStaffing::class);
    }

    public function schedules()
    {
        return $this->hasMany(TemplateSchedule::class);
    }

    public function files()
    {
        return $this->hasMany(TemplateFile::class);
    }
}
