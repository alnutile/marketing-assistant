<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;

class FixTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix missing teams';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (User::all() as $user) {

            $team = Team::updateOrCreate([
                'name' => $user->name,
                'user_id' => $user->id,
                'personal_team' => true,
            ]);

            $user->update([
                'current_team_id' => $team->id,
            ]);

            if (! $team->hasUser($user)) {
                $team->users()->attach($user, ['role' => 'admin']);
            } else {
                // User is already in the team, you might want to update their role instead
                $team->users()->updateExistingPivot($user->id, ['role' => 'admin']);
            }

            foreach ($user->campaigns as $campaign) {
                $campaign->update([
                    'team_id' => $team->id,
                ]);
            }
        }
    }
}
