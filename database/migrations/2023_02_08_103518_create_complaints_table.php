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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('user_type');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->string('title');
            $table->string('description');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->date('service_started')->nullable();
            $table->date('service_ended')->nullable();
            $table->boolean('needs_spare')->nullable()->default(false);
            $table->boolean('determine_price')->nullable()->default(false);
            $table->boolean('urgent')->nullable()->default(false);
            $table->boolean('needs_visit')->nullable()->default(false);
            $table->string('price')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('paid')->nullable()->default(false);
            $table->boolean('job_finished')->nullable()->default(false);
            $table->string('rate')->nullable();
            $table->boolean('canceled')->nullable()->default(false);
            $table->morphs('addressable');
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
        Schema::dropIfExists('complaints');
    }
};
