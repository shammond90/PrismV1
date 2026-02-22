<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'name', 'type', 'website', 'notes'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function spaces()
    {
        return $this->hasMany(Space::class);
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable')->orderByDesc('primary')->orderBy('id');
    }

    public function primaryAddress()
    {
        return $this->morphOne(Address::class, 'addressable')->where('primary', true);
    }
}
