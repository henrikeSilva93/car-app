# Security Audit Checklist

## Estado: EM ANDAMENTO
**Data da auditoria:** 2026-03-06
**Última atualização:** 2026-03-06 14:30:00

---

## 🔴 Crítico

| # | Vulnerabilidade | Arquivo | Linha | Status |
|---|----------------|---------|-------|--------|
| 1 | User ID hardcoded (sempre 1) | resources/views/pages/fuelling/index.blade.php | 45 | [x] |
| 2 | API Key exposta no repositório | .env | 68 | [ ] |
| 3 | IDOR - Ferramentas do chatbot não verificam propriedade | app/Services/PrismService.php | 75-151 | [x] |
| 4 | IDOR - API de gráficos retorna dados de todos os usuários | app/Services/StatisticService.php | 10-31 | [x] |

---

## 🟠 Alto

| # | Vulnerabilidade | Arquivo | Linha | Status |
|---|----------------|---------|-------|--------|
| 5 | APP_DEBUG=true expõe stack traces | .env | 4 | [x] |
| 6 | SESSION_ENCRYPT=false | .env | 32 | [x] |
| 7 | Broken Access Control - editCar/deleteCar sem verificação | resources/views/pages/car/⚡index.blade.php | 49-112 | [x] |
| 8 | Broken Access Control - editMaintenance/deleteMaintenance | resources/views/pages/maintenance/⚡index.blade.php | 50-98 | [x] |

---

## 🟡 Médio

| # | Vulnerabilidade | Arquivo | Linha | Status |
|---|----------------|---------|-------|--------|
| 9 | Falta verificação de propriedade no dashboard | resources/views/pages/dashboard/⚡index.blade.php | 32-55 | [x] |
| 10 | editFuelling sem verificar se pertence ao usuário | resources/views/pages/fuelling/index.blade.php | 65-82 | [x] |

---

## 🟢 Configurações (Verificar)

| # | Item | Status |
|---|------|--------|
| 1 | Verificar se .env está no .gitignore | [x] |
| 2 | Verificar se vendor/ está no .gitignore | [x] |
| 3 | Verificar se node_modules/ está no .gitignore | [x] |
| 4 | Verificar se storage/logs/ está no .gitignore | [x] |

---

## Detalhes das Correções

### 1. User ID Hardcoded (CRÍTICO) - ✅ CORRIGIDO
```php
// ANTES (fuelling/index.blade.php:45)
'user_id' => 1,

// DEPOIS
'user_id' => auth()->id(),
```
Também foi adicionado verificação de propriedade em:
- editFuelling()
- updateFuelling()
- deleteFuelling()

### 2. API Key Exposta (CRÍTICO)
- Remover OPENROUTER_API_KEY do .env
- Adicionar no servidor como variável de ambiente
- Ou usar serviço de secrets

### 3. IDOR no PrismService (CRÍTICO) - ✅ CORRIGIDO
Adicionado verificação em todas as funções:
```php
// Verificar se o veículo pertence ao usuário autenticado
$car = Car::where('id', $id_veiculo)
    ->where('user_id', auth()->id())
    ->first();

if (!$car) {
    return "Erro: Veículo não encontrado ou não pertence ao usuário";
}
```
Verificações adicionadas em:
- MaitenancesTool()
- createMaitenanceTool()
- ViewFuellingsTool()
- CreateFuellingTool()

### 4. IDOR no StatisticService (CRÍTICO) - ✅ CORRIGIDO
```php
public function MaitenanceGraph($car_id)
{
    // Adicionar verificação
    $car = Car::where('id', $car_id)
        ->where('user_id', auth()->id())
        ->first();
        
    if (!$car) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    // ...
}
```

### 5-8. Configurações (.env) - ✅ CORRIGIDO EM 2026-03-06 14:30:00
```env
APP_DEBUG=false
SESSION_ENCRYPT=true
```

### 7. Access Control em editCar/deleteCar - ✅ CORRIGIDO EM 2026-03-06 14:30:00
Adicionado verificação de propriedade em todas as funções:
```php
// editCar
$db_car = Car::where('id', $carId)->where('user_id', auth()->id())->first();

// updateCar
$db_car = Car::where('id', $this->car['id'])->where('user_id', auth()->id())->first();

// deleteCar
$db_car = Car::where('id', $this->car_to_delete)->where('user_id', auth()->id())->first();
```

### 8. Access Control em editMaintenance/deleteMaintenance - ✅ CORRIGIDO EM 2026-03-06 14:30:00
Adicionado verificação de propriedade usando whereHas:
```php
// editMaintenance, updateMaintenance, deleteMaintenance
$maintenance = Maintenance::where('id', $id)
    ->whereHas('car', function($query) {
        $query->where('user_id', auth()->id());
    })
    ->first();
```

### 9. Verificação de propriedade no dashboard - ✅ CORRIGIDO EM 2026-03-06 14:30:00
Adicionado verificação em loadCarData():
```php
$this->selectedCarDetails = Car::where('id', $this->select_car)
    ->where('user_id', auth()->id())
    ->first();
```
