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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('non_tenant_id')->constrained('non_tenants')->onDelete('cascade')->onUpdate('cascade');
            $table->string('location_details');
            $table->string('location_name')->nullable()->default('location');
            $table->string('unit_number');
            $table->string('unit_type');
            $table->string('contact_name')->nullable();
            $table->string('contact_mobile');
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
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
        Schema::dropIfExists('addresses');
    }
};
