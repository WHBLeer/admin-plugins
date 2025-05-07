<?php

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
	    $system = Permission::firstOrCreate([
		    'name' => 'system',
		    'guard_name' => 'admin',
		    'display_name' => '系统管理',
		    'icon' => 'fas fa-cog',
	    ]);

	    $permissions = [
		    [
			    'name' => 'plugins.manage',
			    'guard_name' => 'admin',
			    'display_name' => '插件管理',
			    'icon' => 'ph-duotone  ph-squares-four',
			    'route' => 'admin.plugins.index',
			    'parent_id' => $system->id,
		    ]
	    ];
        // 创建权限
        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }

        // 将权限分配给管理员角色
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }
    }

    public function down()
    {
        $permissions = [
	        "plugins.manage"
        ];

        // 从管理员角色移除权限
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->revokePermissionTo($permissions);
        }

        // 删除权限
        Permission::whereIn('name', $permissions)->delete();
    }
};