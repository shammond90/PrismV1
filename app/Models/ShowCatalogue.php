<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShowCatalogue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'version',
        'description',
        'choreography_by',
        'rights_licensing',
        'tags',
        'thumbnail',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function paperwork()
    {
        return $this->hasMany(CataloguePaperwork::class);
    }

    public function notes()
    {
        return $this->hasMany(CatalogueNote::class);
    }

    public function catalogueContacts()
    {
        return $this->hasMany(CatalogueContact::class);
    }

    public function files()
    {
        return $this->hasMany(CatalogueFile::class);
    }

    public function productionTemplates()
    {
        return $this->hasMany(ProductionTemplate::class);
    }

    public function shows()
    {
        return $this->hasMany(Show::class);
    }
}