<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['project_id','assignee_id','title','description','due_date','status','priority'];

    public function project()  { return $this->belongsTo(Project::class); } //uno a molti
    public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }
}
