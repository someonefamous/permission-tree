<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use SomeoneFamous\PermissionTree\Models\Permission;

class SetUpPermissionsTables extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->default(1)->constrained('permissions');
            $table->string('code')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('permittables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained();
            $table->unsignedBigInteger('permittable_id')->nullable();
            $table->string('permittable_type')->nullable();
            $table->boolean('allowed')->default(true);
            $table->timestamps();

            $table->unique(
                ['permission_id', 'permittable_id', 'permittable_type'],
                'permissions_permittable_unique'
            );
        });

        if (!App::runningUnitTests()) {
            Permission::createManyByArray(config('sf_permissions.available_permissions'));
        }
    }
}
