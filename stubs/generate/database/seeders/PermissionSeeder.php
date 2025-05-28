<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Thehouseofel\Kalion\Infrastructure\Models\Permission;
use Thehouseofel\Kalion\Infrastructure\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin            = Role::query()->create(['name' => 'admin', 'all_permissions' => true]);
        $role_writer           = Role::query()->create(['name' => 'writer']);
        $role_reader           = Role::query()->create(['name' => 'reader']);
        $role_isImportantGroup = Role::query()->create(['name' => 'is_important_group', 'is_query' => true]);

        $permission_seePostDetail = Permission::query()->create(['name' => 'see_post_detail']);
        $permission_filterPosts   = Permission::query()->create(['name' => 'filter_posts']);
        $permission_seeTags       = Permission::query()->create(['name' => 'see_tags']);
        $permission_adminTags     = Permission::query()->create(['name' => 'admin_tags']);

        $role_writer->permissions()->attach($permission_seePostDetail->id);
        $role_writer->permissions()->attach($permission_filterPosts->id);
        $role_writer->permissions()->attach($permission_seeTags->id);

        $role_reader->permissions()->attach($permission_seePostDetail->id);

        $role_isImportantGroup->permissions()->attach($permission_adminTags->id);

        DB::table('role_user')->insert([
            ['role_id' => $role_admin->id, 'user_id' => 1], // user1 -> admin
            ['role_id' => $role_writer->id, 'user_id' => 2], // user2-> writer
            ['role_id' => $role_reader->id, 'user_id' => 2], // user2 -> reader
            ['role_id' => $role_reader->id, 'user_id' => 3], // user3 -> reader
            ['role_id' => $role_isImportantGroup->id, 'user_id' => 3], // user3 -> isImportantGroup
            ['role_id' => $role_reader->id, 'user_id' => 4], // user4 -> reader
        ]);

    }
}
