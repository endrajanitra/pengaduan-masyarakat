<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    protected $signature   = 'email:test {email : Alamat email tujuan}';
    protected $description = 'Kirim email test untuk memastikan konfigurasi SMTP Gmail benar';

    public function handle(): int
    {
        $target = $this->argument('email');

        $this->newLine();
        $this->line('  Konfigurasi aktif:');
        $this->line('  MAIL_MAILER   : ' . config('mail.mailer'));
        $this->line('  MAIL_HOST     : ' . config('mail.mailers.smtp.host', '-'));
        $this->line('  MAIL_PORT     : ' . config('mail.mailers.smtp.port', '-'));
        $this->line('  MAIL_USERNAME : ' . config('mail.mailers.smtp.username', '-'));
        $this->line('  MAIL_FROM     : ' . config('mail.from.address'));
        $this->newLine();
        $this->info("Mencoba kirim email ke: {$target} ...");

        try {
            Mail::raw(
                "Halo!\n\nIni adalah email test dari Sistem Pengaduan Desa Wangisagara.\n\nJika kamu menerima ini, konfigurasi Gmail SMTP sudah benar dan siap digunakan!\n\nSalam,\nSistem Pengaduan Desa Wangisagara",
                function ($message) use ($target) {
                    $message->to($target)
                        ->subject('[Test] Konfigurasi Email Berhasil - Pengaduan Desa Wangisagara');
                }
            );

            $this->components->success("Email berhasil dikirim ke {$target}!");
            $this->line('  Cek inbox (dan folder Spam) di email tujuan.');
            $this->newLine();

            return self::SUCCESS;

        } catch (\Symfony\Component\Mailer\Exception\TransportException $e) {
            $this->components->error('Koneksi ke server Gmail gagal!');
            $this->newLine();
            $this->line('  Error  : ' . $e->getMessage());
            $this->newLine();
            $this->line('  Kemungkinan penyebab:');
            $this->line('  1. MAIL_PASSWORD bukan App Password — pastikan bukan password Gmail biasa');
            $this->line('  2. App Password belum dibuat atau sudah dihapus dari akun Google');
            $this->line('  3. 2-Step Verification belum diaktifkan di akun Google');
            $this->line('  4. Koneksi internet / firewall localhost memblokir port 587');
            $this->newLine();
            $this->line('  Solusi: jalankan  php artisan config:clear  lalu coba lagi.');

            return self::FAILURE;

        } catch (\Exception $e) {
            $this->components->error('Gagal kirim email!');
            $this->line('  Error: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}