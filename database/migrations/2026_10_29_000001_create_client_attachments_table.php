<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Lampiran Sertifikat & Report tiap jadwal audit (IA, SA1, SA2, RC), satu baris per file.
        // Tabel terpisah agar tiap file disimpan dengan query sendiri dan tidak melebihi max_allowed_packet bersama-sama.
        Schema::create('client_attachments', function (Blueprint $table) {
            $table->increments('id_client_attachment'); // INT AUTO INCREMENT
            $table->unsignedInteger('id_client');
            $table->string('type', 30); // mis. ia_certificate, sa1_report

            $table->binary('file');
            $table->string('file_name', 255);
            $table->string('file_mime', 100);

            $table->timestamps(); // otomatis created_at & updated_at
            $table->unique(['id_client', 'type']);
            $table->foreign('id_client')->references('id_client')->on('clients')->onDelete('cascade');
        });

        // BLOB di MySQL maksimal 64KB, ubah ke MEDIUMBLOB (maksimal 16MB)
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE client_attachments MODIFY file MEDIUMBLOB NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_attachments');
    }
};
