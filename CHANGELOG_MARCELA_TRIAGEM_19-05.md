# Doe Vida — Registro de Alterações

**Período:** Maio/2026  
**Banco de dados:** MySQL  
**Backend:** Laravel 12 (`api-doe-vida`)  
**Frontend:** React 18 + TypeScript + Vite (`WEB-DOE-VIDA-`)

---

## Índice

1. [Sprint 1A — Correções de bugs existentes](#sprint-1a--correções-de-bugs-existentes)
2. [Sprint 1B — Novas migrations](#sprint-1b--novas-migrations)
3. [Sprint 2 — Models novos e atualizados](#sprint-2--models-novos-e-atualizados)
4. [Sprint 3 — Seeder de perguntas da triagem](#sprint-3--seeder-de-perguntas-da-triagem)
5. [Sprint 4 — AuthController: LGPD e pré-triagem no cadastro](#sprint-4--authcontroller-lgpd-e-pré-triagem-no-cadastro)
6. [Sprint 5 — TriagemController: fluxo clínico completo](#sprint-5--triagemcontroller-fluxo-clínico-completo)
7. [Sprint 6 — AlertaMedicoController](#sprint-6--alertamedicocontroller)
8. [Sprint 7 — UserTipoSangueHistoricoController](#sprint-7--usertiposanguehistoricocontroller)
9. [Sprint 8 — EligibilityTestPage: perguntas dinâmicas](#sprint-8--eligibilitytestpage-perguntas-dinâmicas)
10. [Sprint 9 — RegistrationDonationPage: LGPD e pré-triagem](#sprint-9--registrationdonationpage-lgpd-e-pré-triagem)
11. [Sprint 10 — DonorDashboard: correções críticas](#sprint-10--donordashboard-correções-críticas)
12. [Sprint 11 — StaffDashboard: triagem clínica dinâmica](#sprint-11--staffdashboard-triagem-clínica-dinâmica)
13. [Sprint 12 — StaffDashboard: alertas médicos e histórico de tipo sanguíneo](#sprint-12--staffdashboard-alertas-médicos-e-histórico-de-tipo-sanguíneo)
14. [Sprint 13 — DirectorDashboard e AdminDashboard: estatísticas e relatórios reais](#sprint-13--directordashboard-e-admindashboard-estatísticas-e-relatórios-reais)
15. [Referência de rotas adicionadas](#referência-de-rotas-adicionadas)
16. [Referência de tabelas adicionadas](#referência-de-tabelas-adicionadas)

---

## Sprint 1A — Correções de bugs existentes

**Contexto:** Bugs silenciosos no SQLite que quebrariam em MySQL.

### Arquivo criado
| Arquivo | O que faz |
|---|---|
| `database/migrations/2026_05_19_000000_fix_duplicate_timestamps.php` | Remove as colunas `created_at` e `updated_at` redundantes das tabelas `hemocentros` e `doacao`, que tinham essas colunas duplicadas por chamar `timestamps()` junto com timestamps customizados |

### Arquivos alterados
| Arquivo | Alteração |
|---|---|
| `app/Models/Doacao.php` | Adicionado `const CREATED_AT = null` e `const UPDATED_AT = 'atualizado_em'` — o model não declarava constantes de timestamp, causando erro de coluna inexistente no MySQL |
| `app/Models/Triagem.php` | Confirmado que **não** tem constantes de timestamp customizadas — correto, pois a tabela `triagens` usa `timestamps()` padrão |

---

## Sprint 1B — Novas migrations

**Contexto:** Criação de todas as tabelas necessárias para os novos módulos. Rodado com `php artisan migrate` sem `migrate:fresh` para preservar dados existentes.

### Arquivos criados
| Migration | Tabela criada | Descrição |
|---|---|---|
| `2026_05_19_000001_add_bloco_to_triagem_perguntas.php` | (alter) `triagem_perguntas` | Adiciona coluna `bloco` (tinyint): `0` = pré-triagem pública, `1` = estado geral, `3` = histórico recente, `4` = histórico comportamental |
| `2026_05_19_000002_create_pre_triagem_respostas_table.php` | `pre_triagem_respostas` | Respostas do questionário público de elegibilidade respondido pelo doador antes de chegar ao hemocentro. FK para `users`, `triagem_perguntas` e `triagem_opcoes` |
| `2026_05_19_000003_create_triagem_sinais_vitais_table.php` | `triagem_sinais_vitais` | Sinais vitais medidos pelo funcionário na triagem clínica: peso, pressão sistólica/diastólica, temperatura, frequência cardíaca, hemoglobina, hematócrito. FK para `triagens` |
| `2026_05_19_000004_create_triagem_aptidao_table.php` | `triagem_aptidao` | Resultado formal da triagem clínica: resultado (apto/inapto_temporario/inapto_definitivo), categoria da inaptidão (lista controlada — nunca diagnóstico), observações internas (só funcionários veem), notificação ao doador (versão genérica), valido_ate. FK única para `triagens` |
| `2026_05_19_000005_create_alertas_medicos_table.php` | `alertas_medicos` | Convocações pós-doação criadas pelo funcionário. Soft delete. FK para `users` (doador), `hemocentros`, `users` (criado_por) |
| `2026_05_19_000006_create_user_tipo_sangue_historico_table.php` | `user_tipo_sangue_historico` | Histórico de alterações de tipo sanguíneo do doador. Tipo anterior, novo, quem alterou, categoria do motivo (lista controlada). FK para `users` |
| `2026_05_19_000007_add_lgpd_to_users_table.php` | (alter) `users` | Adiciona `lgpd_aceite` (boolean), `lgpd_aceite_em` (timestamp) e `lgpd_ip` (varchar 45, suporta IPv6) |

---

## Sprint 2 — Models novos e atualizados

### Arquivos criados
| Model | Tabela | Destaques |
|---|---|---|
| `app/Models/PreTriagemResposta.php` | `pre_triagem_respostas` | `CREATED_AT = 'respondido_em'`, `UPDATED_AT = null`. Relacionamentos: `doador()`, `pergunta()`, `opcao()` |
| `app/Models/TriagemSinaisVitais.php` | `triagem_sinais_vitais` | `CREATED_AT = 'criado_em'`, `UPDATED_AT = null`. Métodos auxiliares: `pressaoNormal()`, `temperaturaNormal()`, `hemoglobinaNormal(string $sexo)` com os limites do Ministério da Saúde |
| `app/Models/TriagemAptidao.php` | `triagem_aptidao` | `CREATED_AT = 'criado_em'`, `UPDATED_AT = 'atualizado_em'`. Método `restricaoAtiva()`. Accessor `getCategoriaLabelAttribute()` com labels legíveis |
| `app/Models/AlertaMedico.php` | `alertas_medicos` | `SoftDeletes`. `CREATED_AT = 'criado_em'`, `UPDATED_AT = 'atualizado_em'`, `DELETED_AT = 'deletado_em'`. Relacionamentos: `doador()`, `hemocentro()`, `criadoPor()` |
| `app/Models/UserTipoSangueHistorico.php` | `user_tipo_sangue_historico` | `CREATED_AT = 'alterado_em'`, `UPDATED_AT = null`. Relacionamentos: `doador()`, `alteradoPor()` |

### Arquivos implementados (estavam vazios — 0 bytes)
| Model | Destaques |
|---|---|
| `app/Models/TriagemPergunta.php` | `SoftDeletes`. Campo `bloco` no fillable. Scopes: `scopeAtivas()`, `scopeDoBloco(int $bloco)`, `scopePreTriagem()`, `scopeTriagemClinica()`. Relacionamentos: `opcoes()`, `respostas()`, `preTriagemRespostas()` |
| `app/Models/TriagemOpcao.php` | Campo `gera_inaptidao` como boolean. Método auxiliar `ehDefinitiva()` (dias_inaptidao === 9999). Relacionamentos: `pergunta()`, `respostas()` |
| `app/Models/TriagemResposta.php` | Relacionamentos: `triagem()`, `pergunta()`, `opcao()` |

### Arquivos alterados
| Model | Alteração |
|---|---|
| `app/Models/Triagem.php` | Adicionados relacionamentos: `sinaisVitais()` (hasOne), `aptidao()` (hasOne), `respostas()` (hasMany) |
| `app/Models/User.php` | Adicionados relacionamentos: `preTriagemRespostas()`, `alertasMedicos()`, `tipoSangueHistorico()`. Adicionados ao `$fillable`: `lgpd_aceite`, `lgpd_aceite_em`, `lgpd_ip`. Adicionados ao `$casts`: `lgpd_aceite => boolean`, `lgpd_aceite_em => datetime`. Adicionado `SoftDeletes` com `deletado_em` |

---

## Sprint 3 — Seeder de perguntas da triagem

### Arquivo criado
| Arquivo | Descrição |
|---|---|
| `database/seeders/TriagemPerguntaSeeder.php` | Popula `triagem_perguntas` e `triagem_opcoes` com 22 perguntas e 50 opções baseadas nas diretrizes do Ministério da Saúde. Usa `firstOrCreate` — idempotente. Distribuição: 7 perguntas bloco 0 (pré-triagem), 5 perguntas bloco 1 (estado geral), 6 perguntas bloco 3 (histórico recente), 4 perguntas bloco 4 (comportamental) |

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `database/seeders/DatabaseSeeder.php` | Adicionado `TriagemPerguntaSeeder::class` antes de `AgendamentoSeeder`, `TriagemSeeder` e `DoacaoSeeder` (dependência de ordem: perguntas precisam existir antes das triagens) |

---

## Sprint 4 — AuthController: LGPD e pré-triagem no cadastro

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `app/Http/Controllers/AuthController.php` | **`register()`**: adicionada validação de `lgpd_aceite` (required\|accepted) e `respostas_pre_triagem` (array opcional). Campos `lgpd_aceite = true`, `lgpd_aceite_em = now()`, `lgpd_ip = $request->ip()` gravados no usuário. Após `assignRole()`, salva respostas em `pre_triagem_respostas`. Response 201 inclui `lgpd_aceite_em`. **Novo método `excluirConta()`**: anonimiza dados pessoais do doador (nome, e-mail, CPF, endereço, dados sensíveis), revoga todos os tokens Sanctum, registra log de auditoria LGPD e aplica soft delete. **Novo método `meusDados()`**: retorna todos os dados que o sistema possui sobre o doador autenticado (portabilidade LGPD Art. 18), incluindo pré-triagem, histórico de tipo sanguíneo e alertas médicos |

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `routes/api.php` | Adicionadas 2 rotas dentro do grupo `auth:sanctum`: `DELETE /api/auth/minha-conta` e `GET /api/auth/meus-dados` |

---

## Sprint 5 — TriagemController: fluxo clínico completo

### Arquivo substituído
| Arquivo | Alteração |
|---|---|
| `app/Http/Controllers/TriagemController.php` | Reescrito completamente. **`index()`**: eager loading ampliado com `sinaisVitais`, `aptidao`, `respostas.pergunta`, `respostas.opcao`. **Novo método `perguntas()`**: retorna perguntas por bloco (`?bloco=N`) para o frontend montar formulários. **`store()`**: reescrito com transação DB. Aceita `sinais_vitais` (array de campos numéricos), `respostas` (array de pergunta_id/opcao_id), `aptidao` (resultado, categoria, observações internas, notificação doador, valido_ate). Salva em `triagem_sinais_vitais`, `triagem_respostas` e `triagem_aptidao`. Mantém `triagens.apto` e `triagens.motivo_inaptidao` para compatibilidade com `DoacaoController`. Gera notificação padrão ao doador se não fornecida. **`show()`**: oculta `observacoes_internas` quando o solicitante é o próprio doador (LGPD). **`update()`**: suporta atualizar `triagem_aptidao` com `updateOrCreate`. Log de auditoria em todas as operações. Rollback em caso de erro |

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `routes/api.php` | Adicionada rota `GET /api/triagens/perguntas` declarada **antes** de `GET /api/triagens/{id}` para evitar conflito de rota |

---

## Sprint 6 — AlertaMedicoController

### Arquivo criado
| Arquivo | Descrição |
|---|---|
| `app/Http/Controllers/AlertaMedicoController.php` | CRUD completo. **`index()`** e **`show()`**: doador vê apenas `id`, `tipo_alerta`, `status`, `notificacao_doador`, `criado_em` — sem campos internos (LGPD). Funcionário/diretor vê tudo do hemocentro. **`store()`**: funcionário cria alerta vinculado a doador, com log de auditoria. Valida que o alvo é um doador (`role_id == 1`). **`update()`**: atualiza status (pendente → compareceu → encerrado). **`destroy()`**: encerra logicamente e aplica soft delete |

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `routes/api.php` | Adicionadas 5 rotas: `GET /api/alertas-medicos`, `GET /api/alertas-medicos/{id}`, `POST /api/auth/alertas-medicos`, `PUT /api/auth/alertas-medicos/{id}`, `DELETE /api/auth/alertas-medicos/{id}` |

---

## Sprint 7 — UserTipoSangueHistoricoController

### Arquivo criado
| Arquivo | Descrição |
|---|---|
| `app/Http/Controllers/UserTipoSangueHistoricoController.php` | **`index($userId)`**: retorna histórico completo de alterações do tipo sanguíneo de um doador. Requer que o funcionário tenha vínculo com o doador (triagem no mesmo hemocentro). Doadores não acessam (403). **`store($userId)`**: altera o tipo sanguíneo com registro histórico obrigatório. Bloqueia se o tipo novo for igual ao atual. Valida que o alvo é doador. Proíbe alterar o próprio tipo. Usa transação DB. Log de auditoria. Retorna tipo anterior e novo na resposta |

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `routes/api.php` | Adicionadas 2 rotas dentro de `auth:sanctum` + prefixo `auth`: `GET /api/auth/doadores/{userId}/tipo-sangue-historico`, `POST /api/auth/doadores/{userId}/tipo-sangue-historico` |

---

## Sprint 8 — EligibilityTestPage: perguntas dinâmicas

### Arquivo substituído
| Arquivo | Alteração |
|---|---|
| `src/components/EligibilityTestPage.tsx` | Substituído de 10 perguntas hardcoded para busca dinâmica via `GET /api/triagens/perguntas?bloco=0`. Tipagem TypeScript para `Pergunta`, `Opcao` e `RespostaParaSalvar`. Lógica de resultado baseada em `gera_inaptidao` das opções (vindo do banco). Ao chegar no resultado `eligible`, salva as respostas no `sessionStorage` com chave `pre_triagem` no formato `{ resultado_geral, respostas: [{pergunta_id, opcao_id}] }`. Botão "Continuar para Cadastro" chama `handleContinuarCadastro()` que garante o save antes de navegar. Tela de loading enquanto busca perguntas. Tela de erro se API indisponível. JSX das telas de resultado (verde/vermelho/amarelo) preservado sem alteração |

---

## Sprint 9 — RegistrationDonationPage: LGPD e pré-triagem

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `src/components/RegistrationDonationPage.tsx` | **State novo**: `lgpdAceite` (boolean, default false). **`buildRegistrationData()`**: reescrita para ler respostas do `sessionStorage` (`pre_triagem`) e incluir no payload `respostas_pre_triagem`. Inclui `lgpd_aceite: true` no payload. **`validatePersonal()`**: adicionada validação do checkbox LGPD — retorna erro se não marcado. **JSX**: adicionado checkbox LGPD com link para `/privacidade` entre o bloco de erros do responsável e o botão de submit. **`handleSkipAppointment()` e `handleAppointmentSubmit()`**: após cadastro bem-sucedido, remove `pre_triagem` do `sessionStorage` |

---

## Sprint 10 — DonorDashboard: correções críticas

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `src/components/dashboards/DonorDashboard.tsx` | **`handleCancelAppointment()`**: corrigido de `api.put(...)` para `api.post('/auth/agendamentos/{id}/cancelar')` — rota correta do backend. **`handleRescheduleConfirm()`**: corrigido de `api.put(...)` para cancelar o agendamento atual via `POST /cancelar` e criar um novo via `POST /auth/agendamentos`. Limpa os campos do dialog após reagendamento. **Filtro do histórico**: corrigido de `['FIN', 'concluido', 'Finalizado']` (inexistentes no backend) para `['CAN', 'EXC']` (status reais). **Badge do histórico**: substituído de "Concluído" fixo para dinâmico: "Cancelado" (vermelho) para `CAN`, "Substituído" (cinza) para `EXC` |

---

## Sprint 11 — StaffDashboard: triagem clínica dinâmica

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `src/components/dashboards/StaffDashboard.tsx` | **States novos**: `perguntas`, `respostasTriagem` (Record\<number, number\>), `sinaisVitais` (7 campos numéricos), `aptidaoFormal` (resultado, categoria, observações internas, valido_ate). **`fetchData()`**: ampliado para buscar perguntas dos blocos 1, 3 e 4 em paralelo via `GET /triagens/perguntas?bloco=N` e combinar em ordem. **`handleRegistrarTriagem()`**: reescrito. Valida perguntas obrigatórias não respondidas. Valida aptidão e categoria. Monta payload completo com sinais vitais, respostas e aptidão. Chama `POST /auth/triagens` — se apto, chama `POST /auth/doacoes` em seguida. Reseta todos os estados após sucesso. Tratamento de erro com extração de mensagem do backend. **Dialog de triagem**: substituído de campos fixos para formulário dinâmico com seção de sinais vitais (7 campos), perguntas dos 3 blocos em seções separadas com radio buttons, campo de volume coletado (visível só quando apto), e painel de aptidão formal com 3 botões de resultado, select de categoria, datepicker de validade e textarea de observações internas |

---

## Sprint 12 — StaffDashboard: alertas médicos e histórico de tipo sanguíneo

### Arquivo alterado
| Arquivo | Alteração |
|---|---|
| `src/components/dashboards/StaffDashboard.tsx` | **States novos**: `alertaDialogOpen`, `alertaDoador`, `alertaForm` (tipo_alerta, notificacao_doador), `tipoSangDialogOpen`, `tipoSangDoador`, `tipoSangHistorico`, `tipoSangForm` (tipo_sangue_novo, categoria_motivo). **`handleAbrirAlerta()`**: abre dialog de alerta para um doador selecionado. **`handleCriarAlerta()`**: chama `POST /auth/alertas-medicos`, valida mensagem obrigatória. **`handleAbrirTipoSang()`**: busca histórico via `GET /auth/doadores/{id}/tipo-sangue-historico` e abre dialog. **`handleSalvarTipoSang()`**: chama `POST /auth/doadores/{id}/tipo-sangue-historico`. **JSX**: adicionados botões "Criar Alerta Médico" e "Histórico Tipo Sanguíneo" na seção de doadores. **Dialog de alerta médico**: select de tipo, textarea de mensagem com aviso para não incluir diagnósticos. **Dialog de histórico de tipo sanguíneo**: lista histórico em ordem decrescente, formulário de nova alteração com select de tipo e select de categoria de motivo (lista controlada) |

---

## Sprint 13 — DirectorDashboard e AdminDashboard: estatísticas e relatórios reais

### Arquivos alterados
| Arquivo | Alteração |
|---|---|
| `src/components/dashboards/DirectorDashboard.tsx` | **State novo**: `statsDir`. **`fetchData()`**: adicionado `GET /api/estatisticas/diretor` ao `Promise.all`. **Cards de contadores**: conectados a `statsDir.agendamentos_hoje`, `statsDir.confirmados_hoje`, `statsDir.doacoes_mes`, `statsDir.estoque_critico.length`. **`handleExportReport()`**: corrigido de toast simulado para download real — usa `fetch()` com token Bearer, recebe blob PDF e cria link de download com nome `relatorio-{tipo}-{data}.pdf`. Endpoints: `/api/relatorios/doacoes`, `/api/relatorios/estoque`, `/api/relatorios/doadores` |
| `src/components/dashboards/AdminDashboard.tsx` | **State novo**: `statsAdmin`. **`fetchData()`**: adicionado `GET /api/estatisticas/admin` ao `Promise.all`. **Cards globais**: conectados a `statsAdmin.total_hemocentros`, `statsAdmin.total_doadores`, `statsAdmin.total_doacoes`. **`handleExportReport()`**: corrigido de toast simulado para download real (mesmo padrão do DirectorDashboard) |

---

## Referência de rotas adicionadas

Todas as rotas abaixo foram adicionadas às já existentes em `routes/api.php`.

### Autenticação e LGPD
| Método | Rota | Descrição |
|---|---|---|
| `DELETE` | `/api/auth/minha-conta` | Anonimizar conta (LGPD Art. 18 — direito de exclusão) |
| `GET` | `/api/auth/meus-dados` | Exportar todos os dados do doador (LGPD Art. 18 — portabilidade) |

### Triagem
| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/triagens/perguntas` | Listar perguntas por bloco (`?bloco=0/1/3/4`) |

### Alertas médicos
| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/alertas-medicos` | Listar alertas (doador vê só os seus sem campos internos) |
| `GET` | `/api/alertas-medicos/{id}` | Detalhe de um alerta |
| `POST` | `/api/auth/alertas-medicos` | Criar alerta médico (funcionário) |
| `PUT` | `/api/auth/alertas-medicos/{id}` | Atualizar status do alerta |
| `DELETE` | `/api/auth/alertas-medicos/{id}` | Encerrar e soft delete |

### Histórico de tipo sanguíneo
| Método | Rota | Descrição |
|---|---|---|
| `GET` | `/api/auth/doadores/{userId}/tipo-sangue-historico` | Histórico de alterações do tipo sanguíneo |
| `POST` | `/api/auth/doadores/{userId}/tipo-sangue-historico` | Registrar nova alteração controlada |

---

## Referência de tabelas adicionadas

| Tabela | Propósito | Soft delete | Timestamps |
|---|---|---|---|
| `pre_triagem_respostas` | Respostas do questionário público de elegibilidade | Não | `respondido_em` (manual) |
| `triagem_sinais_vitais` | Sinais vitais da triagem clínica presencial | Não | `criado_em` (manual) |
| `triagem_aptidao` | Resultado formal da triagem com categoria controlada e isolamento LGPD | Não | `criado_em`, `atualizado_em` |
| `alertas_medicos` | Convocações pós-doação sem diagnóstico exposto ao doador | Sim (`deletado_em`) | `criado_em`, `atualizado_em` |
| `user_tipo_sangue_historico` | Histórico imutável de alterações de tipo sanguíneo | Não | `alterado_em` (manual) |

### Colunas adicionadas em tabelas existentes
| Tabela | Coluna | Tipo | Descrição |
|---|---|---|---|
| `triagem_perguntas` | `bloco` | `tinyint unsigned` | Grupo temático: 0 = pré-triagem, 1/3/4 = clínica |
| `users` | `lgpd_aceite` | `boolean` | Consentimento LGPD |
| `users` | `lgpd_aceite_em` | `timestamp` | Quando aceitou |
| `users` | `lgpd_ip` | `varchar(45)` | IP no momento do aceite (suporta IPv6) |

### Colunas removidas (correção de duplicidade)
| Tabela | Colunas removidas | Motivo |
|---|---|---|
| `hemocentros` | `created_at`, `updated_at` | Duplicadas — tabela usa `criado_em` e `atualizado_em` customizados |
| `doacao` | `created_at`, `updated_at` | Duplicadas — tabela usa `atualizado_em` customizado |

---

## Decisões de arquitetura registradas

| Decisão | Justificativa |
|---|---|
| `triagens.apto` e `triagens.motivo_inaptidao` mantidos | `DoacaoController` depende desses campos para validar aptidão. A nova tabela `triagem_aptidao` complementa sem substituir, evitando regressão |
| Perguntas de pré-triagem e triagem clínica na mesma tabela `triagem_perguntas` | Separadas pelo campo `bloco`. Compartilham `triagem_opcoes` com `gera_inaptidao` e `dias_inaptidao` |
| `observacoes_internas` nunca exposta ao doador | Verificação no `show()` do `TriagemController` com `makeHidden()`. Doador recebe apenas `notificacao_doador` |
| Motivo de inaptidão como enum controlado | Nunca texto livre — evita exposição de diagnósticos em campo aberto (LGPD Art. 11) |
| Histórico de tipo sanguíneo em tabela separada | Rastreabilidade sem sobrescrever. Motivo sempre categoria controlada, nunca texto livre |
| Reagendamento = cancelar + criar novo | Preserva histórico completo de agendamentos para auditoria. Status `EXC` marca o substituído |
| Respostas da pré-triagem via `sessionStorage` | Passagem de dados entre `EligibilityTestPage` → `RegistrationDonationPage` sem estado global ou URL params. Limpo após cadastro |
