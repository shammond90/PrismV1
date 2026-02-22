<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'contact_id',
        'employable_type',
        'employable_id',
        'position',
        'department',
        'start_date',
        'end_date',
    ];

    /**
     * Get the parent employable model (Company, Venue, Building, or Space).
     */
    public function employable()
    {
        return $this->morphTo();
    }

    /**
     * Legacy relation for backward compatibility.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
