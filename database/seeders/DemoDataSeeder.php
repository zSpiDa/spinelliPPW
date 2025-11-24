<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\{User,Project,Publication,Author,Milestone,Task,Tag};

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1) Utenti (8 random) + 1 PI
        $users = User::factory()->count(8)->create();
        $pi = User::firstOrCreate(
            ['email' => 'pi@example.com'],
            ['name' => 'Principal Investigator', 'password' => Hash::make('password'), 'role' => 'pi']
        );

        // 2) Tag
        $tags = Tag::factory()->count(8)->create();

        // 3) Progetti
        $projects = Project::factory()->count(5)->create();

        foreach ($projects as $project) {
            // Associa PI (evita duplicati)
            $project->users()->syncWithoutDetaching([
                $pi->id => ['role' => 'pi', 'effort' => 0.3]
            ]);

            // 2-3 membri con ruoli coerenti (lowercase)
            $members = $users->random(3);
            foreach ($members as $m) {
                // pivot role must match project_user enum: 'pi','manager','member'
                $role = collect(['manager','member'])->random();
                $project->users()->syncWithoutDetaching([
                    $m->id => ['role' => $role, 'effort' => $faker->randomFloat(2, 0.1, 0.8)]
                ]);
            }

            // Milestone
            Milestone::factory()->count(3)->create(['project_id' => $project->id]);

            // Task
            Task::factory()->count(6)->make()->each(function($t) use ($project, $users) {
                $t->project_id  = $project->id;
                $t->assignee_id = $users->random()->id;
                $t->save();
            });

            // Pubblicazioni collegate
            $pubs = Publication::factory()->count(2)->create();
            foreach ($pubs as $pub) {
                $project->publications()->syncWithoutDetaching([$pub->id]);

                // Autori (ordine + corresponding)
                $coauthors = $users->random(3)->values();
                foreach ($coauthors as $i => $u) {
                    Author::create([
                        'publication_id'   => $pub->id,
                        'user_id'          => $u->id,
                        'order'            => $i + 1,
                        'is_corresponding' => $i === 0,
                    ]);
                }

                // Tag su pubblicazioni
                $pub->tags()->attach($tags->random(2)->pluck('id'));
            }

            // Tag su progetto
            $project->tags()->attach($tags->random(3)->pluck('id'));

            // Allegati & Commenti polimorfici
            $project->attachments()->create([
                'path'        => 'docs/'.$project->code.'_plan.pdf',
                'uploaded_by' => $pi->id,
            ]);
            $project->comments()->create([
                'user_id' => $pi->id,
                'body'    => 'Benvenuti nel progetto '.$project->title.'!',
            ]);
        }
    }
}
