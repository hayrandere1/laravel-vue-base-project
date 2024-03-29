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
        Schema::create('report_session_details', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('user_type')->index();
            $table->string('user_id')->index();
            $table->string('ip_address')->index();
            $table->text('user_agent')->nullable();
            $table->integer('duration')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_session_details');
    }
};
