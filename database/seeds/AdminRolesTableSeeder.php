<?php

use App\Domain\AdminRoles\Models\AdminRole;
use App\Domain\Users\Models\User;
use Illuminate\Database\Seeder;

class AdminRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRoleId = DB::table('admin_roles')->insertGetId([
            'title' => 'Администратор',
            'permissions' => json_encode(AdminRole::permissionsList()),
        ]);

        User::where('role', 'admin')->update([
            'admin_role_id' => $adminRoleId
        ]);
    }
}
