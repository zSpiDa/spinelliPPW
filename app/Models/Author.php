<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['publication_id','user_id','order','is_corresponding'];

    public function publication() { return $this->belongsTo(Publication::class); }
    public function user()        { return $this->belongsTo(User::class); }
}
