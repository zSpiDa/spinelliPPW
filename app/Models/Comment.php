<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['commentable_type','commentable_id','user_id','body'];

    public function commentable() { return $this->morphTo(); }
    public function user()        { return $this->belongsTo(User::class); }
}
