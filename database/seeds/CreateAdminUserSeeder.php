<?php

use Illuminate\Database\Seeder;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = App\User::create([
            'name' => 'Developer',
            'email' => 'developer@thisapp.com',
            'password' => bcrypt('developer'),
        ]);
        
        $role = Spatie\Permission\Models\Role::create([
            'name' => 'Developer',
        ]);
        
        $permissions = Spatie\Permission\Models\Permission::pluck('id', 'id')->all();
        
        $role->syncPermissions($permissions);
        
        $user->assignRole([$role->id]);
    }
}
