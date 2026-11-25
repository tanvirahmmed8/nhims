<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->string('type', 50);
            $table->string('district', 100);
            $table->string('address', 255);
            $table->string('phone', 30)->nullable();
            $table->string('status', 30)->default('active')->index();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->string('status', 30)->default('active')->after('password')->index();
        });

        Schema::create('facility_user', function (Blueprint $table) {
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->date('active_from')->nullable();
            $table->date('active_until')->nullable();
            $table->timestamps();

            $table->primary(['facility_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_user');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'status']);
        });

        Schema::dropIfExists('facilities');
        Schema::dropIfExists('roles');
    }
};
