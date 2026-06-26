<?php

namespace App\Console\Commands;

use App\Services\OrangeSmsService;
use Illuminate\Console\Command;

class TestOrangeSms extends Command
{
    protected $signature = 'orange:test-sms {phone?} {--message=Test SmartSMS} {--balance}';
    protected $description = 'Envoie un SMS de test ou vérifie le solde Orange';

    public function handle(OrangeSmsService $sms): int
    {
        if ($this->option('balance')) {
            return $this->checkBalance($sms);
        }

        $phone = $this->argument('phone');
        if (!$phone) {
            $this->error('Fournissez un numéro ou utilisez --balance pour voir le solde.');
            return self::FAILURE;
        }

        $message = $this->option('message');
        $this->info("Envoi vers : {$phone}");
        $this->info("Message    : {$message}");

        try {
            $response = $sms->send($phone, $message);
            $this->info('SMS envoyé avec succès !');
            $this->line(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Erreur : ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function checkBalance(OrangeSmsService $sms): int
    {
        try {
            $contracts = $sms->getContracts();
            if (empty($contracts)) {
                $this->warn('Aucun contrat trouvé. Achetez un bundle sur developer.orange.com.');
                return self::FAILURE;
            }
            foreach ($contracts as $contract) {
                $this->info("Pays      : " . ($contract['country'] ?? '?'));
                $this->info("Statut    : " . ($contract['status'] ?? '?'));
                $this->info("Solde SMS : " . ($contract['availableUnits'] ?? 0));
                $this->info("Expiration: " . ($contract['expirationDate'] ?? '?'));
                $this->line('--- JSON brut du contrat (pour voir tous les champs disponibles) ---');
                $this->line(json_encode($contract, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $this->line('---');
            }
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Erreur : ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
