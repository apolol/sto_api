<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'manager']);

        $permission = Permission::create(['name' => 'view result']);

        $adminRole->givePermissionTo($permission);

        $users = User::all();
        foreach ($users as $user){
            if (in_array($user->id, ['7560883a-6f58-408d-919d-b7843dfd76b9'])){
                $user->assignRole($userRole);
            }else{
                $user->assignRole($adminRole);
            }
        }
    }
}
