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
        // Catatan bebas di Certification & Data Client
        Schema::table('certifications', function (Blueprint $table) {
            $table->text('note')->nullable()->after('expired_date');
        });
        Schema::table('clients', function (Blueprint $table) {
            $table->text('note')->nullable()->after('rc_date');
        });

        // Legalitas berubah dari teks menjadi upload file (client_attachments, type legality).
        // Teks lama dipindah ke Note agar tidak hilang.
        DB::table('clients')->whereNotNull('legality')->where('legality', '<>', '')
            ->get(['id_client', 'legality'])
            ->each(fn ($client) => DB::table('clients')->where('id_client', $client->id_client)
                ->update(['note' => 'Legalitas: '.$client->legality]));

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('legality');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('legality', 255)->nullable()->after('cb');
        });

        // Kembalikan teks Legalitas yang dipindah ke Note
        DB::table('clients')->where('note', 'like', 'Legalitas: %')
            ->get(['id_client', 'note'])
            ->each(fn ($client) => DB::table('clients')->where('id_client', $client->id_client)
                ->update(['legality' => mb_substr($client->note, mb_strlen('Legalitas: '), 255)]));

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('note');
        });
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
