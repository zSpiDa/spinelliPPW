<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    public function projects()     { return $this->morphedByMany(Project::class, 'taggable'); }
    public function publications() { return $this->morphedByMany(Publication::class, 'taggable'); }
}
