<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddCompaniesPackage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('package_id')->nullable()->after('name')->constrained('packages');
            $table->foreignId('main_user_id')->nullable()->after('package_id')->constrained('users');
            $table->foreignId('supervisor_id')->nullable()->after('main_user_id')->constrained('managers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
