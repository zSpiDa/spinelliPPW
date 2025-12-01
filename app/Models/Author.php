<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model //collega le pubblicazioni e utenti
{
    use HasFactory;

    protected $fillable = ['publication_id','user_id','order','is_corresponding'];
    // Opz.: cast boolean per evitare 0/1
    protected $casts = ['is_corresponding' => 'boolean']; //converte automaticamente il valore della colonna da 0/1 a true/false

    public function publication() { return $this->belongsTo(Publication::class); } //uno a molti
    public function user()        { return $this->belongsTo(User::class); } //uno a molti
}
