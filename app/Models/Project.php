<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['title','code','funder','start_date','end_date','status','description'];

    public function users()
    {
        // pivot: project_user (project_id, user_id, role, effort)
        return $this->belongsToMany(User::class)
            ->withPivot(['role','effort'])
            ->withTimestamps();
    }

    public function milestones() { return $this->hasMany(Milestone::class); }

    public function publications()
    {
        // pivot: project_publication (project_id, publication_id)
        return $this->belongsToMany(Publication::class)->withTimestamps();
    }

    public function attachments() { return $this->morphMany(Attachment::class, 'attachable'); }
    public function comments()    { return $this->morphMany(Comment::class, 'commentable'); }
    public function tags()        { return $this->morphToMany(Tag::class, 'taggable'); }
    public function tasks()       { return $this->hasMany(Task::class); }  // <-- aggiunto
}
