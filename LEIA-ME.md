# OBAM — Site em PHP + Bootstrap + MySQL

## Requisitos
PHP 8.1+ (extensões `pdo_mysql`, `mbstring`, `iconv`), MySQL 5.7+/MariaDB 10.3+, Apache (mod_rewrite não é necessário). Pasta `uploads/` com permissão de escrita.

## Instalação
1. Copie a pasta para o servidor (ou `htdocs` do XAMPP/WAMP).
2. Abra `config.php` e preencha **DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS** (os mesmos da conexão do MySQL Workbench).
3. Crie as tabelas, de UMA destas formas:
   - Acesse `http://seusite/install.php` e clique em **Instalar**; ou
   - No Workbench: *Server > Data Import > Import from Self-Contained File* → `schema.sql`.
4. **Apague `install.php`** do servidor.
5. Acesse `/admin` → usuário `admin` · senha `obam123`. Troque a senha em *Alterar minha senha*.

## Perfis
| Perfil | Pode |
|---|---|
| **ADMIN** | Tudo: editar, excluir, criar usuários, moderar, exportar |
| **Responsável** | Ver conteúdo do site e cadastros e exportar .CSV. Não edita nem exclui |

Usuários são criados em **Painel > Usuários**.

## Painel
- **Publicações / Páginas fixas**: criar, editar, apagar, rascunho, imagem de capa, destaque na home.
- **Página inicial (ordem)**: sobe/desce e liga/desliga cada bloco da home. **Menu do site**: ordem, grupos e links.
- **Carrossel, Parceiros, Equipe, Galeria, Vídeos, Literatura, Órgãos por esfera, Configurações** (textos, números, contatos, redes, mapas, texto LGPD).
- **LISTA — Cadastros**: busca, filtros, ficha completa com tabela mês/ano (2024–2034), ativar/inativar e **Exportar .CSV** (simples ou com controle mensal). Denúncias, mensagens, depoimentos e newsletter também exportam.

## Páginas públicas
Início, Quem Somos, publicações/categorias (Notícias, Eventos...), Galeria, Vídeos, Mapa, Informações (Municipal/Estadual/Federal), Literatura, Parceiros, Denúncias, Elogios/Sugestões/Críticas, Contato e **Cadastro do Apicultor** (`cadastro.php`, com validação de CPF e aceite LGPD).

## Segurança
Senhas com hash (bcrypt), proteção CSRF, consultas preparadas, limite de tentativas, bloqueio de execução de scripts em `uploads/`. Em produção use HTTPS e mantenha `DEBUG` como `false`.
