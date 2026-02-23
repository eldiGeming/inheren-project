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
        Schema::create('certifications', function (Blueprint $table) {
            $table->increments('id_certification'); // INT AUTO INCREMENT
            $table->unsignedInteger('id_company'); // harus unsigned
            $table->unsignedInteger('id_type_certificate'); // harus unsigned
            $table->string('number_certificate', 255)->unique();
            $table->date('issues_date');
            $table->date('expired_date');
            $table->text('barcode_certificate');
            $table->boolean('is_deleted')->default(false);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps(); // otomatis created_at & updated_at
            $table->foreign('id_company')->references('id_company')->on('companies')->onDelete('cascade');
            $table->foreign('id_type_certificate')->references('id_type_certificate')->on('type_certificates')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
