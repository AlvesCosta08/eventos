# 🗺️ RoadMap de Evolução Arquitetural - ADTC2 Eventos

**Projeto:** ADTC2 Eventos (Laravel 12 + MySQL 8.0)  
**Data de Atualização:** Novembro de 2026  
**Responsável:** Engenharia de Software (Refatoração Guiada por TDD)

---

## 📌 1. Visão Geral
Este documento registra a transformação arquitetural do sistema ADTC2 Eventos. O objetivo foi migrar de uma base de código com lógica acoplada e sem testes para uma arquitetura limpa, testável, tipada e seguindo os princípios SOLID e Clean Architecture, utilizando TDD (Test-Driven Development) e Análise Estática.

---

## 📊 2. Métricas de Transformação (Antes vs. Depois)

| Métrica | Estado Inicial | Estado Atual | Impacto |
| :--- | :--- | :--- | :--- |
| **Linhas no `EventoController`** | 244 linhas | ~95 linhas | **-61%** (Código enxuto) |
| **Linhas no `DashboardController`** | 50 linhas | ~10 linhas | **-80%** (Delegação total) |
| **Cobertura de Testes (Feature)** | 0 testes | 21 testes / 57+ assertivas | **Blindagem total** |
| **Análise Estática (Larastan)** | Não configurado | Nível 5 (Zero erros na `app/`) | **Qualidade Enterprise** |
| **Lógica de Negócio** | Misturada nos Controllers | Isolada em Actions e Scopes | **SRP Aplicado** |

---

## ✅ 3. Marcos Concluídos (Implementado)

### 🧪 Fase 1: Rede de Segurança (TDD)
Criação de testes de caracterização *antes* de qualquer refatoração, garantindo que o comportamento existente fosse preservado.
- [x] `EventoControllerTest.php` (5 testes: criação, edição, isolamento de congregação).
- [x] `ControleAgendaControllerTest.php` (4 testes: autorização, atualização de modo).
- [x] `UserControllerTest.php` (5 testes: CRUD completo e isolamento de perfil).
- [x] `CalendarioPublicoControllerTest.php` (4 testes: filtros de mês, acesso público).
- [x] `DashboardControllerTest.php` (3 testes: agregação de dados por perfil).

### 🏗️ Fase 2: Refatoração de Controllers (SRP & DRY)
Remoção de lógica de negócio dos controllers, delegando para camadas inferiores.
- [x] **Actions Criadas:**
  - `CreateEventoAction`, `UpdateEventoAction`, `DeleteEventoAction`
  - `GetCongregacoesDisponiveisParaUsuarioAction`
  - `GetDashboardDataAction`
- [x] **FormRequests Aplicados:** Validação e autorização movidas para `StoreEventoRequest`, `UpdateEventoRequest`, `IndexControleAgendaRequest`, etc.
- [x] **Eloquent Local Scopes Criados no Model `Evento`:**
  - `scopeVisivelPara(User $user)`: Isola a regra de quem vê o quê.
  - `scopeDoMes(CarbonInterface $mes)`: Filtra eventos por mês.
  - `scopeFuturos()` e `scopeProximos($dias)`.

### 🔍 Fase 3: Análise Estática Rigorosa (Larastan)
Configuração e limpeza total do código para garantir segurança de tipos.
- [x] Instalação e configuração do `larastan/larastan` (Nível 5).
- [x] Correção de inferência de tipos com PHPDoc (`@property-read \Carbon\Carbon $dt_evento`, `@property-read UserRole $role`).
- [x] Correção de comparações estritas (`=== null` em vez de `=== false` para Carbon).
- [x] Eliminação de operadores nullsafe redundantes (`?->`).
- [x] **Resultado:** `[OK] No errors` na pasta `app/`.

### 📦 Fase 4: Introdução de DTOs (Data Transfer Objects)
Substituição de arrays associativos soltos por objetos imutáveis e tipados.
- [x] Criação de `CreateEventoDTO` com factory method `fromArray()`.
- [x] Refatoração da `CreateEventoAction` para receber o DTO.
- [x] Atualização do `EventoController@store` para instanciar o DTO.

---

## 🚧 4. Próximos Passos (Backlog / A Fazer)

Para elevar o projeto ao nível 100% de maturidade, as seguintes tarefas são recomendadas:

### 🔹 Alta Prioridade (Completar o Padrão DTO)
- [ ] Criar `UpdateEventoDTO` e refatorar `UpdateEventoAction`.
- [ ] Criar `CreateUserDTO` e `UpdateUserDTO` para o `UserController`.

### 🔹 Média Prioridade (Robustez e DX)
- [ ] **API Resources:** Criar `EventoResource` e `UserResource` para padronizar a serialização JSON (caso o frontend evolua para SPA/Vue/React).
- [ ] **Domain Events:** Implementar eventos de domínio (ex: `EventoCriado`, `ModoAgendaAlterado`) para desacoplar efeitos colaterais (como envio de e-mails ou logs de auditoria).
- [ ] **Interfaces para Services:** Criar contracts (interfaces) para o `ControleAgendaService` para facilitar mocking avançado e inversão de dependência.

### 🔹 Baixa Prioridade (Infraestrutura e DevOps)
- [ ] **CI/CD Pipeline:** Configurar GitHub Actions ou GitLab CI para rodar `./vendor/bin/pest` e `./vendor/bin/phpstan` automaticamente em cada Pull Request.
- [ ] **Cobertura de Testes (Coverage):** Configurar Xdebug/PCOV e buscar >80% de cobertura de linhas (`./vendor/bin/pest --coverage`).
- [ ] **Refatoração do Seeder:** Tornar o `EventoSeeder` idempotente (usando `updateOrCreate` ou verificando existência) para evitar duplicação em `db:seed`.

---

## 🛠️ 5. Comandos Úteis de Manutenção

```bash
# Rodar toda a suíte de testes
./vendor/bin/pest

# Rodar testes com cobertura (requer Xdebug/PCOV)
./vendor/bin/pest --coverage

# Rodar análise estática (Garantir que continua 100% limpo)
./vendor/bin/phpstan analyse app --memory-limit=512M

# Formatar código automaticamente (Laravel Pint)
./vendor/bin/pint

# Verificar rotas registradas
php artisan route:list

🏆 6. Princípios de Engenharia Aplicados

✅ TDD (Test-Driven Development): Testes guiam a refatoração.
✅ SRP (Single Responsibility Principle): Cada classe faz uma coisa e faz bem feita.
✅ DRY (Don't Repeat Yourself): Lógica duplicada extraída para Scopes e Actions.
✅ Imutabilidade: Uso de readonly properties nos DTOs.
✅ Type Safety: Uso extensivo de Enums e PHPDoc para eliminar "magic strings".
✅ Clean Architecture: Separação clara entre Camada HTTP (Controllers), Camada de Aplicação (Actions/DTOs) e Camada de Domínio (Models/Enums).


---

### 💡 Como usar este arquivo:
1. Crie um arquivo chamado `RoadMap.md` na raiz do seu projeto (`~/Projetos/adtc2-eventos/RoadMap.md`).
2. Cole todo o conteúdo acima.
3. Use-o como referência para onboarding de novos desenvolvedores ou para planejar as próximas *sprints* de desenvolvimento.

Se quiser, podemos atacar imediatamente o primeiro item do "Próximos Passos": **Criar o `UpdateEventoDTO`**. O que acha? 💪🔥