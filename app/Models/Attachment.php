<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = ['attachable_type','attachable_id','path','uploaded_by'];

    public function attachable() { return $this->morphTo(); }
    public function uploader()   { return $this->belongsTo(User::class, 'uploaded_by'); }
}
