<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Templates extends Model
{

    use HasFactory;

    protected $fillable = [
        'name',
        'extrait',
        'channel',
        'content',
        'branding_logo',
        'branding_colors',
        'sector',
        'language_id',
        'category_id',
        'client_id',
        'is_favori'
    ];

    // Relation avec la catégorie
    public function category()
    {
        return $this->belongsTo(Categorie::class);
    }

    // Relation avec les tags (many-to-many)
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag_template', 'template_id', 'tag_id');
    }

    public function language()
    {
        return $this->belongsTo(Languages::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campagnes::class);
    }

     public function client()
    {
        return $this->belongsTo(Clients::class);
    }
}
