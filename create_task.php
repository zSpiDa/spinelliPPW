<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

try {
    // Ensure user exists
    $u = User::firstOrCreate(
        ['email' => 'prof.demo@example.com'],
        ['name' => 'Prof. Demo', 'password' => bcrypt('password'), 'role' => 'researcher']
    );

    // Ensure project exists
    $p = Project::firstOrCreate(
        ['code' => 'PRIN2022'],
        ['title' => 'MORPHEUS', 'funder' => 'MUR', 'description' => 'Framework human-factor-aware per cybersecurity']
    );

    // Create task with required fields
    $t = Task::create([
        'project_id' => $p->id,
        'assignee_id' => $u->id,
        'title' => 'Kickoff meeting',
        'description' => 'Riunione iniziale progetto',
        'status' => 'open',
        'priority' => 'medium',
    ]);

    echo "OK task_id={$t->id} project_id={$p->id} assignee_id={$u->id}\n";
    $row = DB::table('tasks')->where('id', $t->id)->first();
    print_r($row);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
