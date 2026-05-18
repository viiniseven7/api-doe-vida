# 🛠️ CHECKLIST DE PENDÊNCIAS (URGENTE)

O projeto está em uma fase de transição onde o Front-end já preparou a lógica para os novos relacionamentos do banco de dados, mas o sistema está bloqueado por erros no Back-end.

---

## 🔴 1. BLOQUEIO CRÍTICO (Sintaxe)
Corrigir imediatamente o arquivo:  
`app/Http/Controllers/AgendamentoController.php` na **Linha 18**.

*   **Erro:** `syntax error, unexpected token ")", expecting variable`.
*   **O que fazer:** Provavelmente há uma vírgula sobrando ou uma variável não declarada em algum método. Enquanto isso não for corrigido, a API retorna Erro 500 e nada aparece na tela.

---

## ⚙️ 2. REQUISITOS TÉCNICOS (API)
Para o fluxo de Doação funcionar, o Back-end deve garantir os seguintes retornos:

### A. Listagem de Agendamentos (`GET /api/agendamentos`)
Deve usar o `with(['triagem', 'doacao'])` para retornar este formato exato:
```json
{
  "id": 1,
  "status_agendamento": "CON",
  "triagem": { 
    "id": 10,
    "apto": true 
  },
  "doacao": null
}
```
*   **Importante:** Se a `triagem` não vier dentro do agendamento, o botão "Registrar Doação" nunca aparecerá.

### B. Objeto Usuário (`/auth/me`)
Deve incluir obrigatoriamente o campo:
*   `tempo_restricao`: (null ou data ISO "2026-05-10"). 
*   Isso é a "Fonte da Verdade" que o Front usa para bloquear novos agendamentos.

---

## 🚀 3. FLUXO DE ENVIO (Front -> Back)
O Front-end já está configurado para enviar os dados assim:

**Para Triagem:**
```json
{
  "agendamento_id": 1,
  "user_id": 10,
  "apto": true,
  "data_triagem": "2026-05-04"
}
```

**Para Doação:**
```json
{
  "agendamento_id": 1,
  "triagem_id": 10,
  "user_id": 10,
  "tipo_sangue": "O+",
  "quantidade": 450
}
```

---
**Status do Front-end:** 100% pronto e aguardando a correção da Linha 18 no Back-end.
