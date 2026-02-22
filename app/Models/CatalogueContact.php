<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueContact extends Model
{
    use HasFactory;

    protected $table = 'catalogue_contacts';

    protected $fillable = [
        'show_catalogue_id',
        'contact_id',
        'role',
        'notes',
    ];

    public function showCatalogue()
    {
        return $this->belongsTo(ShowCatalogue::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
