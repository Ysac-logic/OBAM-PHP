# OBAM — Site institucional e painel administrativo

Site em **PHP + Bootstrap 5 + MySQL** para a OBAM, organização ligada à apicultura. Reúne o site público (notícias, galeria, vídeos, mapa, parceiros, denúncias, contato), o **cadastro online de apicultores** e um **painel administrativo** para gerenciar todo o conteúdo.

## Sumário
- [Funcionalidades](#funcionalidades)
- [Tecnologias](#tecnologias)
- [Estrutura de pastas](#estrutura-de-pastas)
- [Banco de dados](#banco-de-dados)
- [Instalação](#instalação)
- [Painel administrativo](#painel-administrativo)
- [Segurança](#segurança)
- [Publicação em produção](#publicação-em-produção)

## Funcionalidades

### Site público
| Página | Arquivo | Descrição |
|---|---|---|
| Início | `index.php` | Carrossel, estatísticas, destaques, temas, últimas publicações, parceiros e newsletter. Os blocos e sua ordem são configuráveis pelo painel |
| Quem Somos | `sobre.php` | Apresentação da organização e equipe |
| Publicações | `publicacoes.php`, `post.php` | Notícias, eventos e demais categorias, com página individual |
| Galeria | `galeria.php` | Fotos com legenda |
| Vídeos | `videos.php` | Vídeos do YouTube incorporados |
| Mapa | `mapa.php` | Mapa de localização/atuação |
| Informações | `informacoes.php` | Órgãos por esfera: Municipal, Estadual e Federal |
| Literatura | `literatura.php` | Materiais e documentos para download |
| Parceiros | `parceiros.php` | Instituições parceiras |
| Denúncias | `denuncias.php` | Formulário de denúncia (aceita anonimato) |
| Elogios/Sugestões/Críticas | `feedbacks.php` | Depoimentos, com moderação antes de aparecer |
| Contato | `contato.php` | Formulário de mensagem |
| Newsletter | `newsletter.php` | Inscrição por e-mail |
| Cadastro do Apicultor | `cadastro.php` | Ficha de filiação com validação de CPF e aceite da LGPD |

### Painel administrativo (`/admin`)
- **Publicações e páginas fixas**: criar, editar, excluir, rascunho, imagem de capa e destaque na home.
- **Página inicial**: ligar/desligar e reordenar cada bloco da home.
- **Menu do site**: ordem, grupos e links.
- **Carrossel, Parceiros, Equipe, Galeria, Vídeos, Literatura e Órgãos**.
- **Configurações**: textos, números, contatos, redes sociais, mapas e texto da LGPD.
- **Cadastros de apicultores**: busca, filtros, ficha completa com controle mensal (2024–2034), ativar/inativar e **exportação em CSV**.
- **Caixa de entrada**: denúncias, mensagens, depoimentos e newsletter, também exportáveis em CSV.
- **Usuários e senha**: criação de usuários e troca da própria senha.

### Perfis de acesso
| Perfil | Permissões |
|---|---|
| **Admin** | Tudo: editar, excluir, criar usuários, moderar e exportar |
| **Responsável** | Somente visualizar conteúdo e cadastros e exportar CSV |

## Tecnologias
- PHP 8.1+ (extensões `pdo_mysql`, `mbstring`, `iconv`), sem framework
- MySQL 5.7+ / MariaDB 10.3+ via PDO
- Bootstrap 5, Bootstrap Icons, AOS e Swiper (em `assets/vendor/`)
- Apache (XAMPP/WAMP em desenvolvimento)

## Estrutura de pastas
```
obam-site/
├── index.php, sobre.php, ...   Páginas públicas
├── config.example.php          Modelo de configuração (copie para config.php)
├── schema.sql                  Criação das tabelas e dados iniciais
├── .htaccess                   Bloqueia listagem de pastas e arquivos sensíveis, cabeçalhos de segurança
├── inc/
│   ├── bootstrap.php           Conexão PDO, sessão, helpers
│   ├── layout.php              Cabeçalho e rodapé do site
│   └── home/                   Blocos da página inicial (slider, stats, destaques...)
├── admin/                      Painel administrativo (login, CRUDs, cadastros, usuários)
├── assets/                     CSS, JS e bibliotecas
└── uploads/                    Imagens e arquivos enviados (não versionado)
```

## Banco de dados
Definido em `schema.sql`. Principais tabelas:

| Tabela | Uso |
|---|---|
| `users` | Usuários do painel (perfil `admin` ou `responsavel`) |
| `settings` | Configurações do site (chave/valor) |
| `menu_items`, `home_sections` | Menu e blocos da home |
| `slides` | Carrossel |
| `posts` | Publicações e páginas fixas |
| `parceiros`, `orgaos`, `equipe` | Parceiros, órgãos por esfera e equipe |
| `galeria`, `videos`, `literatura` | Mídias e documentos |
| `feedbacks`, `denuncias`, `mensagens`, `newsletter` | Interação com os visitantes |
| `apicultores`, `controle_mensal` | Cadastro de apicultores e controle mensal por ano/mês |

## Instalação

1. **Clone o repositório** dentro do `htdocs` do XAMPP:
   ```bash
   git clone https://github.com/Ysac-logic/OBAM-PHP.git obam-site
   ```
2. **Crie o `config.php`** a partir do modelo e preencha os dados do MySQL:
   ```bash
   copy config.example.php config.php
   ```
   ```php
   define('DB_HOST', 'localhost');
   define('DB_PORT', '3306');
   define('DB_NAME', 'obam');
   define('DB_USER', 'root');
   define('DB_PASS', 'sua_senha');
   ```
3. **Crie o banco** e importe as tabelas:
   ```sql
   CREATE DATABASE obam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
   Depois importe `schema.sql` (MySQL Workbench: *Server > Data Import > Import from Self-Contained File*, ou `mysql -u root -p obam < schema.sql`).
4. Garanta que a pasta `uploads/` tenha permissão de escrita.
5. Acesse `http://localhost/obam-site/` e o painel em `http://localhost/obam-site/admin`.

> Acesso inicial ao painel: usuário `admin` e a senha informada em `LEIA-ME.md`. **Troque-a imediatamente** em *Alterar minha senha*.

## Segurança
- Senhas com hash (bcrypt) e sessão com cookie `httponly` e `SameSite`
- Proteção CSRF nos formulários
- Consultas preparadas (PDO) contra SQL injection
- Limite de tentativas de login
- Bloqueio de execução de scripts em `uploads/`
- `config.php` e `schema.sql` inacessíveis pelo navegador (`.htaccess`)
- `config.php` e `uploads/` fora do Git (`.gitignore`)
- Consentimento LGPD registrado com data e IP no cadastro de apicultores

## Publicação em produção
- Use **HTTPS**.
- Mantenha `DEBUG` como `false` no `config.php`.
- Use um usuário de banco próprio, sem privilégios de `root`, e senha forte.
- Troque a senha padrão do `admin`.
