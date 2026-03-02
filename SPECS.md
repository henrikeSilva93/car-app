# Car App — Specs

Resumo técnico atualizado do projeto "Car App".

## Visão geral
- Aplicação Laravel (requisição: PHP ^8.2, Laravel ^12) com páginas implementadas como componentes Livewire/Volt.
- Objetivo: gerenciar veículos, manutenções e abastecimentos com dashboard e estatísticas.

## Stack
- PHP 8.2
- Laravel ^12
- Livewire (livewire/flux, livewire/volt)
- Frontend: Tailwind + Vite

## Rotas principais
- `/` -> página do dashboard (Livewire page)
- `/cars` -> gerenciamento de veículos
- `/maintenance` -> gerenciamento de manutenções
- `/fuelling` -> página de abastecimentos

Ver: `routes/web.php`.

## Modelos
- `User`: Autenticação, campos: `name`, `email`, `password`.
- `Car`: Veículo, campos: `user_id`, `brand`, `model`, `year`, `plate`, `mileage`.
- `Maintenance`: Manutenção, campos: `car_id`, `description`, `cost`.
- `Fuelling`: Abastecimento, campos: `car_id`, `user_id`, `liters`, `cost`, `station`, `date`.
- `Mileage`: Histórico de quilometragem, campos: `car_id`, `mileage`.
- `ChatbotMessageHistory`: Histórico de mensagens do chatbot, campos: `user_id`, `message`, `sender`, `sent_at`.

O modelo `Fuelling` foi adicionado em `app/Models/Fuelling.php` e a migration em `database/migrations/2026_02_25_000000_create_fuellings_table.php`.

## Views / UI
- Páginas em `resources/views/pages/*`:
	- `dashboard/⚡index.blade.php`: Dashboard com widgets de estatísticas.
	- `maintenance/⚡index.blade.php`: CRUD de manutenções.
	- `fuelling/index.blade.php`: CRUD de abastecimentos (com modal Flux, atualização de quilometragem).
- Componentes reutilizáveis em `resources/views/components` (ex.: `info-widget`, `title`, `navbar`).
- As páginas usam Livewire page components definidos inline (ex.: `new class extends Component` dentro dos arquivos Blade).

## Banco de dados / Migrations
- Migrations para `cars`, `maintenances`, `fuellings`, `mileages` em `database/migrations/`.
- Seeders: `database/seeders/UserSeeder.php`, `DatabaseSeeder.php`.

## Como rodar localmente
1. Instalar dependências PHP:

```bash
composer install
```

2. Copiar `.env.example` para `.env` e ajustar configurações (DB).
3. Gerar chave:

```bash
php artisan key:generate
```

4. Rodar migrations e seeders:

```bash
php artisan migrate --seed
```

5. Instalar dependências JS e rodar dev server:

```bash
npm install
npm run dev
```

6. Iniciar servidor Laravel (se não estiver usando `npm run dev` com scripts):

```bash
php artisan serve
```

## Testes
- Executar testes com:

```bash
php artisan test
```

## Pontos importantes / Observações técnicas
- CRUD completo de manutenções e abastecimentos, ambos com modais Flux.
- Ao criar/editar abastecimento, a quilometragem do veículo é atualizada automaticamente.
- **Histórico de quilometragem**: Tabela `mileages` registra o histórico de quilometragem de cada veículo, permitindo rastreamento ao longo do tempo.
- Dashboard exibe estatísticas dinâmicas:
	- Gasto total, abastecimento total, manutenção total (últimos 30 dias).
	- Valor de abastecimento é calculado dinamicamente da tabela `fuellings`.
- Páginas implementadas como Livewire page components inline.
- **ChatBot com IA**: Botão flutuante no canto inferior direito que permite interação por voz/texto com IA.
	- Usa Ollama (`ministral-3b`) via pacote Prism para gerar respostas.
	- Ferramentas disponíveis: `manutencao`, `criar_manutencao`, `ver_abastecimentos`, `abastecimento`.
	- Histórico de mensagens persistido em `ChatbotMessageHistory`.
	- Suporta formatação markdown nas respostas.
	- Serviço em `app/Services/PrismService.php`, componente em `resources/views/components/⚡chat-bot.blade.php`.

## Recomendações / Próximos passos
- Adicionar validações dos campos nos modais de manutenção e abastecimento.
- Implementar testes unitários/feature para `Maintenance` e `Fuelling`.
- Opcional: padronizar a forma de declaração dos componentes (mover lógica para classes Livewire separadas em `app/Http/Livewire/Pages`).
- Melhorar UX dos modais (mensagens, loading, erros).
- Melhorar o chatbot: adicionar suporte a seleção de veículo, feedback visual de ferramentas executadas.

---

Arquivos úteis:
- `routes/web.php`
- `app/Models/Car.php`
- `app/Models/Maintenance.php`
- `app/Services/PrismService.php`
- `resources/views/components/⚡chat-bot.blade.php`
- `resources/views/pages/maintenance/⚡index.blade.php`
- `resources/views/pages/fuelling/index.blade.php`

Gerado automaticamente em: `SPECS.md`


## CRUD de abastecimento
- Modal Flux de criação/edição/exclusão em `fuelling/index.blade.php` com campos: `selected_car`, `liters`, `cost`, `station`, `date`, `mileage`.
- Lógica Livewire inline: métodos para criar, editar, atualizar, deletar abastecimento e atualizar quilometragem do veículo.
- Botões de editar e excluir na tabela de abastecimentos, com modais dedicados.
- Valor de abastecimento no dashboard é calculado dinamicamente da tabela `fuellings`.

