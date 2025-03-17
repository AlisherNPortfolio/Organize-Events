<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // User management
            ['name' => 'View Any User', 'slug' => 'users.view-any', 'description' => 'Can view the list of all users'],
            ['name' => 'View User', 'slug' => 'users.view', 'description' => 'Can view user details'],
            ['name' => 'Create User', 'slug' => 'users.create', 'description' => 'Can create new users'],
            ['name' => 'Update User', 'slug' => 'users.update', 'description' => 'Can update user details'],
            ['name' => 'Delete User', 'slug' => 'users.delete', 'description' => 'Can delete users'],
            ['name' => 'Change User Role', 'slug' => 'users.change-role', 'description' => 'Can change user roles'],

            // Event management
            ['name' => 'View Any Event', 'slug' => 'events.view-any', 'description' => 'Can view the list of all events'],
            ['name' => 'View Event', 'slug' => 'events.view', 'description' => 'Can view event details'],
            ['name' => 'Create Event', 'slug' => 'events.create', 'description' => 'Can create new events'],
            ['name' => 'Update Any Event', 'slug' => 'events.update-any', 'description' => 'Can update any event'],
            ['name' => 'Update Own Event', 'slug' => 'events.update-own', 'description' => 'Can update own events'],
            ['name' => 'Delete Any Event', 'slug' => 'events.delete-any', 'description' => 'Can delete any event'],
            ['name' => 'Delete Own Event', 'slug' => 'events.delete-own', 'description' => 'Can delete own events'],
            ['name' => 'Complete Any Event', 'slug' => 'events.complete-any', 'description' => 'Can mark any event as completed'],
            ['name' => 'Complete Own Event', 'slug' => 'events.complete-own', 'description' => 'Can mark own events as completed'],

            // Participant management
            ['name' => 'View Any Participants', 'slug' => 'participants.view-any', 'description' => 'Can view participants of any event'],
            ['name' => 'View Own Event Participants', 'slug' => 'participants.view-own', 'description' => 'Can view participants of own events'],
            ['name' => 'Manage Any Participants', 'slug' => 'participants.manage-any', 'description' => 'Can manage participants of any event'],
            ['name' => 'Manage Own Event Participants', 'slug' => 'participants.manage-own', 'description' => 'Can manage participants of own events'],

            // Event participation
            ['name' => 'Participate In Events', 'slug' => 'events.participate', 'description' => 'Can participate in events'],

            // Image management
            ['name' => 'Upload Images', 'slug' => 'images.upload', 'description' => 'Can upload images to events'],
            ['name' => 'Delete Any Image', 'slug' => 'images.delete-any', 'description' => 'Can delete any image'],
            ['name' => 'Delete Own Image', 'slug' => 'images.delete-own', 'description' => 'Can delete own uploaded images'],

            // Fine management
            ['name' => 'Apply Fines', 'slug' => 'fines.apply', 'description' => 'Can apply fines to users'],
            ['name' => 'Remove Fines', 'slug' => 'fines.remove', 'description' => 'Can remove fines from users'],
            ['name' => 'View Fines', 'slug' => 'fines.view', 'description' => 'Can view user fines'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $rolePermissions = [
            'creator' => [
                'events.view-any', 'events.view', 'events.create',
                'events.update-own', 'events.delete-own', 'events.complete-own',
                'participants.view-own', 'participants.manage-own',
                'events.participate', 'images.upload', 'images.delete-own',
            ],

            'user' => [
                'events.view-any', 'events.view',
                'events.participate', 'images.upload', 'images.delete-own',
            ],
        ];

        foreach ($rolePermissions as $role => $permissionSlugs) {
            foreach ($permissionSlugs as $slug) {
                $permission = Permission::where('slug', $slug)->first();
                if ($permission) {
                    RolePermission::updateOrCreate(
                        ['role' => $role, 'permission_id' => $permission->id],
                        ['role' => $role, 'permission_id' => $permission->id]
                    );
                }
            }
        }
    }
}
