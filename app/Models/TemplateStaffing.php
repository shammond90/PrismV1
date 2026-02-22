<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateStaffing extends Model
{
    use HasFactory;

    protected $table = 'template_staffing';

    protected $fillable = [
        'production_template_id',
        'department',
        'role',
        'quantity',
        'notes',
    ];

    public function productionTemplate()
    {
        return $this->belongsTo(ProductionTemplate::class);
    }
}
