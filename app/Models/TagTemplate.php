<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TagTemplate extends Model
{
    use HasFactory;

    protected $table = 'tag_template'; // nom de la table pivot
    protected $fillable = [
        'template_id',
        'tag_id',
    ];

    // Relation vers Template
    public function template()
    {
        return $this->belongsTo(Templates::class);
    }

    // Relation vers Tag
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
