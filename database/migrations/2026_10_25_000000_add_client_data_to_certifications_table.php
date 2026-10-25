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
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('sales_name', 255)->nullable();
            $table->text('scope')->nullable();
            $table->string('legality', 255)->nullable();
            $table->string('pic_name', 255)->nullable();

            // Foto & dokumen disimpan sebagai BLOB di database
            $table->binary('photo')->nullable();
            $table->string('photo_mime', 100)->nullable();
            $table->binary('document')->nullable();
            $table->string('document_name', 255)->nullable();
            $table->string('document_mime', 100)->nullable();

            // Jadwal audit: IA memakai issues_date, lalu SA1, SA2, RC
            $table->date('sa1_date')->nullable();
            $table->date('sa2_date')->nullable();
            $table->date('rc_date')->nullable();
        });

        // BLOB di MySQL maksimal 64KB, ubah ke MEDIUMBLOB (maksimal 16MB)
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE certifications MODIFY photo MEDIUMBLOB NULL, MODIFY document MEDIUMBLOB NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn([
                'sales_name', 'scope', 'legality', 'pic_name',
                'photo', 'photo_mime', 'document', 'document_name', 'document_mime',
                'sa1_date', 'sa2_date', 'rc_date',
            ]);
        });
    }
};
