# Car App — Specs

Resumo técnico rápido do projeto "Car App".

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

## Modelos (existentes)
- `User` — campos: `name`, `email`, `password`, casts e factories.
- `Car` — `user_id`, `brand`, `model`, `year`, `plate`, `mileage`.
- `Maintenance` — `car_id`, `description`, `cost`.

Observação: o modelo `Fuelling` foi adicionado em `app/Models/Fuelling.php` e a migration em `database/migrations/2026_02_25_000000_create_fuellings_table.php`.

## Views / UI
- Páginas em `resources/views/pages/*` (ex.: `resources/views/pages/maintenance/⚡index.blade.php`, `resources/views/pages/fuelling/index.blade.php`).
- Componentes reutilizáveis em `resources/views/components` (ex.: `info-widget`, `title`, `navbar`).
- As páginas usam Livewire page components definidos inline (ex.: `new class extends Component` dentro dos arquivos Blade).

## Banco de dados / Migrations
- Existem migrations para `cars` e `maintenances` em `database/migrations/` (ex.: `2026_01_21_042623_cars.php`, `2026_02_14_235829_create_maintenances_table.php`).
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
- Páginas de funcionalidade (manutenção, abastecimento) são implementadas como Livewire page components; algumas páginas contém lógica PHP embutida no Blade (classe anônima `new class extends Component`).
- A funcionalidade de `Fuelling` atualmente só tem interface HTML — sem modelo nem lógica persistente.
- Dashboard já contém estatísticas (ex.: `total_fuel_spent`) exibidas via componentes `info-widget`.

## Recomendações / Próximos passos
- Criar modelo `Fuelling` + migration (`liters`, `cost`, `station`, `car_id`, `user_id`, `date`) e relacionamentos Eloquent.
- Implementar Livewire page/component para CRUD de abastecimentos (seguindo o padrão de `Maintenance`).
- Adicionar validações, testes unitários/feature para `Maintenance` e `Fuelling`.
- Opcional: padronizar a forma de declaração dos componentes (mover lógica para classes Livewire separadas em `app/Http/Livewire/Pages`).

---

Arquivos úteis:
- `routes/web.php`
- `app/Models/Car.php`
- `app/Models/Maintenance.php`
- `resources/views/pages/maintenance/⚡index.blade.php`
- `resources/views/pages/fuelling/index.blade.php`

Gerado automaticamente em: `SPECS.md`


## 3. criando o crud de abastecimento
- [x] criar um modal e inserir os campos seguindo o fillable da model, o modal deve ser um flux component (seguir exemplo da pagina de manutenções)

Implementação realizada:
- Adicionado modal Flux de criação/edição em `resources/views/pages/fuelling/index.blade.php` (`add-fuelling`) com os campos: `selected_car`, `liters`, `cost`, `station`, `date`.
- Implementada lógica Livewire mínima inline no mesmo arquivo: carregamento de `userCars`, listagem de `fuellings`, métodos `createFuelling`, `editFuelling`, `updateFuelling`, `confirmDeleteFuelling`, `deleteFuelling` e `resetForm`.
- Criado o model `Fuelling` (`app/Models/Fuelling.php`) e a migration `create_fuellings_table`.

 - [x] Todo abastecimento recebe um campo de quilometragem e ao criar/editar abastecimento, a quilometragem do veículo é atualizada automaticamente.

Implementação:
- Campo "Quilometragem" adicionado ao modal de abastecimento.
- Ao criar ou editar abastecimento, o campo `mileage` do veículo é atualizado conforme o valor informado.

