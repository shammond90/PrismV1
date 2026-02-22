<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateSchedule extends Model
{
    use HasFactory;

    protected $table = 'template_schedules';

    protected $fillable = [
        'production_template_id',
        'template_name',
        'description',
        'schedule_data',
    ];

    public function productionTemplate()
    {
        return $this->belongsTo(ProductionTemplate::class);
    }
}
