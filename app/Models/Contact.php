<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'first_name',
        'middle_name',
        'last_name',
        'given_name',
        'pronouns',
        'locations',
        'notes',
    ];

    protected $casts = [
        'locations' => 'array',
    ];

    public function employments()
    {
        return $this->hasMany(\App\Models\Employment::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'employments')
            ->withPivot(['position','department','start_date','end_date'])
            ->withTimestamps();
    }

    public function emails()
    {
        return $this->hasMany(\App\Models\EmailAddress::class);
    }

    public function phones()
    {
        return $this->hasMany(\App\Models\PhoneNumber::class);
    }

    public function productions()
    {
        return $this->belongsToMany(\App\Models\Production::class, 'contact_production')
            ->withPivot(['role', 'department', 'notes', 'departments', 'positions'])
            ->withTimestamps();
    }
}
