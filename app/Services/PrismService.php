<?php
namespace App\Services;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Tool;
use App\Models\Car;
use App\Models\Maintenance;
use App\Models\Fuelling;

class PrismService
{
    public $model = 'ministral-3:3b';
    
    public function generateResponse($prompt)
    {   

        $context = [
        'usuario' => auth()->user()->name,
        'veículos' => Car::where('user_id', auth()->id())
            ->select(['brand', 'model', 'year', 'mileage', 'id'])
            ->get()
            ->toArray(),
        'mensagens anteriores' => \App\Models\ChatbotMessageHistory::where('user_id', auth()->id())
            ->limit(30)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray()
    ];

        $prompt = "
            Você é uma IA especializada em assistência de veículos. 
            Responda de forma breve e direta ao ponto, seguindo estas regras:

            1. **Saudações**: Se o usuário disser 'olá', responda com outra saudação incluindo o nome do usuário.  
            Exemplo: 'Olá, {nome}, como posso ajudar você hoje?'
            se já houver uma iteração anterior, não precisa mandar saudações, vá direto ao ponto.

            2. **Lista de veículos**: Mostre a lista apenas se o usuário pedir.  
            Use markdown para destacar **marca, modelo e ano**.  
            Nunca mostre o ID do veículo, a menos que o usuário peça explicitamente.

            3. **Manutenções**:  
            - Se o usuário perguntar sobre manutenções, chame a ferramenta `manutencao` passando `{id_veiculo}`.  
            - Se o usuário pedir para criar uma nova manutenção, chame `criar_manutencao` passando `{id_veiculo}` e a descrição.  
            - Use apenas os dados retornados pela ferramenta, não invente informações.
            4. **Abastecimentos**:  
            - Se o usuário perguntar sobre abastecimentos, chame a ferramenta `ver_abastecimentos` passando `{id_veiculo}`.
            - Se o usuário pedir para criar um novo abastecimento, chame `abastecimento` passando `{id_veiculo}`, litros, custo e posto.

            4. **Formatação**:  
            - Use markdown para destacar informações importantes.  
            - Seja sempre objetivo e claro.

        Contexto disponível: " . json_encode($context);

             

        $response = Prism::text()
            ->using(Provider::Ollama, $this->model)
            ->withPrompt($prompt)
            ->withClientOptions(['timeout' => 200])
            ->withTools([$this->MaitenancesTool(), $this->createMaitenanceTool(), $this->ViewFuellingsTool(), $this->CreateFuellingTool()])
            ->withMaxSteps(2)
            ->generate();
            
        $this->RegisterMessage($response->text);
        return $response->text;
    }

    public function MaitenancesTool() {
        $maitenanceTool = Tool::as('manutencao')
                        ->for('obtenha as manutenções do veículo')
                        ->withStringParameter('id_veiculo', 'o id do veículo para obter as manutenções relacionadas a ele')
                        ->using(function (int $id_veiculo) {
            
                            $manutenções = Maintenance::where('car_id', $id_veiculo)->get()->toArray();
                              return "Aqui estão as manutenções registradas: " . json_encode($manutenções);
                        })
                        ->withErrorHandling();
        return $maitenanceTool;
    }

    public function createMaitenanceTool() {
        $createMaitenance = Tool::as('criar_manutencao')
                        ->for('crie uma nova manutenção para um veículo')
                        ->withStringParameter('id_veiculo', 'o id do veículo para criar a manutenção relacionada a ele')
                        ->withStringParameter('descricao', 'a descrição da manutenção a ser criada')
                        ->withStringParameter('custo', 'o custo da manutenção a ser criada')
                        ->using(function (int $id_veiculo, string $descricao, float $custo) {
                          
                          try {
                                $manutencao = Maintenance::create([
                                    'car_id' => $id_veiculo,
                                    'description' => $descricao,
                                    'cost' => $custo,
                                ]);
                            
                                return "Manutenção criada com sucesso: ";
                            } catch (\Exception $e) {
                            return "Erro ao criar manutenção: ";
                            }
                        })
                        ->withErrorHandling();
        return $createMaitenance;
    }

    public function ViewFuellingsTool() {
        $viewFuelling = Tool::as('ver_abastecimentos')
                        ->for('obtenha os abastecimentos do veículo')
                        ->withStringParameter('id_veiculo', 'o id do veículo para obter os abastecimentos relacionados a ele')
                        ->using(function (int $id_veiculo) {
            
                            $abastecimentos = Fuelling::where('car_id', $id_veiculo)->get()->toArray();
                              return "Aqui estão os abastecimentos registrados: " . json_encode($abastecimentos);
                        })
                        ->withErrorHandling();
        return $viewFuelling;
    }

    public function CreateFuellingTool() {
        $createFuelling = Tool::as('abastecimento')
                        ->for('registre um novo abastecimento para um veículo')
                        ->withStringParameter('id_veiculo', 'o id do veículo para criar o abastecimento relacionado a ele')
                        ->withStringParameter('litros', 'a quantidade de litros abastecida')
                        ->withStringParameter('custo', 'o custo do abastecimento')
                        ->withStringParameter('posto', 'o nome do posto onde foi realizado o abastecimento')
                        ->using(function (int $id_veiculo, float $litros, float $custo, string $posto) {
                          
                          try {
                                $abastecimento = Fuelling::create([
                                    'car_id' => $id_veiculo,
                                    'user_id' => auth()->id(),
                                    'liters' => $litros,
                                    'cost' => $custo,
                                    'station' => $posto
                                ]);
                            
                                return "Abastecimento registrado com sucesso: ";
                            } catch (\Exception $e) {
                            return "Erro ao registrar abastecimento: ";
                            }
                        })
                        ->withErrorHandling();
        return $createFuelling;
    }

    public function RegisterMessage($message) {
        try{
              \App\Models\ChatbotMessageHistory::create([
                    'user_id' => auth()->id(),
                    'message' => $message,
                    'sender' =>  'bot'
        ]);
        } catch(\Exception $e) {
            // Log the error or handle it as needed
             \Log::error('Error saving chatbot message: ' . $e->getMessage());
        }
    }
}