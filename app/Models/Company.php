<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'industry',
        'website',
        'notes',
    ];

    public function setWebsiteAttribute($value)
    {
        if (!empty($value) && !preg_match('#^https?://#i', $value)) {
            $value = 'http://' . $value;
        }

        $this->attributes['website'] = $value;
    }

    public function employments()
    {
        return $this->hasMany(Employment::class);
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'employments')
            ->withPivot(['position','department','start_date','end_date'])
            ->withTimestamps();
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
