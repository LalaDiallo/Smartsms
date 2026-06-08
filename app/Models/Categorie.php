<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    // Relation : une catégorie peut avoir plusieurs templates
    public function templates()
    {
        return $this->hasMany(Templates::class);
    }
}
