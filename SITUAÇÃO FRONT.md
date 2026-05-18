## 📊 Módulo: Relatórios & Estatísticas — GUIA DE CONSUMO (BACK-END PRONTO)

Os endpoints de relatórios e estatísticas foram implementados e otimizados. Abaixo, as instruções detalhadas para o Front-end.

---

### 1. Dashboards (JSON)

Estes endpoints retornam dados agregados, ideais para construção de gráficos (Chart.js, Recharts, etc.).

| Endpoint                             | Método | Descrição                                         | Parâmetros           |
| :----------------------------------- | :------ | :-------------------------------------------------- | :-------------------- |
| `/api/reports/donations-summary`   | `GET` | Totais por status (Agendado, Concluído, etc.)      | `dias` (padrão 30) |
| `/api/reports/blood-stock`         | `GET` | Saldo total de bolsas de sangue por tipo            | -                     |
| `/api/reports/performance-monthly` | `GET` | Doações nos últimos 12 meses (Gráfico de Linha) | -                     |

**Exemplo de Resposta (`donations-summary`):**

```json
[
  { "label": "Agendado", "total": 15 },
  { "label": "Concluído", "total": 42 },
  { "label": "Cancelado", "total": 3 }
]
```

---

### 2. Relatórios para Download (PDF)

Estes endpoints geram arquivos PDF reais. O front-end deve abrir estas URLs em uma nova aba ou usar um link de download.

* **Doações:** `GET /api/relatorios/doacoes?periodo=30`
* **Estoque:** `GET /api/relatorios/estoque`
* **Doadores:** `GET /api/relatorios/doadores`

> **Nota de Segurança:** Estes endpoints exigem o token Bearer no Header. Para download direto, recomenda-se que o Front-end busque o arquivo como `blob` e gere um link local ou use uma estratégia de `window.open` passando o token (se configurado).

---

### 3. Regras de Filtro por Papel (Roles)

O Back-end aplica filtros automáticos baseados no usuário autenticado:

1. **ADMIN (Geral):**
   * Vê dados de **todas as unidades** por padrão.
   * Pode filtrar uma unidade específica enviando `?hemocentro_id=X` na URL.
2. **DIRETOR / FUNCIONÁRIO:**
   * Vê **apenas os dados da sua unidade** (vínculo automático via `hemocentro_id` do perfil). Não é necessário enviar parâmetros de ID.

---

### 4. Dicas de Implementação no Front

* **Filtros de Período:** Use o parâmetro `?dias=X` para Dashboards e `?periodo=X` para PDFs.
* **Cores dos Gráficos:** No gráfico de estoque, utilize cores de alerta (vermelho) quando o valor retornado for menor que o esperado pelo seu design, embora o PDF já venha com a marcação "CRÍTICO".
* **Tipos Sanguíneos:** A lista segue a ordem padrão: `['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']`.

---

**Status da Implementação:** ✅ Concluído e Disponível.
**Biblioteca PDF:** `barryvdh/laravel-dompdf` (Instalada).
