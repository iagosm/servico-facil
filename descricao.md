# Serviço Fácil — Documentação Técnica Completa

## 1. VISÃO GERAL

- **Propósito:** Sistema de gestão para assistências técnicas e prestadores de serviços, focado no controle de Ordens de Serviço (OS), clientes, estoque de peças, fornecedores e equipe técnica.
- **Público-alvo:** Administradores de assistências técnicas, supervisores e técnicos de campo ou laboratório.
- **Tecnologias:**
  - **Backend:** Laravel 13 (PHP 8.3+)
  - **Frontend:** Vue 3 com Inertia.js e TypeScript
  - **Estilização:** Tailwind CSS 4
  - **Autenticação:** Laravel Fortify
  - **Banco de Dados:** SQLite (padrão de desenvolvimento/kit inicial)
  - **Outras Libs:** Chart.js (gráficos), Lucide Vue Next (ícones), Maska (máscaras de input).
- **Arquitetura:** Monolito moderno utilizando o padrão **Singe Page Application (SPA)** via Inertia.js, mantendo a lógica de rotas e controladores no Laravel e a UI reativa no Vue.

## 2. MAPA DE PÁGINAS / ROTAS

### Área Pública
- `/`: **Welcome** - Página inicial com links de login/registro.

### Área Logada (Middleware `auth`, `verified`)
- `/dashboard`: **Dashboard** - Visão geral com métricas de faturamento, lucro, OS por status e alertas de estoque baixo.
- `/financeiro`: **Financeiro** - Relatórios detalhados de faturamento vs custo, ranking de técnicos e peças mais usadas.
- `/clientes`: **Gestão de Clientes** - Listagem, cadastro, edição e exclusão de clientes.
- `/estoque`: **Gestão de Estoque** - Controle de peças e insumos (CRUD).
- `/pedidos`: **Pedidos de Compra** - Controle de solicitações de peças a fornecedores.
- `/servicos`: **Ordens de Serviço** - Fluxo principal do sistema (Listagem, Criação, Edição, Detalhes).
- `/equipe`: **Gestão de Equipe** - CRUD de usuários (técnicos e administrativos).
- `/fornecedores`: **Gestão de Fornecedores** - Cadastro de parceiros comerciais.

### Configurações
- `/settings/profile`: Perfil do usuário.
- `/settings/security`: Segurança (senha).
- `/settings/appearance`: Aparência do sistema (temas).

## 3. FORMULÁRIOS

### Cadastro de Cliente
- **Página:** `/clientes/create` e `/clientes/{id}/edit`
- **Campos:** Nome (string, req), Telefone (string, req), Email (email), Endereço, Cidade, Estado, CEP, Documento (CPF/CNPJ), Observações.
- **Ação:** Cria ou atualiza o registro no banco e redireciona com mensagem de sucesso.

### Ordem de Serviço (OS)
- **Página:** `/servicos/create`
- **Campos Principais:** Cliente (id, req), Tipo (diagnostico, reparo, orcamento, req), Prioridade (normal, urgente, aguardando_aprovacao, req), Supervisor (id), Data Previsão, Valor Cobrado, Obs Internas, Obs Cliente.
- **Equipamentos (Array):** Tipo, Marca, Modelo, Número de Série, Condição de Entrada.
- **Problemas (Array por Equipamento):** Descrição.
- **Técnicos (Array):** User ID, Papel (executor, supervisor, auxiliar).
- **Itens do Cliente (Array):** Descrição (ex: "carregador", "capa").
- **Ação:** Inicia uma transação no banco, gera número da OS (ex: OS-0001), salva todos os relacionamentos e registra o status inicial "recebido" na timeline.

### Pedido de Compra
- **Página:** `/pedidos/create`
- **Campos:** Item do Estoque (id), Fornecedor (id), Descrição, Quantidade, Preço Unitário, Status (pendente, pedido, recebido, cancelado), Datas (solicitação, pedido, previsão).

## 4. BANCO DE DADOS

### Tabelas Principais
- **`users`:** Usuários do sistema. Campos: `name`, `email`, `password`, `cargo` (admin, gerente, supervisor, tecnico), `telefone`, `ativo`, `supervisor_id`.
- **`clientes`:** Dados dos clientes. Campos: `nome`, `telefone`, `email`, `endereco`, `documento`, `observacoes`.
- **`servicos`:** Cabeçalho da OS. Campos: `numero` (unique), `cliente_id`, `status`, `prioridade`, `valor_cobrado`, `custo_total`, `data_entrada`, `data_conclusao`.
- **`servico_equipamentos`:** Equipamentos vinculados a uma OS.
- **`servico_problemas`:** Problemas relatados em cada equipamento.
- **`servico_pecas`:** Peças do estoque utilizadas na OS (vinculadas ao equipamento).
- **`estoque`:** Cadastro de produtos/peças. Campos: `nome`, `sku`, `quantidade`, `quantidade_minima`, `preco_custo`, `preco_venda`.
- **`pedidos`:** Pedidos de reposição de estoque.
- **`servico_status`:** Log de histórico de mudanças de status (Timeline).

### Relacionamentos
- `Servico` belongsTo `Cliente`
- `Servico` hasMany `ServicoEquipamento`
- `ServicoEquipamento` hasMany `ServicoProblema` e `ServicoPeca`
- `Servico` belongsToMany `User` (técnicos da OS via `servico_users`)
- `User` belongsTo `User` (hierarquia de supervisor)

## 5. FUNCIONALIDADES IMPLEMENTADAS

- **Dashboard Inteligente:** Gráficos de faturamento, alerta de estoque crítico e ranking de produtividade.
- **Fluxo de OS Complexo:** Suporte a múltiplos equipamentos em uma única OS, cada um com seus problemas e peças.
- **Timeline de Status:** Registro histórico de quem mudou o status da OS e quando.
- **Criação Rápida de Cliente:** No formulário de OS, é possível cadastrar um cliente via modal sem sair da página.
- **Gestão Financeira:** Cálculo automático de lucro (Valor Cobrado - Custo Total das Peças).
- **Gestão de Equipe:** Controle de usuários ativos/inativos e cargos.

## 6. FLUXOS PRINCIPAIS

1. **Atendimento Inicial:**
   - Cadastro do cliente (ou seleção).
   - Abertura da OS registrando equipamentos e itens deixados.
   - Definição de técnicos responsáveis.
2. **Execução Técnica:**
   - Técnico assume a OS e altera status para "em_reparo".
   - Adiciona problemas encontrados e peças utilizadas.
   - Finaliza o serviço alterando para "pronto".
3. **Encerramento:**
   - Entrega ao cliente, alteração para status "entregue".
   - Registro automático da data de entrega para faturamento.

## 7. INTEGRAÇÕES E APIs EXTERNAS

- Não foram detectadas integrações externas (gateways de pagamento ou APIs de terceiros) na versão atual. O sistema é self-contained.

## 8. REGRAS DE NEGÓCIO

- **Faturamento:** Apenas OS com status "pronto" ou "entregue" são contabilizadas no faturamento do Dashboard e Financeiro.
- **Lucro:** Calculado pela diferença entre o `valor_cobrado` e o `custo_total` (soma do custo das peças).
- **Estoque Baixo:** O sistema gera alertas quando a `quantidade` <= `quantidade_minima`.
- **Hierarquia:** Técnicos podem ter supervisores atribuídos.
- **Segurança:** Apenas usuários autenticados e com e-mail verificado acessam o core do sistema.

## 9. PENDÊNCIAS E INCOMPLETOS

- **Financeiro:** A lógica de `porMes` no `FinanceiroController` usa a função SQL `MONTH()` e `YEAR()`, que pode variar conforme o driver do banco (MySQL vs SQLite).
- **Permissões:** O código faz uso de cargos (`cargo`), mas não há um sistema de ACL (Access Control List) ou Policies robusto implementado para restringir botões de exclusão por cargo, por exemplo.

## 10. AUTENTICAÇÃO E AUTORIZAÇÃO

- **Mecanismo:** Laravel Fortify (Session-based).
- **Proteção:** Middleware `auth` e `verified`.
- **Perfis:** `admin`, `gerente`, `supervisor`, `tecnico`.
- **Restrição de Acesso:** O `EquipeController` filtra usuários ativos para atribuição em OS.

## 11. ESTRUTURA DE PASTAS

- `app/Http/Controllers`: Lógica de requisições e processamento de dados.
- `app/Models`: Definição de entidades e relacionamentos Eloquent.
- `database/migrations`: Definição da estrutura do banco de dados.
- `resources/js/pages`: Componentes Vue que representam as páginas (Inertia).
- `resources/js/components`: Componentes de UI reutilizáveis (botões, inputs, etc).
- `routes/web.php`: Rotas principais da aplicação.

## 12. VARIÁVEIS DE AMBIENTE

- `APP_NAME`: Nome da aplicação.
- `APP_ENV`: Ambiente (local, production).
- `APP_KEY`: Chave de criptografia do Laravel.
- `DB_CONNECTION`: Driver do banco (default: sqlite).
- `DB_DATABASE`: Caminho para o arquivo .sqlite.

## 13. NOMENCLATURA E PADRÕES USADOS

- **Código PHP:** PSR-12, nomes de métodos em `camelCase`, modelos em `PascalCase`.
- **Código JS/TS:** Variáveis em `camelCase`, componentes Vue em `PascalCase`.
- **Rotas:** Padrão RESTful para resources (`index`, `create`, `store`, `edit`, `update`, `destroy`).
- **Respostas:** Uso do `Inertia::render` para páginas e `RedirectResponse` com flashes de sessão (`sucesso`, `erro`).

## 14. DEPENDÊNCIAS PRINCIPAIS

- `inertiajs/inertia-laravel`: Ponte entre Laravel e Vue.
- `laravel/fortify`: Implementação agnóstica de backend de autenticação.
- `lucide-vue-next`: Biblioteca de ícones.
- `chart.js` & `vue-chartjs`: Renderização de gráficos no Dashboard/Financeiro.
- `maska`: Máscaras de input para telefone e documentos.

## 15. PONTOS DE ENTRADA DO SISTEMA

- **Local:** `php artisan serve` e `npm run dev`.
- **Comando de Setup:** `composer run setup` (executa install, migrate, key generate e npm build).

## 16. O QUE O SISTEMA NÃO FAZ

- Não emite Nota Fiscal (NFe/NFSe).
- Não possui integração direta com WhatsApp para notificações (embora tenha campo de telefone).
- Não gerencia contas a pagar/receber externas às OS e Pedidos de Peças.

## 17. GLOSSÁRIO DO DOMÍNIO

- **OS (Ordem de Serviço):** Registro principal de um atendimento técnico.
- **Equipamento:** O item físico do cliente que receberá manutenção.
- **Peça/Item de Estoque:** Insumo utilizado para o reparo que gera custo.
- **Timeline:** Histórico cronológico de eventos de uma OS.
- **Supervisor:** Usuário responsável por revisar ou acompanhar o trabalho de um técnico.
