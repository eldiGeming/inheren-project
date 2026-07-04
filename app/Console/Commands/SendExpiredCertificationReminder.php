<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certification;
use App\Services\WhatsappService;

class SendExpiredCertificationReminder extends Command
{
    protected $signature = 'reminder:certification';
    protected $description = 'Kirim reminder sertifikat H-7 ke WhatsApp';

    public function handle()
    {
        $targetDate = now()->addDays(7)->toDateString();

        $certifications = Certification::with('company:id_company,name_company')
            ->whereDate('expired_date', $targetDate)
            ->where('is_deleted', 0)
            ->whereNull('reminder_sent_at')
            ->get();

        if ($certifications->isEmpty()) {
            $this->info('Tidak ada sertifikat.');
            return;
        }

        // Format pesan
        $message = "📢 Reminder Sertifikat Expired (H-7)\n\n";

        foreach ($certifications as $cert) {
            $message .= "• {$cert->company->name_company}\n";
            $message .= "  No: {$cert->number_certificate}\n";
            $message .= "  Exp: {$cert->expired_date}\n\n";
        }

        $adminPhone = env('NO_WHATSAPP');

        $wa = new WhatsappService();
        $response = $wa->send($adminPhone, $message);

        // update status
        Certification::whereIn(
            'id_certification',
            $certifications->pluck('id_certification')
        )->update([
            'reminder_sent_at' => now()
        ]);

        $this->info('Reminder berhasil dikirim!');
    }
}