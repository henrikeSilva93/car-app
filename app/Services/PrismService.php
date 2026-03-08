<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Car;
use App\Models\ChatbotMessageHistory;
use App\Models\Fuelling;
use App\Models\Maintenance;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Facades\Tool;

class PrismService
{
    public string $model = 'minimax-m2.5:cloud';

    public function generateResponse(string $userQuestion): string
    {
        $context = [
            'usuario' => auth()->user()->name,
            'veiculos' => Car::where('user_id', auth()->id())
                ->select(['brand', 'model', 'year', 'id'])
                ->get()
                ->toArray(),
            'mensagens anteriores' => ChatbotMessageHistory::where('user_id', auth()->id())
                ->limit(30)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray(),
        ];

        $prompt = "
            Voce e uma IA especializada em assistencia de veiculos.
            Responda de forma breve e direta ao ponto, seguindo estas regras:

            1. **Saudacoes**: Se o usuario disser 'ola', responda com outra saudacao incluindo o nome do usuario.
            Exemplo: 'Ola, {nome}, como posso ajudar voce hoje?'
            se ja houver uma iteracao anterior, nao precisa mandar saudacoes, va direto ao ponto.

            2. **Lista de veiculos**: Mostre a lista apenas se o usuario pedir.
            Use markdown para destacar **marca, modelo e ano**.
            Nunca mostre o ID do veiculo, a menos que o usuario pida explicitamente.

            3. **Manutencoes**:
            - Se o usuario perguntar sobre manutencoes, chame a ferramenta `manutencao` passando `{id_veiculo}`.
            - Se o usuario pedir para criar uma nova manutencao, chame `criar_manutencao` passando `{id_veiculo}` e a descricao.
            - Use apenas os dados retornados pela ferramenta, nao invente informacoes.

            4. **Abastecimentos**:
            - Se o usuario perguntar sobre abastecimentos, chame a ferramenta `ver_abastecimentos` passando `{id_veiculo}`.
            - Se o usuario pedir para criar um novo abastecimento, chame `abastecimento` passando `{id_veiculo}`, litros, custo e posto.

            4. **Formatacao**:
            - Use markdown para destacar informacoes importantes.
            - Seja sempre objetivo e claro.

        Contexto disponivel: ".json_encode($context).'dadas as regras acima, responda a seguinte pergunta: '.$userQuestion;

        $response = Prism::text()
            ->using(Provider::Ollama, $this->model)
            ->withPrompt($prompt)
            ->withClientOptions(['timeout' => 200])
            ->withTools([
                $this->maintenancesTool(),
                $this->createMaintenanceTool(),
                $this->viewFuellingsTool(),
                $this->createFuellingTool(),
            ])
            ->withMaxSteps(2)
            ->generate();

        $this->registerMessage($response->text);

        return $response->text;
    }

    public function maintenancesTool()
    {
        $maintenanceTool = Tool::as('manutencao')
            ->for('obtenha as manutencoes do veiculo')
            ->withStringParameter('id_veiculo', 'o id do veiculo para obter as manutencoes relacionadas a ele')
            ->using(function (int $idVeiculo): string {
                $car = Car::where('id', $idVeiculo)
                    ->where('user_id', auth()->id())
                    ->first();

                if (! $car) {
                    return 'Erro: Veiculo nao encontrado ou nao pertence ao usuario';
                }

                $maintenances = Maintenance::where('car_id', $idVeiculo)->get()->toArray();

                return 'Aqui estao as manutencoes registradas: '.json_encode($maintenances);
            })
            ->withErrorHandling();

        return $maintenanceTool;
    }

    public function createMaintenanceTool()
    {
        $createMaintenance = Tool::as('criar_manutencao')
            ->for('crie uma nova manutencao para um veiculo')
            ->withStringParameter('id_veiculo', 'o id do veiculo para criar a manutencao relacionada a ele')
            ->withStringParameter('descricao', 'a descricao da manutencao a ser criada')
            ->withStringParameter('custo', 'o custo da manutencao a ser criada')
            ->using(function (int $idVeiculo, string $descricao, float $custo): string {
                $car = Car::where('id', $idVeiculo)
                    ->where('user_id', auth()->id())
                    ->first();

                if (! $car) {
                    return 'Erro: Veiculo nao encontrado ou nao pertence ao usuario';
                }

                try {
                    Maintenance::create([
                        'car_id' => $idVeiculo,
                        'description' => $descricao,
                        'cost' => $custo,
                    ]);

                    return 'Manutencao criada com sucesso: ';
                } catch (\Exception $e) {
                    return 'Erro ao criar manutencao: '.$e->getMessage();
                }
            })
            ->withErrorHandling();

        return $createMaintenance;
    }

    public function viewFuellingsTool()
    {
        $viewFuelling = Tool::as('ver_abastecimentos')
            ->for('obtenha os abastecimentos do veiculo')
            ->withStringParameter('id_veiculo', 'o id do veiculo para obter os abastecimentos relacionados a ele')
            ->using(function (int $idVeiculo): string {
                $car = Car::where('id', $idVeiculo)
                    ->where('user_id', auth()->id())
                    ->first();

                if (! $car) {
                    return 'Erro: Veiculo nao encontrado ou nao pertence ao usuario';
                }

                $fuellings = Fuelling::join('mileages', 'fuellings.id', '=', 'mileages.fuelling_id')
                    ->where('fuellings.car_id', $idVeiculo)
                    ->select('fuellings.*', 'mileages.mileage')
                    ->get()
                    ->toArray();

                return 'Aqui estao os abastecimentos registrados: '.json_encode($fuellings);
            })
            ->withErrorHandling();

        return $viewFuelling;
    }

    public function createFuellingTool()
    {
        $createFuelling = Tool::as('abastecimento')
            ->for('registre um novo abastecimento para um veiculo')
            ->withStringParameter('id_veiculo', 'o id do veiculo para criar o abastecimento relacionado a ele')
            ->withStringParameter('litros', 'a quantidade de litros abastecida')
            ->withStringParameter('custo', 'o custo do abastecimento')
            ->withStringParameter('posto', 'o nome do posto onde foi realizado o abastecimento')
            ->using(function (int $idVeiculo, float $litros, float $custo, string $posto): string {
                $car = Car::where('id', $idVeiculo)
                    ->where('user_id', auth()->id())
                    ->first();

                if (! $car) {
                    return 'Erro: Veiculo nao encontrado ou nao pertence ao usuario';
                }

                try {
                    Fuelling::create([
                        'car_id' => $idVeiculo,
                        'user_id' => auth()->id(),
                        'liters' => $litros,
                        'cost' => $custo,
                        'station' => $posto,
                    ]);

                    return 'Abastecimento registrado com sucesso: ';
                } catch (\Exception $e) {
                    return 'Erro ao registrar abastecimento: '.$e->getMessage();
                }
            })
            ->withErrorHandling();

        return $createFuelling;
    }

    public function registerMessage(string $message): void
    {
        try {
            ChatbotMessageHistory::create([
                'user_id' => auth()->id(),
                'message' => $message,
                'sender' => 'bot',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving chatbot message: '.$e->getMessage());
        }
    }
}
