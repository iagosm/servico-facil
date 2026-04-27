# 📊 Arquitetura de Dados - Serviço Fácil

Este guia serve como mapa para desenvolvedores realizarem manutenções e expansões no banco de dados.

---

## 1. Diagrama Técnico Completo (ERD)
Este diagrama usa a notação *Crow's Foot*. A ponta com "tridente" indica o lado **Muitos** e a linha reta indica o lado **Um**.

```mermaid
erDiagram
    USERS ||--o{ SERVICOS : "supervisiona"
    USERS ||--o{ SERVICO_USERS : "atua como técnico"
    USERS ||--o{ SERVICO_STATUS : "registra mudança"
    USERS ||--o{ PEDIDOS : "solicita/recebe"

    CLIENTES ||--o{ SERVICOS : "solicita"
    
    SERVICOS ||--o{ SERVICO_EQUIPAMENTOS : "possui itens"
    SERVICOS ||--o{ SERVICO_USERS : "equipe designada"
    SERVICOS ||--o{ SERVICO_STATUS : "log de estados"
    SERVICOS ||--o{ ITENS_CLIENTE : "pertences anexos"

    SERVICO_EQUIPAMENTOS ||--o{ SERVICO_PROBLEMAS : "relatos"
    SERVICO_EQUIPAMENTOS ||--o{ SERVICO_PECAS : "trocas"

    ESTOQUE ||--o{ SERVICO_PECAS : "fornece peça"
    ESTOQUE ||--o{ PEDIDOS : "item do pedido"

    FORNECEDORES ||--o{ PEDIDOS : "vende para a loja"

    CLIENTES {
        bigint id PK
        string nome
        string telefone
        string email
        string endereco
        string cidade
        string estado
        string cep
        string documento
        text observacoes
    }

    SERVICOS {
        bigint id PK
        string numero UK
        bigint cliente_id FK
        bigint supervisor_id FK
        string tipo
        string status
        string prioridade
        text obs_internas
        text obs_cliente
        decimal valor_cobrado
        decimal custo_total
        date data_entrada
        date data_previsao
        date data_conclusao
        date data_entrega
        date validade_orcamento
    }

    SERVICO_EQUIPAMENTOS {
        bigint id PK
        bigint servico_id FK
        string tipo
        string marca
        string modelo
        string numero_serie
        text condicao_entrada
    }

    SERVICO_PROBLEMAS {
        bigint id PK
        bigint servico_equipamento_id FK
        text descricao
        text laudo_tecnico
        boolean resolvido
    }

    SERVICO_PECAS {
        bigint id PK
        bigint servico_equipamento_id FK
        bigint estoque_id FK
        string descricao
        integer quantidade
        decimal preco_custo
        decimal preco_cobrado
    }

    ESTOQUE {
        bigint id PK
        string nome
        enum tipo
        enum condicao
        string sku UK
        text descricao
        integer quantidade
        integer quantidade_minima
        decimal preco_custo
        decimal preco_venda
    }

    PEDIDOS {
        bigint id PK
        bigint estoque_id FK
        bigint fornecedor_id FK
        bigint solicitado_por FK
        bigint recebido_por FK
        string descricao
        integer quantidade
        decimal preco_unitario
        enum status
        string numero_pedido
        date data_solicitacao
        date data_pedido
        date data_previsao
        date data_recebimento
        text observacao
    }

    FORNECEDORES {
        bigint id PK
        string nome
        string telefone
        string email
        string contato
        string site
        text observacoes
        boolean ativo
    }

    SERVICO_USERS {
        bigint id PK
        bigint servico_id FK
        bigint user_id FK
        string papel
        datetime data_inicio
        datetime data_fim
        text observacao
    }

    SERVICO_STATUS {
        bigint id PK
        bigint servico_id FK
        bigint user_id FK
        string status_anterior
        string status_novo
        text observacao
    }

    ITENS_CLIENTE {
        bigint id PK
        bigint servico_id FK
        string descricao
    }
```

---

## 2. Dicionário de Chaves (Para Modificações)

Se você for alterar o banco, atente-se a estas conexões mestres:

| Tabela Origem | Tabela Destino | Chave Estrangeira (FK) | Comportamento ao Deletar |
| :--- | :--- | :--- | :--- |
| `clientes` | `servicos` | `cliente_id` | **CASCADE**: Deleta a OS se o cliente sumir. |
| `servicos` | `servico_equipamentos`| `servico_id` | **CASCADE**: Remove equipamentos ao excluir OS. |
| `estoque` | `servico_pecas` | `estoque_id` | **SET NULL**: Mantém o registro da peça na OS mesmo se o item sair do estoque. |
| `users` | `servicos` | `supervisor_id` | **SET NULL**: Mantém a OS mesmo se o usuário for excluído. |
| `servicos` | `servico_users` | `servico_id` | **CASCADE**: Remove vínculos de equipe ao excluir OS. |
| `servicos` | `servico_status` | `servico_id` | **CASCADE**: Remove histórico de status ao excluir OS. |

---

## 3. Cenários de Fluxo (Como as tabelas conversam)

### Exemplo: Fluxo de Peça e Estoque
1. O técnico identifica um problema em `servico_problemas`.
2. Ele adiciona uma peça em `servico_pecas`. 
   - Se a peça vem do seu armário, ela aponta para um `estoque_id`.
   - O campo `preco_venda` do `estoque` deve ser usado para preencher o `preco_cobrado` na OS.
   - O campo `preco_custo` do `estoque` deve ser copiado para `preco_custo` na `servico_pecas` para auditoria histórica.
3. Se não houver a peça:
   - Cria-se um registro em `pedidos` apontando para o `estoque_id` (que está zerado) e para um `fornecedor_id`.
   - Quando o `pedido` mudar o status para 'recebido', você deve somar a `quantidade` no `estoque`.

### Exemplo: Controle de Equipe
1. Uma OS em `servicos` possui um Supervisor fixo (`supervisor_id`).
2. Adicionalmente, múltiplos técnicos podem ser vinculados via `servico_users`.
   - O campo `papel` define a função (ex: 'técnico', 'auxiliar').
   - `data_inicio` e `data_fim` permitem rastrear o tempo dedicado.

---

## 4. Dicas para Futuras Alterações
- **Novos Campos**: Sempre que adicionar um valor monetário, use `decimal(10,2)`.
- **Soft Deletes**: Se planeja permitir "desfazer exclusões", precisará adicionar a coluna `deleted_at` nas tabelas principais (`clientes`, `servicos`, `estoque`).
- **Logs**: A tabela `servico_status` registra todas as transições de estado da OS, armazenando o `status_anterior` e `status_novo` para fins de auditoria.
