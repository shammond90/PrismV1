<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'show_id', 'title', 'status', 'start_date', 'end_date', 'initial_contact_date', 'space_id', 'primary_company_id', 'primary_contact_id', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'initial_contact_date' => 'date',
    ];

    public function show()
    {
        return $this->belongsTo(Show::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_production')->withTimestamps();
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'contact_production')->withPivot('role', 'department', 'notes', 'departments', 'positions')->withTimestamps();
    }

    public function primaryCompany()
    {
        return $this->belongsTo(Company::class, 'primary_company_id');
    }

    public function primaryContact()
    {
        return $this->belongsTo(Contact::class, 'primary_contact_id');
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function events()
    {
        return $this->hasMany(\App\Models\Event::class);
    }
}
