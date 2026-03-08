<?php

namespace App\Console\Commands;

use App\Services\PrismService;
use Illuminate\Console\Command;

class TestarPrism extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:testar-prism';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'executa um teste do PrismService para gerar uma resposta com base em um prompt específico.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new PrismService;
        $ask = $this->ask('Digite a descrição do veículo para obter uma resposta da IA:');

        $response = $service->generateResponse($ask);

        $this->info('Resposta: '.$response);
    }
}
