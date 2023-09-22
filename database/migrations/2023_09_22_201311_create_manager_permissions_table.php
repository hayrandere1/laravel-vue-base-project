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
        Schema::create('manager_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_group_id')->constrained('manager_role_groups')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('manager_roles')->cascadeOnDelete();
            $table->enum('filter_type', ['everyone']);
            $table->longText('filter_values')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_permissions');
    }
};
