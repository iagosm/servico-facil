Você é um arquiteto de software sênior. Analise este projeto completo e crie o conteúdo de um arquivo chamado `descricao.md` que deve ser salvo na raiz do projeto.

Retorne APENAS o conteúdo do arquivo em Markdown válido e bem formatado, sem explicações antes ou depois.
Seja extremamente detalhado — esse arquivo será usado para explicar o sistema a outras IAs para análise, sugestão de melhorias e desenvolvimento de novas funcionalidades.

---

# [Nome do Sistema] — Documentação Técnica Completa

## 1. VISÃO GERAL

- Propósito e público-alvo
- Tecnologias usadas (linguagem, framework, banco, libs principais)
- Arquitetura geral

## 2. MAPA DE PÁGINAS / ROTAS

Para cada página/rota:

- URL, nome, o que faz, quem acessa

## 3. FORMULÁRIOS

Para cada formulário:

- Página onde está
- Campos (nome, tipo, obrigatório, validações)
- O que acontece ao submeter

## 4. BANCO DE DADOS

Para cada tabela/coleção:

- Nome e propósito
- Campos (tipo, PK, FK, nullable)
- Relacionamentos
- Regras de negócio atreladas

## 5. FUNCIONALIDADES IMPLEMENTADAS

Liste todas, agrupadas por módulo. Seja específico.
Exemplo: "Cadastro de clientes com upload de foto e validação de CPF"

## 6. FLUXOS PRINCIPAIS

Passo a passo dos fluxos mais importantes do sistema.
Exemplo:

- Fluxo de cadastro de usuário
- Fluxo de login/autenticação
- Fluxo de [funcionalidade principal]

## 7. INTEGRAÇÕES E APIs EXTERNAS

APIs de terceiros, pagamento, e-mail, SMS, webhooks, etc.

## 8. REGRAS DE NEGÓCIO

Todas as regras encontradas no código.
Exemplo: "Usuário só pode editar registro se for o dono"

## 9. PENDÊNCIAS E INCOMPLETOS

TODOs no código, fluxos pela metade, funcionalidades incompletas.

## 10. AUTENTICAÇÃO E AUTORIZAÇÃO

- Como o login funciona (session, JWT, OAuth, etc.)
- Perfis/roles de usuário existentes
- O que cada perfil pode e não pode fazer
- Rotas protegidas vs públicas

## 11. ESTRUTURA DE PASTAS

O que cada pasta/diretório contém e sua responsabilidade.
Exemplo:

- /controllers → lógica de negócio
- /models → definição das tabelas
- /views → templates HTML

## 12. VARIÁVEIS DE AMBIENTE

Liste todas as variáveis do .env, o que cada uma configura e se são obrigatórias ou opcionais.
Não exponha valores reais.

## 13. NOMENCLATURA E PADRÕES USADOS

- Padrão de nomes de variáveis (camelCase, snake_case?)
- Padrão de rotas (REST? prefixo /api/v1?)
- Estrutura do JSON retornado pelas APIs
- Como erros são tratados e retornados

## 14. DEPENDÊNCIAS PRINCIPAIS

Para cada dependência relevante do package.json / composer.json / requirements.txt:

- Nome e para que é usada especificamente no projeto

## 15. PONTOS DE ENTRADA DO SISTEMA

- Arquivo principal que inicia a aplicação
- Como rodar localmente (comandos)
- Como rodar em produção

## 16. O QUE O SISTEMA NÃO FAZ (escopo negativo)

Liste o que claramente está fora do escopo atual.
Isso evita que IAs sugiram funcionalidades que não fazem sentido para o contexto.

## 17. GLOSSÁRIO DO DOMÍNIO

Termos específicos do seu negócio usados no sistema.
Exemplo: "Ocorrência = registro de problema aberto por um cliente"
Isso é essencial para que IAs entendam a linguagem do negócio corretamente.
