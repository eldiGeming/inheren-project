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
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id_client'); // INT AUTO INCREMENT

            $table->string('client_name', 255);
            $table->string('sales_name', 255)->nullable();
            $table->text('scope')->nullable();
            $table->string('legality', 255)->nullable();
            $table->string('pic_name', 255)->nullable();
            $table->string('iso_standard', 255)->nullable();

            // Foto & dokumen disimpan sebagai BLOB di database
            $table->binary('photo')->nullable();
            $table->string('photo_mime', 100)->nullable();
            $table->binary('document')->nullable();
            $table->string('document_name', 255)->nullable();
            $table->string('document_mime', 100)->nullable();

            // Jadwal audit
            $table->date('ia_date')->nullable();
            $table->date('sa1_date')->nullable();
            $table->date('sa2_date')->nullable();
            $table->date('rc_date')->nullable();

            $table->boolean('is_deleted')->default(false);
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps(); // otomatis created_at & updated_at
        });

        // BLOB di MySQL maksimal 64KB, ubah ke MEDIUMBLOB (maksimal 16MB)
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'])) {
            DB::statement('ALTER TABLE clients MODIFY photo MEDIUMBLOB NULL, MODIFY document MEDIUMBLOB NULL');
        }

        // Data ini sempat disimpan di tabel certifications (migration 2026_10_25 yang sudah dihapus).
        // Jika migration itu sudah terlanjur dijalankan, pindahkan datanya ke clients lalu hapus kolomnya.
        if (Schema::hasColumn('certifications', 'sales_name')) {
            $columns = [
                'sales_name', 'scope', 'legality', 'pic_name',
                'photo', 'photo_mime', 'document', 'document_name', 'document_mime',
                'sa1_date', 'sa2_date', 'rc_date',
            ];

            DB::table('clients')->insertUsing(
                array_merge(['client_name', 'iso_standard', 'ia_date'], $columns, ['created_at', 'updated_at']),
                DB::table('certifications as c')
                    ->join('companies as co', 'co.id_company', '=', 'c.id_company')
                    ->join('type_certificates as t', 't.id_type_certificate', '=', 'c.id_type_certificate')
                    ->where('c.is_deleted', 0)
                    ->where(function ($query) use ($columns) {
                        foreach ($columns as $column) {
                            $query->orWhereNotNull('c.'.$column);
                        }
                    })
                    ->select(array_merge(
                        ['co.name_company', 't.type_certificate', 'c.issues_date'],
                        array_map(fn ($column) => 'c.'.$column, $columns),
                        ['c.created_at', 'c.updated_at']
                    ))
            );

            Schema::table('certifications', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
