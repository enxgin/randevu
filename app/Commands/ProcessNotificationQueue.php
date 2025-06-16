<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\NotificationTriggerService;
use App\Models\CustomerModel;

class ProcessNotificationQueue extends BaseCommand
{
    protected $group = 'Notifications';
    protected $name = 'notifications:process';
    protected $description = 'Bildirim kuyruğunu işle ve otomatik mesajları gönder';

    protected $usage = 'notifications:process [options]';
    protected $arguments = [];
    protected $options = [
        '--limit' => 'İşlenecek maksimum mesaj sayısı (varsayılan: 50)',
        '--birthday' => 'Doğum günü kutlama mesajlarını da işle'
    ];

    public function run(array $params)
    {
        $triggerService = new NotificationTriggerService();
        $limit = $params['limit'] ?? 50;
        $processBirthdays = isset($params['birthday']);

        CLI::write('Bildirim kuyruğu işleniyor...', 'yellow');
        CLI::newLine();

        try {
            // 1. Doğum günü kutlama mesajlarını planla (eğer istenirse)
            if ($processBirthdays) {
                CLI::write('Doğum günü kutlama mesajları planlanıyor...', 'cyan');
                $triggerService->scheduleBirthdayGreetings();
                CLI::write('✓ Doğum günü mesajları planlandı', 'green');
                CLI::newLine();
            }

            // 2. Kuyruktaki mesajları işle
            CLI::write("Kuyruk işleniyor (maksimum {$limit} mesaj)...", 'cyan');
            $result = $triggerService->processQueue($limit);

            // Sonuçları göster
            CLI::newLine();
            CLI::write('İşlem Sonuçları:', 'yellow');
            CLI::write("├─ İşlenen mesaj sayısı: {$result['processed']}", 'white');
            CLI::write("├─ Başarılı gönderim: {$result['success']}", 'green');
            CLI::write("└─ Başarısız gönderim: {$result['failed']}", 'red');

            if ($result['processed'] > 0) {
                $successRate = round(($result['success'] / $result['processed']) * 100, 2);
                CLI::newLine();
                CLI::write("Başarı oranı: %{$successRate}", $successRate >= 90 ? 'green' : ($successRate >= 70 ? 'yellow' : 'red'));
            }

            CLI::newLine();
            CLI::write('✓ Bildirim kuyruğu işlemi tamamlandı', 'green');

        } catch (\Exception $e) {
            CLI::error('Hata: ' . $e->getMessage());
            CLI::write('Stack trace: ' . $e->getTraceAsString(), 'red');
            return EXIT_ERROR;
        }

        return EXIT_SUCCESS;
    }
}