<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id', 'name', 'event_type', 'start_at', 'end_at', 'space_id', 'departments', 'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'departments' => 'array',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }
}
