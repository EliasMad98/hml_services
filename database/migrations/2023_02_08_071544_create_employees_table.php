<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            // $table->foreignId('job_type_id')->constrained('job_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('job_type_id')->nullable();
            $table->foreign('job_type_id')->references('id')->on('job_types')->onDelete('set null');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('fcm_token')->nullable();
            $table->string('password');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
