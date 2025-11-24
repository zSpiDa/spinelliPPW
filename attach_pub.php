<?php
// Script di utilità per eseguire operazioni Eloquent senza interattività
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Models\Publication;
use Illuminate\Support\Facades\DB;

try {
    $u = User::firstOrCreate([
        'email' => 'prof.demo@example.com'
    ], [
        'name' => 'Prof. Demo',
        'password' => bcrypt('password'),
        'role' => 'researcher'
    ]);

    $p = Project::firstOrCreate([
        'code' => 'PRIN2022'
    ], [
        'title' => 'MORPHEUS',
        'funder' => 'MUR',
        'description' => 'Framework human-factor-aware per cybersecurity'
    ]);

    $pub = Publication::create([
        'title' => 'XAI for Phishing',
        'status' => 'drafting'
    ]);

    $p->publications()->syncWithoutDetaching([$pub->id => []]);

    echo "OK project_id={$p->id} publication_id={$pub->id} user_id={$u->id}\n";

    // mostra riga nel pivot per conferma
    $row = DB::table('project_publication')->where('project_id', $p->id)->where('publication_id', $pub->id)->first();
    echo "pivot: ";
    print_r($row);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
