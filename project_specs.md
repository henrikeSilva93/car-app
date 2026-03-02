# Car App - Specification Document (SDD)

## Overview

- **Project Name**: Car App
- **Technology Stack**: PHP 8.2, Laravel 12, Livewire/Volt, Tailwind CSS, Vite
- **Database**: MySQL/PostgreSQL (config via `.env`)
- **Document Version**: 1.0
- **Last Updated**: 2026-03-02

---

## Domain Model

### Entities

| Entity | Attributes | Description |
|--------|------------|-------------|
| **User** | `id`, `name`, `email`, `password` | Usuário autenticado do sistema |
| **Car** | `id`, `user_id`, `brand`, `model`, `year`, `plate`, `mileage` | Veículos do usuário |
| **Maintenance** | `id`, `car_id`, `description`, `cost`, `created_at` | Registros de manutenção |
| **Fuelling** | `id`, `car_id`, `user_id`, `liters`, `cost`, `station`, `date`, `mileage` | Registros de abastecimento |
| **Mileage** | `id`, `car_id`, `mileage`, `recorded_at` | Histórico de quilometragem |

### Relationships

```
User (1) ─────< Car (1) ─────< Maintenance
                         └────< Fuelling (1) ─────< Mileage
```

### Business Rules

- `Fuelling.liters` MUST be greater than 0
- `Fuelling.cost` MUST be greater than or equal to 0
- `Car.year` MUST be between 1900 and current year
- `Car.plate` MUST match regex `[A-Z]{3}-\d{4}`

---

## Feature 1: Car Management

### Problem Statement

Usuários precisam gerenciar seus veículos para que possam registrar manutenções e abastecimentos associados a cada carro.

### Usage Context

Acessado via menu "Meus Carros" após autenticação.

### Expected Behavior

- **Main Flow**: Usuário acessa lista → clica em "Adicionar" → preenche dados do veículo → salva → redirecionado para lista
- **Alternative Flows**: 
  - Editar veículo existente
  - Excluir veículo (apenas se não houver abastecimentos/manutenções associadas)
- **Edge Cases**: 
  - Placa duplicada para o mesmo usuário
  - Ano inválido (futuro)
  - Attempto de excluir veículo com histórico

### Acceptance Criteria

1. Given usuário autenticado, When cria carro com brand, model, year, plate, mileage, Then carro é persistido no banco
2. Given carro existente, When edita qualquer campo, Then dados atualizados e refletidos na listagem
3. Given usuário não autenticado, When acessa rota /cars, Then redirecionado para /login
4. Given carro com abastecimentos, When tenta excluir, Then sistema retorna erro de integridade

### Constraints

- SHALL validar `plate` com regex `[A-Z]{3}-\d{4}`
- SHALL validar `year` entre 1900 e ano atual
- SHALL requerer todos os campos obrigatórios
- MUST associar carro ao usuário autenticado (ownership)
- SHALL impedir placa duplicada para o mesmo usuário

### Schema

```
brand: string (required, max:50)
model: string (required, max:50)
year: integer (required, between:1900,current_year)
plate: string (required, regex:/^[A-Z]{3}-\d{4}$/, unique:user)
mileage: integer (required, min:0)
```

### Examples

```json
{
  "brand": "Toyota",
  "model": "Corolla",
  "year": 2023,
  "plate": "ABC-1234",
  "mileage": 15000
}
```

---

## Feature 2: Maintenance Management

### Problem Statement

Usuários precisam registrar e visualizar histórico de manutenções realizadas em seus veículos para controle de custos e planejamento de despesas.

### Usage Context

Acessado via menu "Manutenções" após login autenticado.

### Expected Behavior

- **Main Flow**: Usuário acessa lista → seleciona veículo → preenche formulário (descrição, custo) → salva → mantém na página com feedback
- **Alternative Flows**: 
  - Editar manutenção existente
  - Excluir manutenção com confirmação
- **Edge Cases**: 
  - Manutenção sem custo (gratuito)
  - Veículo sem histórico de manutenções

### Acceptance Criteria

1. Given usuário autenticado, When cria manutenção com description e cost, Then manutenção é persistida e vinculada ao carro
2. Given manutenção existente, When edita custo, Then custo atualizado e refletido no dashboard
3. Given manutenção existente, When exclui, Then registro removido e dashboard atualizado
4. Given usuário não autenticado, When acessa rota de manutenções, Then redirecionado para /login
5. Given custo negativo, When tenta salvar, Then validação retorna erro

### Constraints

- SHALL validar `cost >= 0`
- SHALL requerer `description` obrigatória (min:3, max:500)
- MUST pertencer a um veículo do usuário autenticado
- SHALL ordenar lista por data decrescente
- SHALL exibir veículo associado em cada registro

### Schema

```
car_id: integer (required, exists:cars,id,belongs_to_user)
description: string (required, min:3, max:500)
cost: decimal (required, min:0)
```

### Examples

```json
{
  "car_id": 1,
  "description": "Troca de óleo e filtro",
  "cost": 150.00
}
```

---

## Feature 3: Fuelling Management

### Problem Statement

Usuários precisam registrar abastecimentos para controle de custos de combustível e manter a quilometragem dos veículos atualizada automaticamente.

### Usage Context

Acessado via menu "Abastecimentos" após login autenticado.

### Expected Behavior

- **Main Flow**: Usuário acessa lista → seleciona veículo → preenche formulário (litros, custo, posto, data, quilometragem) → salva → mileage atualizada automaticamente
- **Alternative Flows**: 
  - Editar abastecimento existente (recalcula mileage)
  - Excluir abastecimento (recalcula mileage para última leitura)
- **Edge Cases**: 
  - Abastecimento com quilometragem menor que anterior
  - Primeiro abastecimento do veículo
  - Abastecimento fora de ordem cronológica

### Acceptance Criteria

1. Given usuário autenticado, When cria abastecimento com todos os campos, Then abastecimento persistido e mileage do carro atualizada
2. Given novo abastecimento, When mileage maior que anterior, Then novo registro criado na tabela mileages
3. Given abastecimento existente, When edita mileage, Then mileage do carro atualizada e histórico ajustado
4. Given abastecimento existente, When exclui, Then mileage retorna ao valor anterior
5. Given usuário não autenticado, When acessa rota de abastecimentos, Then redirecionado para /login
6. Given liters <= 0, When tenta salvar, Then validação retorna erro

### Constraints

- SHALL validar `liters > 0`
- SHALL validar `cost >= 0`
- SHALL requerer todos os campos obrigatórios
- MUST pertencer a veículo do usuário autenticado
- SHALL atualizar `Car.mileage` após cada operação (create/update/delete)
- SHALL criar registro em `mileages` table para rastreamento histórico

### Integration: Mileage Update Flow

```
Fuelling Create/Update → 
  1. Validate input 
  2. Save fuelling record
  3. Update Car.mileage
  4. Create Mileage record (car_id, mileage, recorded_at)
```

### Schema

```
car_id: integer (required, exists:cars,id,belongs_to_user)
user_id: integer (required, exists:users,id)
liters: decimal (required, gt:0)
cost: decimal (required, gte:0)
station: string (required, max:100)
date: datetime (required)
mileage: integer (required, min:0)
```

### Examples

```json
{
  "car_id": 1,
  "user_id": 1,
  "liters": 45.5,
  "cost": 250.00,
  "station": "Posto Shell Centro",
  "date": "2026-03-01 14:30:00",
  "mileage": 15250
}
```

---

## Feature 4: Dashboard & Analytics

### Problem Statement

Usuários precisam visualizar estatísticas consolidadas de gastos com veículos para tomada de decisão e controle financeiro.

### Usage Context

Página inicial após login (rota /dashboard).

### Expected Behavior

- **Main Flow**: Usuário autenticado acessa dashboard → sistema calcula estatísticas dos últimos 30 dias → exibe widgets com totais
- **Alternative Flows**: 
  - Atualizar dados manualmente
  - Visualizar breakdown por veículo
- **Edge Cases**: 
  - Primeiro mês de uso (sem dados históricos)
  - Usuário sem veículos cadastrados

### Acceptance Criteria

1. Given usuário autenticado, When acessa dashboard, Then sistema exibe total gasto em abastecimentos (últimos 30 dias)
2. Given usuário autenticado, When acessa dashboard, Then sistema exibe total gasto em manutenções (últimos 30 dias)
3. Given usuário autenticado, When acessa dashboard, Then sistema exibe total litros abastecidos (últimos 30 dias)
4. Given cálculos incorretos, When dados exibidos, Then diferença detectada em testes

### Constraints

- SHALL calcular estatísticas baseadas nos últimos 30 dias a partir da data atual
- SHALL considerar apenas registros do usuário autenticado
- SHALL recalcular automaticamente após create/update/delete em abastecimentos ou manutenções
- SHALL exibir "0" quando não houver dados no período

### Data Points

| Widget | Calculation | Unit |
|--------|-------------|------|
| Total Abastecimentos | SUM(fuellings.cost) WHERE date >= now() - 30 days | BRL |
| Total Manutenções | SUM(maintenances.cost) WHERE created_at >= now() - 30 days | BRL |
| Litros Abastecidos | SUM(fuellings.liters) WHERE date >= now() - 30 days | Liters |

---

## Cross-Cutting Concerns

### Authentication

- Laravel Breeze ou similar para auth
- Todos os endpoints de CRUD requerem autenticação
- Middleware `auth` aplicado a todas as rotas

### Authorization

- Usuário só pode visualizar/editar seus próprios carros
- Usuário só pode criar abastecimentos/manutenções para seus veículos
- Policies para validação de ownership

### Validation Rules (Globais)

| Campo | Regra |
|-------|-------|
| year | between:1900,2026 |
| plate | regex:/^[A-Z]{3}-\d{4}$/ |
| liters | gt:0 |
| cost | gte:0 |
| mileage | integer, min:0 |

---

## Open Decisions

1. **Dashboard por veículo**: O dashboard deve ter visão consolidada global ou por veículo? → Pendente
2. **Quantidade máxima de carros**: Deve haver limite de carros por usuário? → Pendente
3. **Exportação de dados**: O sistema deve permitir exportar dados (PDF/Excel)? → Pendente
4. **Notificações**: O sistema deve notificar sobre manutenções preventivas? → Pendente
5. **Histórico de mileage**: Quantos registros antigos manter? → Pendente

---

## Implementation Notes

### Code Organization

- **Livewire Components**: `app/Livewire/Pages`
- **Views**: `resources/views/pages`
- **Models**: `app/Models`
- **Migrations**: `database/migrations`
- **Seeders**: `database/seeders`

### Acceptance Criteria Format specification uses **Given-

ThisWhen-Then** format:
- **Given**: Contexto/pré-condição
- **When**: Ação/evento
- **Then**: Resultado esperado

### Constraint Keywords

- **SHALL**: Requisito obrigatório
- **MUST**: Requisito obrigatório (forte)
- **SHOULD**: Recomendação
- **MAY**: Funcionalidade opcional

---

## Roadmap

### Week 1
- CRUD Car
- Authentication

### Week 2
- CRUD Maintenance
- Basic validations

### Week 3
- CRUD Fuelling
- Automatic mileage update

### Week 4
- Dashboard statistics
- Automated tests

### Week 5
- Refactor: Inline → Dedicated Livewire classes
- UX improvements (loading states, messages, errors)
