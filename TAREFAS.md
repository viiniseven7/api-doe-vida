# TAREFAS.MD - Lista de Desenvolvimento Doe Vida API

## 📋 **Metodologia de Priorização**

- **Prioridade 1 (Crítica)**: Funcionalidades core que impedem o funcionamento básico
- **Prioridade 2 (Alta)**: Funcionalidades essenciais para MVP
- **Prioridade 3 (Média)**: Melhorias importantes mas não críticas
- **Prioridade 4 (Baixa)**: Funcionalidades avançadas e otimizações

---

## 🛠 **TAREFAS PENDENTES**

### 📧 **MÓDULO: NOTIFICAÇÕES POR E-MAIL (Prioridade 2)**

- [ ] **Notificações de Agendamento**
  - Criar notificações: confirmação, lembrete (24h antes), cancelamento.
  - Templates HTML em `resources/views/mail`.
- [ ] **Alertas de Restrição**
  - Notificar doador quando o período de restrição biológica terminar.
- [ ] **Alertas de Estoque Crítico**
  - Notificar funcionários quando um tipo sanguíneo atingir a `quantidade_minima`.

### 🎯 **MÓDULO: CAMPANHAS (Prioridade 2)**

- [ ] **Criar Model Campanha**
  - Relationships: belongsToMany Hemocentros.
- [ ] **Criar CampanhaController**
  - Implementar CRUD completo para Admin/Diretor.
- [ ] **Adicionar Rotas de Campanhas**
  - `POST /api/auth/campanhas`, `GET /api/campanhas`, etc.

### 🔐 **MÓDULO: PERMISSÕES SPATIE - GRANULARIDADE (Prioridade 3)**

- [ ] **Permissions Específicas**
  - Criar permissions: `editar_usuario`, `ver_relatorios`, `gerenciar_estoque`.
- [ ] **Implementar Middleware de Permissões**
  - Substituir checagem manual de `role_id` nos Controllers pelo middleware do Spatie.

### ⚙️ **MÓDULO: CONFIGURAÇÕES AVANÇADAS (Prioridade 3)**

- [ ] **Parâmetros de Hemocentro**
  - Horários de funcionamento e **limite de doações diárias/por hora**.
- [ ] **Reagendamento Inteligente**
  - Facilitar a troca de data sem precisar cancelar e criar um novo do zero.

### 🧪 **MÓDULO: TESTES E QUALIDADE (Prioridade 4)**

- [ ] **Testes de Integração (Workflow Completo)**
  - Testar o fluxo: Elegibilidade -> Agendamento -> Triagem -> Doação -> Certificado.
- [ ] **Documentação de Testes**
  - Exportar coleção Postman/Insomnia atualizada.

---

## ✅ **TAREFAS CONCLUÍDAS**

### 🔐 **AUTENTICAÇÃO E ELEGIBILIDADE**

- [X] Registro de Doador Livre (sem hemocentro fixo).
- [X] Recuperação de senha via e-mail.
- [X] **Persistência de Elegibilidade (Autoexame)**: Bloqueio de agendamento para inaptos no back-end.

### 📅 **AGENDAMENTOS**

- [X] Fluxo de criação com validação de 90/120 dias.
- [X] **Visibilidade Staff**: Exibição de agendamentos cancelados para reabertura.
- [X] **Ciclo de Vida**: Transição automática para status `FIN` após doação.

### 🩺 **TRIAGEM MÉDICA**

- [X] Migration, Model e Controller de Triagem.
- [X] Registro de aptidão e motivos de inaptidao.
- [X] Integração obrigatória Triagem -> Doação.

### 🩸 **DOAÇÃO E ESTOQUE**

- [X] Registro completo de doação (ml, tipo sanguíneo, validade).
- [X] **Integração de Estoque**: Soma automática ao estoque do hemocentro após doação.
- [X] Gestão de estoque (ajuste manual e listagem).

### 📊 **DASHBOARDS E RELATÓRIOS**

- [X] Estatísticas para Dashboard (JSON).
- [X] Relatórios PDF: Doações, Estoque e Doadores.
- [X] **Certificados de Doação**: Geração de PDF oficial com código de autenticação para doadores.

---

**Próximo Passo Recomendado:** Iniciar o **Módulo de Campanhas** para permitir que os hemocentros solicitem doações ativamente.
