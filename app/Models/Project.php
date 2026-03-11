<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['title','code','funder','start_date','end_date','status','description', 'file_path'];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'effort')
            ->withTimestamps();
    }

    public function milestones() { return $this->hasMany(Milestone::class); } //uno a molti

    public function publications()
    {
        // pivot: project_publication (project_id, publication_id)
        return $this->belongsToMany(Publication::class)->withTimestamps();
    }

    public function attachments() { return $this->morphMany(Attachment::class, 'attachable'); } //polimorfica uno a molti
    public function comments()    { return $this->morphMany(Comment::class, 'commentable'); }
    public function tags()        { return $this->morphToMany(Tag::class, 'taggable'); } //polimorfica molti a molti
    public function tasks()       { return $this->hasMany(Task::class); }
    public function group()       { return $this->belongsTo(Groups::class); } //molti a uno
}
