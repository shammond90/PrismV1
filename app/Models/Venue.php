<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'website', 'notes'];

    public function buildings()
    {
        return $this->hasMany(Building::class);
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
