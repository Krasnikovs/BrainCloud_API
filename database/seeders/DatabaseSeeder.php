<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\File;
use App\Models\Folder;
use App\Models\Plan;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $mainAdmin = Role::create(['name' => 'Main Administrator']);
        $user = Role::create(['name' => 'User']);
        $fakeUser = Role::create(['name' => 'Fake User']);
        $guest = Role::create(['name' => 'Guest']);

        Permission::create(['name' => 'index.users'])->assignRole($mainAdmin);
        Permission::create(['name' => 'destroy.users'])->assignRole($mainAdmin);

        Permission::create(['name' => 'plans'])->assignRole($mainAdmin);

        Permission::create(['name' => 'supports'])->assignRole($mainAdmin);

        Permission::create(['name' => 'topics'])->assignRole($mainAdmin);

        Permission::create(['name' => 'userFiles'])->assignRole([$user, $fakeUser]);
        Permission::create(['name' => 'userFolders'])->assignRole([$user, $fakeUser]);
        Permission::create(['name' => 'me'])->assignRole([$user, $fakeUser]);
        Permission::create(['name' => 'logout'])->assignRole([$user, $fakeUser]);

        $plans = array(
            ['type' => 'Free', 'description' => 'Free plan, where you have only 20 gigabytes of storage with some limitations', 'max_space' => 20, 'price' => 0],
            ['type' => 'Basic', 'description' => 'You have to buy, to get more storage with no minimal file size or max file size', 'max_space' => 50, 'price' => 9.99],
            ['type' => 'Extra', 'description' => 'More storage, no more limits for the number of files and folders', 'max_space' => 100, 'price' => 14.99],
            ['type' => 'Rugged storage', 'description' => 'Five times more storage than plan Extra and no more limitations', 'max_space' => 500, 'price' => 19.99],
        );
        foreach ($plans as $plan) {
            Plan::create([
                'type' => $plan['type'],
                'description' => $plan['description'],
                'max_space' => $plan['max_space'],
                'price' => $plan['price'],
            ]);
        }

        User::create([
           'image' => 'Test User',
           'name' => 'Main administrator',
//           'email' => 'markuss0303@gmail.com',
            'email' => 'asd@asd.com',
           'password' => Hash::make('admin123'),
        ])->assignRole($mainAdmin);
        Storage::disk('local')->makeDirectory('public/'.'1'.'/'.'TEST_FOLDER_FOR_ADMIN');
        Folder::create([
            'title' => 'TEST_FOLDER_FOR_ADMIN',
            'user_id' => 1,
            'folder_location' => 'public/'.'1'.'/'.'TEST_FOLDER_FOR_ADMIN',
        ]);

        User::create([
            'image' => 'Test User',
            'name' => 'User without admin rights',
            'email' => 'latvian10@gmail.com',
            'password' => Hash::make('admin123'),
        ])->assignRole($user);
        Storage::disk('local')->makeDirectory('public/'.'2'.'/'.'TEST_FOLDER_FOR_USER');
        Folder::create([
            'title' => 'TEST_FOLDER_FOR_USER',
            'user_id' => 2,
            'folder_location' => 'public/'.'2'.'/'.'TEST_FOLDER_FOR_USER',
        ]);

        User::factory()->times(1000)->create()->each(function ($factoryUser) {
            $factoryUser->assignRole('Fake User');
        });

        $topics = [
            'Account issues',
            'Folder issues',
            'File issues',
            'Report a bug',
            'Other',
        ];
        foreach ($topics as $topic) {
            Topic::create([
                'title' => $topic,
            ]);
        }
    }
}
