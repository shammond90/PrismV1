<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id', 'show_catalogue_id', 'title', 'status', 'opening_date', 'notes', 'choreography_by',
    ];

    protected $casts = [
        'opening_date' => 'date',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function showCatalogue()
    {
        return $this->belongsTo(ShowCatalogue::class);
    }



    public function productions()
    {
        return $this->hasMany(\App\Models\Production::class);
    }
}
