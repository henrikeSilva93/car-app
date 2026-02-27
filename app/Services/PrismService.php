<?php
namespace App\Services;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Tool;
use App\Models\Car;
use App\Models\Maintenance;

class PrismService
{
    
    public function generateResponse($prompt)
    {   

        $context  = [
            'usuario' => auth()->user()->name,
            'veículos' => Car::all()->where('user_id', auth()->user()->id)->select(['brand', 'model', 'year', 'mileage', 'id'])->toArray(),
            
        ];
        
        $prompt = "VocÊ é uma IA especializada em assistente de veículos, responda as perguntas relacionadas a veículos, 
                seja breve e direto ao ponto.
                verifique o contexto de veículos para obter informações sobre os veículos cadastrados, os dados dos veículos é um array que está dentro da chave veículos, lá tem as propriedas brand, model, year, mileage e id. 
                 - se o usuario fizer uma saudação como olá, responda com outra saudação e com o nome do usuario dado pelo contexto. ( exemplo: olá, {nome do usuario}, como posso ajudar você hoje? )
                 - se o usuario perguntar sobre os veículos disponiveis, responda com a lista de veículos disponiveis no contexto, destacando a marca, modelo e ano de cada veículo usando markdowns.
                 -mostre a lista de veículos apenas se o usuário pedir, caso contrário, não mencione os veículos.
                 - Quando o usuário perguntar sobre manutençoes, você DEVE chamar a ferramenta 'manutencao' passando {id_veiculo} que foi passado pelo contexto como argumento. 
                 - Não invente dados, use apenas o resultado da ferramenta para responder.
                 dentro do campo text deve ser usado markdowns para destacar as partes importantes da resposta.
                - se o usuario de uma saudação como olá, responda com outra saudação e com o nome do usuario dado pelo contexto. ( exemplo: olá, {nome do usuario}, como posso ajudar você hoje? )
                - se o usuario perguntar sobre os veículos disponiveis, responda com a lista de veículos disponiveis no contexto, destacando a marca, modelo e ano de cada veículo usando markdowns.
                - se o usuario pedir para criar uma nova manutenção, chame a ferramenta 'criar_manutencao' passando o id do veículo e a descrição da manutenção como argumentos.
                -mostre a lista de veículos apenas se o usuário pedir, caso contrário, não mencione os veículos.
                - Quando o usuário perguntar sobre manutençoes, você DEVE chamar a ferramenta 'manutencao' passando {id_veiculo} que foi passado pelo contexto como argumento. 
                - Não invente dados, use apenas o resultado da ferramenta para responder.
                dentro do campo text deve ser usado markdowns para destacar as partes importantes da resposta.
                pergunta:
            " .$prompt."VocÊ usuará as seguintes informações de contexto para responder a pergunta: ".json_encode($context);

               $maitenanceTool = Tool::as('manutencao')
                        ->for('obtenha as manutenções do veículo')
                        ->withStringParameter('id_veiculo', 'o id do veículo para obter as manutenções relacionadas a ele')
                        ->using(function (int $id_veiculo) {
            
                            $manutenções = Maintenance::where('car_id', $id_veiculo)->get()->toArray();
                              return "Aqui estão as manutenções registradas: " . json_encode($manutenções);
                        })
                        ->withErrorHandling();

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

         

        $response = Prism::text()
            ->using(Provider::Ollama, 'ministral-3:3b')
            ->withPrompt($prompt)
            ->withClientOptions(['timeout' => 200])
            ->withTools([$maitenanceTool, $createMaitenance])
            ->withMaxSteps(2)
            ->generate();
            

        return $response->text;
    }

    // public function MaitenancesTool() {
    //     $maitenanceTool = Tool::as('manutenções')
    //                     ->for('obtenha as manutenções do veículo')
    //                     ->withStringParameter('id_veiculo', 'o id do veículo para obter as manutenções relacionadas a ele')
    //                     ->using(function ($id_veiculo) {
    //                         $manutenções = Maintenance::where('car_id', $id_veiculo)->get();
    //                         return $manutenções->toArray();
    //                     });
    //     return $maitenanceTool;
    // }
}