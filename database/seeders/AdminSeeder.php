<?php

namespace Database\Seeders;

use App\Actions\Jetstream\CreateTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate([
            'email' => env('ADMIN_EMAIL'),
        ],
            [
                'password' => bcrypt(env('ADMIN_PASSWORD')),
                'name' => 'Admin',
            ]);

        if (! Team::where('name', 'Admin Team')->exists()) {
            (new CreateTeam)->create($user, [
                'name' => 'Admin Team',
            ]);
        }

    }
}
