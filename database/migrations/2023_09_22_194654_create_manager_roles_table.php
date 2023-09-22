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
        Schema::create('manager_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent')->nullable()->constrained('manager_roles')->cascadeOnDelete();
            $table->string('route_name')->nullable();
            $table->string('controller');
            $table->string('action');
            $table->string('model')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_roles');
    }
};
