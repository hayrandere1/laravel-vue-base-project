<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_session_dailies', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('user_type')->nullable()->index();
            $table->string('user_id')->nullable()->index();
            $table->string('ip_address')->nullable()->index();
            $table->integer('duration')->default(0);
            $table->integer('count')->default(0);
            $table->unique(['date', 'user_type', 'user_id', 'ip_address']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_session_dailies');
    }
};
