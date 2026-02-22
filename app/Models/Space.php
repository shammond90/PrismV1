<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    use HasFactory;

    protected $fillable = ['building_id', 'name', 'type', 'notes'];

    public function building()
    {
        return $this->belongsTo(Building::class);
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
