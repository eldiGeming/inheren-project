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
        Schema::create('type_certificates', function (Blueprint $table) {
            $table->increments('id_type_certificate'); // INT AUTO INCREMENT
            $table->string('type_certificate', 255);
            $table->text('description_certificate');
            $table->boolean('is_deleted')->default(false);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_certifications');
    }
};
