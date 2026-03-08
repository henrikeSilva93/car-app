# Security Checklist - Car App

## Status

- **Situação:** Em andamento
- **Última revisão:** 2026-03-08
- **Escopo:** Aplicação Laravel, componentes Livewire/Volt, services e pipeline CI

---

## Resumo executivo

- Vulnerabilidades críticas de **IDOR** previamente identificadas em serviços principais foram tratadas com validação por `user_id`.
- Ainda existe pendência de higiene operacional: garantir segredos somente em ambiente seguro e não em arquivos locais compartilhados.
- Recomendado manter revisão de segurança a cada release.

---

## Itens críticos

| # | Item | Arquivo(s) | Status | Ação recomendada |
|---|---|---|---|---|
| 1 | Segredos em `.env` local/compartilhado | `.env` | ⚠️ Pendente | Garantir uso de secret manager no deploy/CI |
| 2 | Controle de acesso por ownership em serviços de IA | `app/Services/PrismService.php` | ✅ Resolvido | Manter testes cobrindo tentativa de acesso cruzado |
| 3 | Controle de acesso em endpoints de estatística | `app/Services/StatisticService.php` | ✅ Resolvido | Validar em testes de regressão |

---

## Itens altos

| # | Item | Arquivo(s) | Status | Ação recomendada |
|---|---|---|---|---|
| 4 | `APP_DEBUG` em produção | `.env` / ambiente de deploy | ✅ Resolvido | Confirmar `APP_DEBUG=false` em produção |
| 5 | Criptografia de sessão | `.env` | ✅ Resolvido | Confirmar `SESSION_ENCRYPT=true` |
| 6 | Access control em operações CRUD | páginas Livewire/Volt | ✅ Resolvido | Padronizar validações em todas as novas actions |

---

## Itens médios

| # | Item | Área | Status | Ação recomendada |
|---|---|---|---|---|
| 7 | Padronização de tratamento de exceções | Services | ⚠️ Parcial | Centralizar logs e mensagens de erro |
| 8 | Cobertura de testes focada em segurança | `tests/Feature` | ⚠️ Parcial | Adicionar cenários negativos (403/ownership) |
| 9 | Pipeline CI sem prompts interativos | `.github/workflows/ci.yml` | ⚠️ Parcial | Garantir `--no-interaction` em comandos críticos |

---

## Hardening recomendado

1. Definir política de rotação de segredos (trimestral).
2. Revisar permissões de escrita em `storage/` e `bootstrap/cache/` no ambiente de produção.
3. Implementar checklist de release com foco em:
- segurança de configuração
- validação de migrações
- regressão de autorização

---

## Verificações rápidas (release)

```bash
php artisan test
php artisan route:list
php artisan config:clear
php artisan optimize:clear
```

---

## Histórico

- **2026-03-06:** correções de ownership e hardening inicial.
- **2026-03-08:** normalização de documentação/encoding e consolidação deste checklist.
