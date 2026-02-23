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
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id_company'); // INT AUTO INCREMENT

            $table->string('name_company', 255);
            $table->text('address_company');
            $table->string('phone_company', 13);
            $table->string('email_company', 255);
            $table->text('description_company');
            $table->string('logo_company', 255);

            $table->boolean('is_deleted')->default(false);

            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps(); // otomatis created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
