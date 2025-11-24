<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;
    protected $fillable = ['title','type','venue','doi','status','target_deadline'];

    public function projects()   { return $this->belongsToMany(Project::class)->withTimestamps(); }
    public function authors()    { return $this->hasMany(Author::class); }
    public function attachments(){ return $this->morphMany(Attachment::class, 'attachable'); }
    public function comments()   { return $this->morphMany(Comment::class, 'commentable'); }
    public function tags()       { return $this->morphToMany(Tag::class, 'taggable'); }
}
