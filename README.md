# ServiçoFácil

> Sistema SaaS de gestão para qualquer prestador de serviço — do técnico autônomo à oficina com equipe.

<p>
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white"/>
  <img alt="Vue" src="https://img.shields.io/badge/Vue-3-42b883?style=flat&logo=vue.js&logoColor=white"/>
  <img alt="Inertia" src="https://img.shields.io/badge/Inertia.js-2-9553e9?style=flat"/>
  <img alt="MySQL" src="https://img.shields.io/badge/MySQL-8-4479A1?style=flat&logo=mysql&logoColor=white"/>
  <img alt="Status" src="https://img.shields.io/badge/status-em%20desenvolvimento-yellow?style=flat"/>
</p>

---

## O problema que resolve

A maioria dos pequenos prestadores de serviço ainda gerencia seus atendimentos por **caderno, WhatsApp ou planilha**. Isso gera:

- OS perdida ou sem registro
- Cliente reclamando de problema que "já foi arrumado antes"
- Peça usada sem dar baixa no estoque
- Técnico sem saber quanto lucrou no mês
- Equipamento devolvido sem o acessório que o cliente deixou

O ServiçoFácil resolve tudo isso em um único sistema, simples o suficiente para qualquer técnico usar sem treinamento.

---

## Para quem é

O sistema foi pensado para ser **genérico por design** — o vocabulário muda conforme o negócio, mas a estrutura é a mesma. Funciona para qualquer segmento que trabalhe com reparo, diagnóstico ou manutenção:

| Segmento | O que gerencia |
|---|---|
| Assistência técnica de celular | Tela quebrada, bateria, conector |
| Informática e notebooks | Formatação, troca de HD, vírus |
| TV e eletrônicos | Placa, fonte, capacitor |
| Oficina mecânica | Revisão, freio, suspensão |
| Ar-condicionado | Limpeza, gás, instalação |
| Eletrodomésticos | Máquina de lavar, geladeira, fogão |
| Marcenaria | Móvel danificado, reforma, ajuste |
| Costura e alfaiataria | Conserto, ajuste, customização |
| Chaveiro | Cópia, conserto de fechadura, cofre |
| Qualquer outro serviço de reparo | — |

Não importa o nicho — se o seu negócio recebe um item com problema, conserta e devolve, esse sistema é para você.

---

## Funcionalidades

### Ordem de Serviço

O coração do sistema. Cada OS registra tudo que acontece com o item do cliente desde a entrada até a entrega.

**Campos da OS:**
- Cliente vinculado (com botão de cadastro rápido caso não exista)
- Tipo de serviço: diagnóstico, reparo ou orçamento
- Equipamento: tipo, marca e modelo
- Problema relatado pelo cliente
- Técnico responsável
- Prioridade: normal, urgente ou aguardando aprovação
- Valor cobrado e custo das peças usadas
- Itens deixados pelo cliente (chip, capa, carregador, controle...)
- Observações internas — o técnico anota detalhes que o cliente **não vê**
- Observações para o cliente — aparece na OS impressa

**Fluxo de status:**

```
Recebido → Diagnóstico → Aguardando peça → Em reparo → Pronto → Entregue
```

Cada mudança de status é registrada com data e hora, formando uma linha do tempo completa da OS. O dono da loja consegue ver exatamente onde está cada equipamento a qualquer momento.

---

### Clientes

- Cadastro completo: nome, telefone, email, endereço
- Histórico de todas as OS do cliente
- Quantas vezes voltou com o mesmo problema (taxa de retorno)
- Total gasto ao longo do tempo
- Busca rápida por nome ou telefone na hora de abrir uma OS

---

### Controle de Estoque

- Cadastro de peças e produtos com quantidade disponível
- **Baixa automática** quando uma peça é usada em uma OS
- Alerta visual quando o estoque está abaixo do mínimo configurado
- Diferencia peça **do estoque** (sua) da peça **do cliente** (deixada por ele)
- Histórico de uso por peça

**Exemplo real:**
> Técnico troca tela de iPhone 13 → sistema desconta 1 unidade do estoque automaticamente → se o estoque chegar a zero, aparece alerta de reposição

---

### Pedidos de Compra

- Lista de peças que precisam ser repostas
- Gerado automaticamente quando o estoque atinge o mínimo
- Técnico pode adicionar itens manualmente
- Controle do que foi pedido, o que chegou e o que está pendente

---

### Dashboard e Métricas

Tela gerencial para o dono do negócio acompanhar a saúde da operação em tempo real.

**Métricas disponíveis:**
- Faturamento do dia, semana e mês
- Número de OS abertas, em andamento e finalizadas
- Tempo médio de reparo por tipo de serviço
- Taxa de retorno — clientes que voltaram com o mesmo problema
- Lucro real por OS (valor cobrado menos custo das peças)
- Custo-hora do técnico
- Peças mais utilizadas
- Alertas de estoque baixo

---

### Impressão e Exportação

**Impressão:**
- OS formatada para entregar ao cliente com dados do equipamento, problema, valor e campo de assinatura
- Recibo de entrega
- Orçamento para aprovação do cliente

**Exportação:**
- Lista de OS em Excel (CSV)
- Relatório financeiro em PDF
- Backup completo dos dados

---

## Fluxo completo do sistema

```
Cliente chega com equipamento
        │
        ▼
Técnico abre nova OS
  ├── Busca cliente cadastrado
  │     └── Se não existe → cadastro rápido no modal
  ├── Informa equipamento e problema
  ├── Define prioridade e técnico
  └── Registra itens deixados pelo cliente
        │
        ▼
Status: Recebido
        │
        ▼
Técnico analisa → Status: Diagnóstico
  └── Anota observações internas
        │
        ▼
Precisa de peça?
  ├── Sim → Status: Aguardando peça
  │         └── Sistema verifica estoque
  │               ├── Tem peça → reserva automaticamente
  │               └── Não tem → gera pedido de compra
  └── Não → segue direto
        │
        ▼
Status: Em reparo
  └── Peça usada → baixa automática no estoque
        │
        ▼
Status: Pronto
  └── Sistema pode notificar cliente (WhatsApp — futuro)
        │
        ▼
Cliente retira → Status: Entregue
  └── OS finalizada com custo, valor e lucro registrados
        │
        ▼
Financeiro atualizado automaticamente
```

---

## Arquitetura técnica

```
servicofacil/
├── app/
│   ├── Http/
│   │   └── Controllers/        # lógica de cada módulo
│   └── Models/                 # Cliente, Servico, Estoque...
├── database/
│   └── migrations/             # estrutura do banco
├── resources/
│   └── js/
│       ├── pages/              # telas Vue (uma por rota)
│       │   ├── Dashboard.vue
│       │   ├── Clientes/
│       │   ├── Servicos/
│       │   ├── Estoque/
│       │   ├── Pedidos/
│       │   └── Financeiro/
│       ├── components/         # componentes reutilizáveis
│       │   ├── Cliente/        # ModalNovoCliente
│       │   ├── OS/             # StatusBadge, TimelineStatus
│       │   ├── Shared/         # DataTable, ConfirmDialog
│       │   └── Print/          # OrdemServicoPrint
│       └── layouts/            # AppLayout com menu lateral
└── routes/
    └── web.php                 # todas as rotas da aplicação
```

---

## Stack

| Camada | Tecnologia | Motivo |
|---|---|---|
| Backend | Laravel 13 | robusto, produtivo, mercado BR |
| Frontend | Vue 3 + Vite | reativo, rápido, fácil de manter |
| Bridge | Inertia.js | sem API REST, sem duplicar lógica |
| Autenticação | Laravel Fortify | login, 2FA, recuperação de senha |
| Tokens | Laravel Sanctum | sessão segura, pronto para API futura |
| Banco | MySQL | confiável, amplamente suportado |
| Testes | Pest | sintaxe limpa, integrado ao Laravel |
| Deploy (futuro) | Docker | ambiente padronizado, fácil de escalar |

---

## Como o Inertia.js funciona aqui

O Inertia elimina a necessidade de criar uma API REST separada. O Laravel controla as rotas e envia dados diretamente para os componentes Vue como props — sem JSON manual, sem Axios configurado, sem duplicar validações.

```php
// Controller envia dados direto para o Vue
public function index() {
    return inertia('Servicos/Index', [
        'servicos' => Servico::with('cliente')->paginate(20)
    ]);
}
```

```vue
<!-- Vue recebe como prop normal -->
<script setup>
defineProps({ servicos: Object })
</script>
```

---

## Banco de dados

| Tabela | O que armazena |
|---|---|
| `users` | técnicos e donos do sistema |
| `clientes` | dados dos clientes |
| `servicos` | ordens de serviço |
| `servico_status` | histórico de mudanças de status |
| `itens_cliente` | itens deixados (chip, capa, carregador...) |
| `estoque` | peças disponíveis |
| `servico_estoque` | peças usadas em cada OS |
| `pedidos` | peças que precisam ser compradas |

---

## Instalação

```bash
# Clone o repositório
git clone https://github.com/SEU_USUARIO/servicofacil.git
cd servicofacil

# Dependências PHP
composer install

# Dependências Node
npm install

# Ambiente
cp .env.example .env
php artisan key:generate

# Banco de dados — configure o .env antes
php artisan migrate

# Frontend
npm run build
```

**Rodando em desenvolvimento:**

```bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
```

Acesse: http://localhost:8000

---

## Licença

Projeto privado. Todos os direitos reservados.