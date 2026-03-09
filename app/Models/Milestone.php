<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;


    protected $fillable = ['project_id','task_id','title','due_date','status'];
    public function project() { return $this->belongsTo(Project::class); } //uno a molti
    public function task() { return $this->hasMany(Task::class); }
}
