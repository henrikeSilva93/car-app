# Car App — SPECS.md (versão orientada para IA)

## 📌 Visão geral
Aplicação Laravel (PHP ^8.2, Laravel ^12) com Livewire/Volt para gerenciar veículos, manutenções e abastecimentos.  
Objetivo: fornecer CRUDs completos, dashboard com estatísticas e atualização automática de quilometragem.

---

## 🛠️ Stack
- Backend: PHP 8.2, Laravel ^12
- Frontend: Tailwind + Vite
- Componentes: Livewire (flux, volt)
- Banco: MySQL/Postgres (config via `.env`)

---

## 🚗 Modelos e relacionamentos
- **User**: `name`, `email`, `password`
- **Car**: `user_id`, `brand`, `model`, `year`, `plate`, `mileage`
- **Maintenance**: `car_id`, `description`, `cost`
- **Fuelling**: `car_id`, `user_id`, `liters`, `cost`, `station`, `date`

### Regras de negócio
- `liters > 0`
- `cost >= 0`
- `year` entre 1900 e ano atual
- `plate` regex `[A-Z]{3}-\d{4}`

---

## 📖 User Stories

### Carros
- Como usuário, quero cadastrar meus veículos para gerenciar manutenções e abastecimentos.
- Como usuário, quero ver a quilometragem atualizada automaticamente após cada abastecimento.

### Manutenções
- Como usuário, quero registrar manutenções com descrição e custo.
- Como usuário, quero visualizar o histórico de manutenções por veículo.

### Abastecimentos
- Como usuário, quero registrar abastecimentos com litros, custo, posto e data.
- Como usuário, quero editar e excluir abastecimentos.
- Como usuário, quero que a quilometragem do carro seja atualizada ao salvar abastecimento.

### Dashboard
- Como usuário, quero ver estatísticas dos últimos 30 dias:
  - Total gasto em abastecimentos
  - Total gasto em manutenções
  - Litros abastecidos

---

## ✅ Critérios de aceitação

### Fuelling
- Usuário autenticado pode criar, editar e excluir abastecimento com todos os campos obrigatórios.
- Ao salvar abastecimento, quilometragem do carro é atualizada.
- Dashboard reflete gasto total de abastecimentos nos últimos 30 dias.

### Maintenance
- Usuário pode criar/editar/excluir manutenção.
- Custos aparecem no dashboard.
- Validação: custo ≥ 0, descrição obrigatória.

### Dashboard
- Estatísticas calculadas dinamicamente a partir das tabelas.
- Widgets exibem valores corretos com base nos últimos 30 dias.

---

## 🧪 Testes obrigatórios

- **Fuelling**
   - Criar, editar e excluir abastecimento → quilometragem atualizada.
   - Dashboard → soma correta dos custos.
- **Maintenance**
  - Criar manutenção → aparece no histórico.
  - Dashboard → soma correta dos custos.
- **Auth**
  - Usuário não autenticado → redirecionado para login.

---

## 📂 Organização do código
- Livewire components → `app/Http/Livewire/Pages`
- Views → `resources/views/pages`
- Migrations → `database/migrations`
- Seeders → `database/seeders`

---

## 🗺️ Roadmap incremental

1. **Semana 1**  
   - CRUD de `Car`  
   - Autenticação básica  

2. **Semana 2**  
   - CRUD de `Maintenance`  
   - Validações básicas  

3. **Semana 3**  
   - CRUD de `Fuelling`  
   - Atualização automática da quilometragem  

4. **Semana 4**  
   - Dashboard estatístico  
   - Testes automatizados  

5. **Semana 5**  
   - Refatoração Livewire inline → classes dedicadas  
   - Melhorias UX (loading, mensagens, erros)  
