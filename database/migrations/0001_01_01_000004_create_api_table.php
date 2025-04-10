<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('password', 500)->nullable();
            $table->string('description')->nullable();
            $table->boolean('has_certificate')->default(false);
            $table->timestamps();
        });

        Schema::create('api_user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained();
            $table->foreignId('api_user_id')->constrained();
        });

        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('origin_app')->nullable();
            $table->string('origin_type')->nullable();
            $table->string('uri');
            $table->enum('method', ['get', 'post', 'put', 'patch', 'delete'])->default('get');
            $table->text('headers')->nullable();
            $table->text('params')->nullable();
            $table->integer('code');
            $table->boolean('success');
            $table->string('message');
            $table->foreignId('api_user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_users');
        Schema::dropIfExists('api_role_user');
        Schema::dropIfExists('api_logs');
    }
};
