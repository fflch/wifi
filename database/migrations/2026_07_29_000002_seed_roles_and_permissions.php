<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'aprovar wifi', 'guard_name' => 'senhaunica']);

        $servidor = Role::firstOrCreate(['name' => 'Servidor', 'guard_name' => 'senhaunica']);
        $servidorUsp = Role::firstOrCreate(['name' => 'ServidorUSP', 'guard_name' => 'senhaunica']);

        $servidor->givePermissionTo('aprovar wifi');
    }

    public function down(): void
    {
        Permission::where('name', 'aprovar wifi')->delete();
        Role::whereIn('name', ['Servidor', 'ServidorUSP'])->delete();
    }
};
