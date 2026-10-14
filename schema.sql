-- ============================================================
-- OBAM - Observatório Baiano de Apicultura e Meliponicultura
-- Banco MySQL (utf8mb4). Importe no MySQL Workbench:
--   Server > Data Import > Import from Self-Contained File
--   ou abra este arquivo e execute (raio). Também pode usar install.php.
-- Usuário inicial: admin / obam123 (troque após o primeiro acesso)
-- ============================================================
-- @@INSTALL_START
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS controle_mensal, apicultores, newsletter, mensagens, denuncias, feedbacks, equipe, literatura, galeria, videos, orgaos, parceiros, posts, slides, home_sections, menu_items, settings, users;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50) NOT NULL UNIQUE, nome VARCHAR(120) NOT NULL, senha_hash VARCHAR(255) NOT NULL, papel ENUM('admin','responsavel') NOT NULL DEFAULT 'responsavel', ativo TINYINT(1) NOT NULL DEFAULT 1, ultimo_login DATETIME NULL, criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE settings (k VARCHAR(80) PRIMARY KEY, v TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE menu_items (id INT AUTO_INCREMENT PRIMARY KEY, parent_id INT NULL, label VARCHAR(120) NOT NULL, url VARCHAR(255) NOT NULL DEFAULT '#', ordem INT NOT NULL DEFAULT 0, visivel TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE home_sections (id INT AUTO_INCREMENT PRIMARY KEY, chave VARCHAR(40) NOT NULL UNIQUE, titulo VARCHAR(120) NOT NULL, ativo TINYINT(1) NOT NULL DEFAULT 1, ordem INT NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE slides (id INT AUTO_INCREMENT PRIMARY KEY, tag VARCHAR(60), titulo VARCHAR(200) NOT NULL, texto VARCHAR(400), botao VARCHAR(60), url VARCHAR(255), imagem VARCHAR(255), ordem INT NOT NULL DEFAULT 0, ativo TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE posts (id INT AUTO_INCREMENT PRIMARY KEY, tipo ENUM('post','page') NOT NULL DEFAULT 'post', titulo VARCHAR(255) NOT NULL, slug VARCHAR(190) NOT NULL UNIQUE, categoria VARCHAR(80), resumo VARCHAR(400), conteudo MEDIUMTEXT, imagem VARCHAR(255), autor VARCHAR(120), publicado_em DATE, status ENUM('publicado','rascunho') NOT NULL DEFAULT 'publicado', destaque TINYINT(1) NOT NULL DEFAULT 0, lista_home TINYINT(1) NOT NULL DEFAULT 0, ordem INT NOT NULL DEFAULT 0, visualizacoes INT NOT NULL DEFAULT 0, criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, KEY (tipo,status), KEY (categoria)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE parceiros (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(200) NOT NULL, sigla VARCHAR(40), descricao VARCHAR(500), funcao VARCHAR(80), url VARCHAR(255), icone VARCHAR(60) DEFAULT 'bi-people-fill', ordem INT NOT NULL DEFAULT 0, ativo TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE orgaos (id INT AUTO_INCREMENT PRIMARY KEY, esfera ENUM('Municipal','Estadual','Federal') NOT NULL, nome VARCHAR(255) NOT NULL, sede VARCHAR(255), link VARCHAR(255), ordem INT NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE videos (id INT AUTO_INCREMENT PRIMARY KEY, titulo VARCHAR(200) NOT NULL, youtube_url VARCHAR(255) NOT NULL, ordem INT NOT NULL DEFAULT 0, ativo TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE galeria (id INT AUTO_INCREMENT PRIMARY KEY, imagem VARCHAR(255) NOT NULL, legenda VARCHAR(200), ordem INT NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE literatura (id INT AUTO_INCREMENT PRIMARY KEY, titulo VARCHAR(200) NOT NULL, descricao VARCHAR(500), arquivo VARCHAR(255), url VARCHAR(255), ordem INT NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE equipe (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120) NOT NULL, cargo VARCHAR(120), foto VARCHAR(255), ordem INT NOT NULL DEFAULT 0) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE feedbacks (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120) NOT NULL, perfil VARCHAR(80), tipo ENUM('Elogio','Sugestão','Crítica') NOT NULL DEFAULT 'Elogio', nota TINYINT NOT NULL DEFAULT 5, mensagem TEXT NOT NULL, status ENUM('pendente','aprovado') NOT NULL DEFAULT 'pendente', criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE denuncias (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120), municipio VARCHAR(120), tipo VARCHAR(80), data_ocorrencia DATE NULL, descricao TEXT NOT NULL, contato VARCHAR(160), status ENUM('nova','em_analise','resolvida') NOT NULL DEFAULT 'nova', criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE mensagens (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(120) NOT NULL, email VARCHAR(160) NOT NULL, assunto VARCHAR(200), mensagem TEXT NOT NULL, lida TINYINT(1) NOT NULL DEFAULT 0, criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE newsletter (id INT AUTO_INCREMENT PRIMARY KEY, email VARCHAR(160) NOT NULL UNIQUE, criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE apicultores (id INT AUTO_INCREMENT PRIMARY KEY, nome VARCHAR(160) NOT NULL, cpf CHAR(11) NOT NULL UNIQUE, endereco VARCHAR(255) NOT NULL, localidade VARCHAR(160) NOT NULL, telefone VARCHAR(30) NOT NULL, nucleo VARCHAR(120), data_filiacao DATE NULL, assinatura VARCHAR(160), consentimento TINYINT(1) NOT NULL DEFAULT 0, consentimento_em DATETIME NULL, ip VARCHAR(45), status ENUM('pendente','ativo','inativo') NOT NULL DEFAULT 'pendente', obs TEXT, criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, atualizado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, KEY (status), KEY (nucleo), KEY (localidade)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE controle_mensal (apicultor_id INT NOT NULL, ano SMALLINT NOT NULL, mes TINYINT NOT NULL, valor VARCHAR(60) NOT NULL, PRIMARY KEY (apicultor_id,ano,mes), CONSTRAINT fk_cm_apicultor FOREIGN KEY (apicultor_id) REFERENCES apicultores(id) ON DELETE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO users (username,nome,senha_hash,papel) VALUES ('admin','Administrador','$2y$10$lCilS.R1eymQqo729aiM4OZuPvYa71OZWsgtJB.YCxo76vlCGmz8i','admin');
INSERT INTO settings (k,v) VALUES ('site_nome','OBAM');
INSERT INTO settings (k,v) VALUES ('site_subtitulo','Observatório Baiano de Apicultura e Meliponicultura');
INSERT INTO settings (k,v) VALUES ('site_org','Instituto Federal da Bahia — Euclides da Cunha');
INSERT INTO settings (k,v) VALUES ('email','contato@obam.ifba.edu.br');
INSERT INTO settings (k,v) VALUES ('telefone','');
INSERT INTO settings (k,v) VALUES ('endereco','IFBA — Campus Euclides da Cunha, Bahia');
INSERT INTO settings (k,v) VALUES ('instagram','https://www.instagram.com/observatorio.baiano.apic.melip');
INSERT INTO settings (k,v) VALUES ('instagram_nome','@observatorio.baiano.apic.melip');
INSERT INTO settings (k,v) VALUES ('youtube','https://www.youtube.com/@ObservatorioBaianoApícola_Meli');
INSERT INTO settings (k,v) VALUES ('facebook','https://www.facebook.com/profile.php?id=61571265248902');
INSERT INTO settings (k,v) VALUES ('logo','assets/image/logo-completa.png');
INSERT INTO settings (k,v) VALUES ('favicon','assets/img/favicon.png');
INSERT INTO settings (k,v) VALUES ('stat1_num','54');
INSERT INTO settings (k,v) VALUES ('stat1_label','Apicultores na ADAB (EuC)');
INSERT INTO settings (k,v) VALUES ('stat2_num','7.407');
INSERT INTO settings (k,v) VALUES ('stat2_label','Colmeias');
INSERT INTO settings (k,v) VALUES ('stat3_num','15');
INSERT INTO settings (k,v) VALUES ('stat3_label','Associados ASSAPEC');
INSERT INTO settings (k,v) VALUES ('stat4_num','13');
INSERT INTO settings (k,v) VALUES ('stat4_label','Parceiros');
INSERT INTO settings (k,v) VALUES ('newsletter_titulo','Fique por dentro das novidades');
INSERT INTO settings (k,v) VALUES ('newsletter_texto','Receba atualizações sobre apicultura, legislação e eventos do OBAM.');
INSERT INTO settings (k,v) VALUES ('sobre_lead','Um espaço permanente, plural e diversificado de debate e formulação de propostas para a proteção das abelhas na Bahia.');
INSERT INTO settings (k,v) VALUES ('sobre_imagem','assets/img/about-company-1.jpg');
INSERT INTO settings (k,v) VALUES ('mapa_apiarios_url','https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d248862!2d-39.01!3d-10.5!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1spt!2sbr!4v1');
INSERT INTO settings (k,v) VALUES ('mapa_contato_url','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7768.2!2d-39.01!3d-10.5!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2sEuclides+da+Cunha%2C+BA!5e0!3m2!1spt!2sbr!4v1');
INSERT INTO settings (k,v) VALUES ('denuncia_externa_url','https://forumbaianodecombateaosagrotoxicos.org/formulario-de-denuncia-mortandade-de-abelhas/');
INSERT INTO settings (k,v) VALUES ('cadastro_intro','Preencha a ficha para se cadastrar como apicultor no OBAM. Seu cadastro será analisado pela equipe antes de ser ativado.');
INSERT INTO settings (k,v) VALUES ('lgpd_texto','Declaro que li e concordo com o tratamento dos meus dados pessoais (nome, CPF, endereço, localidade, telefone e dados de filiação) pelo OBAM, com a finalidade exclusiva de cadastro e identificação de apicultores, apoio a políticas públicas e ações do Observatório, conforme a Lei nº 13.709/2018 (LGPD). Posso solicitar acesso, correção ou eliminação dos meus dados a qualquer momento pelo e-mail de contato.');
INSERT INTO settings (k,v) VALUES ('ano_inicio_controle','2024');
INSERT INTO settings (k,v) VALUES ('ano_fim_controle','2034');
INSERT INTO home_sections (chave,titulo,ativo,ordem) VALUES ('slider','Carrossel de destaques',1,1);
INSERT INTO home_sections (chave,titulo,ativo,ordem) VALUES ('stats','Números do Observatório',1,2);
INSERT INTO home_sections (chave,titulo,ativo,ordem) VALUES ('destaques','Destaques e Em Alta',1,3);
INSERT INTO home_sections (chave,titulo,ativo,ordem) VALUES ('temas','Explorar por Tema',1,4);
INSERT INTO home_sections (chave,titulo,ativo,ordem) VALUES ('ultimas','Últimas Publicações',1,5);
INSERT INTO home_sections (chave,titulo,ativo,ordem) VALUES ('parceiros','Parceiros',1,6);
INSERT INTO home_sections (chave,titulo,ativo,ordem) VALUES ('newsletter','Newsletter',1,7);
INSERT INTO slides (tag,titulo,texto,botao,url,imagem,ordem) VALUES ('Cadastro','Cadastro do Apicultor','Faça sua ficha de cadastro no OBAM e fortaleça a rede de apicultores da região.','Cadastrar','cadastro.php','assets/image/pi_t6.html-txt.3.jpg',1);
INSERT INTO slides (tag,titulo,texto,botao,url,imagem,ordem) VALUES ('Legislação','Informações e Regulamentações Apícolas','Tudo sobre normas municipais, estaduais e federais que regem a apicultura na Bahia.','Saiba mais','post.php?slug=legislacao','assets/image/pi_t6.html-txt.3.jpg',2);
INSERT INTO slides (tag,titulo,texto,botao,url,imagem,ordem) VALUES ('Alerta','Mortandade das Abelhas','Entenda os fatores que ameaçam as abelhas e o que podemos fazer para protegê-las.','Saiba mais','post.php?slug=mortalidade','assets/image/bee-dead.jpg',3);
INSERT INTO slides (tag,titulo,texto,botao,url,imagem,ordem) VALUES ('Meliponicultura','Meliponicultura e Polinização','O papel fundamental das abelhas sem ferrão na biodiversidade do Brasil.','Saiba mais','post.php?slug=melipoli','assets/image/foto22.jpeg',4);
INSERT INTO slides (tag,titulo,texto,botao,url,imagem,ordem) VALUES ('Participe','Faça uma Denúncia','Ajude a proteger as abelhas denunciando mortandade ou uso indevido de agrotóxicos.','Denunciar','denuncias.php','assets/image/pi_t6.html-txt.5.avif',5);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','A Importância das Abelhas para o Mundo','abelhas','Abelhas','','<p>As abelhas são muito mais do que produtoras de mel. Elas são <strong>agentes fundamentais de polinização</strong>, responsáveis pela reprodução de cerca de 70% das plantas com flores do planeta, incluindo a maioria das culturas agrícolas que alimentam a humanidade.</p>
     <h3>Por que as abelhas são tão importantes?</h3>
     <p>A polinização realizada pelas abelhas garante a produção de frutas, verduras, legumes e sementes. Sem elas, culturas como maçã, café, amêndoa, abóbora e morango praticamente não existiriam.</p>
     <blockquote>Estima-se que 1 em cada 3 colheres de comida que ingerimos depende diretamente da polinização por abelhas.</blockquote>
     <h3>Ameaças à sobrevivência das abelhas</h3>
     <p>Apesar de sua importância, as populações de abelhas vêm diminuindo drasticamente em todo o mundo, ameaçadas por:</p>
     <ul><li>Uso indiscriminado de agrotóxicos</li><li>Destruição e fragmentação de habitats</li><li>Mudanças climáticas</li><li>Doenças e parasitas como o <em>Varroa destructor</em></li><li>Introdução de espécies invasoras</li></ul>
     <p>O monitoramento e a proteção das abelhas são, portanto, uma questão de segurança alimentar e ambiental global. O OBAM atua nessa frente, coletando dados, conscientizando a população e pressionando por políticas públicas eficazes.</p>','assets/image/pi_t1.html-txt.1.avif','Equipe OBAM','2025-08-31','publicado',1,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','Ciclo de Vida das Abelhas e Cuidados de Saúde','vidasaude','Saúde','','<p>Compreender o ciclo de vida das abelhas é essencial para qualquer apicultor e para quem deseja proteger esses fascinantes insetos. A colmeia é uma sociedade complexa, organizada em castas com papéis bem definidos.</p>
     <h3>As castas da colmeia</h3>
     <p><strong>A rainha</strong> é a única fêmea reprodutiva da colônia. Pode viver de 3 a 5 anos e postura até 2.000 ovos por dia na temporada de maior produção. <strong>As operárias</strong> são fêmeas inférteis que realizam todas as tarefas da colmeia — coleta de néctar, produção de mel, cuidado com as larvas e defesa. <strong>Os zangões</strong> são os machos, cuja única função é fecundar uma nova rainha.</p>
     <h3>Desenvolvimento</h3>
     <p>Uma abelha operária passa por quatro estágios: ovo (3 dias), larva (6 dias), pupa (12 dias) e adulto. Cada fase requer cuidados específicos de temperatura e alimentação por parte das operárias.</p>
     <h3>Doenças comuns e prevenção</h3>
     <ul><li><strong>Varroa:</strong> ácaro parasita que se alimenta de larvas e adultos. Monitoramento regular e tratamento são essenciais.</li><li><strong>Loque americana e europeia:</strong> doenças bacterianas das crias, controladas com manejo sanitário rigoroso.</li><li><strong>Nosemose:</strong> doença fúngica que afeta o sistema digestivo das adultas.</li></ul>
     <p>A saúde da colmeia depende diretamente do manejo cuidadoso do apicultor, de inspeções regulares e do fornecimento de ambiente limpo e nutritivo.</p>','assets/image/pi_t2.html-txt.1.avif','Equipe OBAM','2023-10-31','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','A Mortalidade das Abelhas','mortalidade','Mortandade','','<p>A mortalidade das abelhas tornou-se um dos maiores problemas ambientais do século XXI. Em todo o mundo, apicultores relatam perdas crescentes de colônias — um fenômeno conhecido como <strong>Desordem do Colapso das Colônias (DCC)</strong>.</p>
     <h3>Causas principais</h3>
     <p>As causas da mortalidade massiva de abelhas são multifatoriais:</p>
     <ul>
       <li><strong>Agrotóxicos:</strong> especialmente neonicotinoides, que afetam o sistema nervoso das abelhas mesmo em doses sub-letais, comprometendo a navegação, memória e reprodução.</li>
       <li><strong>Perda de habitat:</strong> a expansão agrícola e urbana elimina áreas de forrageamento e nidificação.</li>
       <li><strong>Monoculturas:</strong> reduzem a diversidade floral, comprometendo a nutrição das colônias.</li>
       <li><strong>Patógenos:</strong> vírus, fungos e bactérias aproveitam colônias enfraquecidas.</li>
       <li><strong>Mudanças climáticas:</strong> alteram a disponibilidade de flores e a fenologia das plantas.</li>
     </ul>
     <h3>O papel do OBAM</h3>
     <p>O Observatório Baiano de Apicultura e Meliponicultura monitora eventos de mortalidade em toda a Bahia, coletando dados que subsidiam ações do Ministério Público e de órgãos de fiscalização. Se você observar mortalidade incomum, faça uma <a href="denuncias.php">denúncia aqui</a>.</p>
     <blockquote>A morte de abelhas em escala não é apenas um problema apícola — é um alarme ecológico para toda a sociedade.</blockquote>','assets/image/pi_t1.html-txt.3.avif','Equipe OBAM','2025-08-31','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','Iniciando na Apicultura: Um Guia para Iniciantes','iniciar','Apicultura','','<p>A apicultura é uma atividade gratificante que combina contato com a natureza, produção de alimentos saudáveis e contribuição para o meio ambiente. Para começar com segurança e eficácia, siga este guia básico.</p>
     <h3>1. Formação e capacitação</h3>
     <p>Antes de instalar sua primeira colmeia, busque um curso de apicultura. O SEBRAE, o SENAI e instituições como o IFBA oferecem treinamentos para iniciantes. O conhecimento sobre o comportamento das abelhas é fundamental para um manejo seguro.</p>
     <h3>2. Equipamentos essenciais</h3>
     <ul><li>Vestimenta completa: máscara, macacão e luvas</li><li>Fumigador (fumigador apícola)</li><li>Formão apícola</li><li>Colmeia padrão Langstroth ou Schetty</li><li>Alimentador</li></ul>
     <h3>3. Escolha do local</h3>
     <p>O apiário deve ser instalado em local com abundância de flores, longe de áreas com uso intensivo de agrotóxicos, com acesso a água limpa e protegido de ventos fortes e umidade excessiva.</p>
     <h3>4. Legislação</h3>
     <p>Em todo o Brasil, apiários devem ser registrados no sistema estadual de defesa agropecuária. Na Bahia, o órgão responsável é a ADAB. O cadastro é gratuito e obrigatório. Saiba mais em <a href="post.php?slug=cadastro-adab">Cadastro ADAB</a>.</p>
     <h3>5. Primeiros passos práticos</h3>
     <p>Comece com 2 a 5 colmeias. Faça inspeções regulares (a cada 15 dias), observe o comportamento das abelhas, verifique a presença da rainha, a existência de cria saudável e o nível de mel e pólen estocados.</p>','assets/image/pi_t6.html-txt.3.jpg','Equipe OBAM','2025-08-31','publicado',0,1,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','A importância da Apicultura na Polinização','apiculturaepoli','Apicultura','','<p>A relação entre apicultura e agricultura vai muito além do mel. As abelhas são os mais eficientes polinizadores do planeta e a apicultura, quando praticada corretamente, representa um serviço ecossistêmico de valor imensurável.</p>
     <h3>O serviço de polinização</h3>
     <p>Quando uma abelha visita uma flor para coletar néctar e pólen, ela carrega involuntariamente grãos de pólen de uma flor a outra, permitindo a fertilização e a formação de frutos e sementes. Esse serviço é estimado em bilhões de dólares anuais para a agricultura mundial.</p>
     <h3>Culturas que dependem das abelhas</h3>
     <ul><li>Café, cacau e castanha-do-pará</li><li>Maçã, pêssego, ameixa e frutas cítricas</li><li>Melancia, melão, abobrinha e pepino</li><li>Soja, girassol e canola</li><li>Morango e framboesa</li></ul>
     <h3>Apicultura como serviço ambiental</h3>
     <p>Apicultores que instalam colmeias próximas a áreas de cultivo podem cobrar pelo serviço de polinização, criando uma nova fonte de renda e incentivando práticas agrícolas mais sustentáveis. Nos EUA e Europa, esse mercado movimenta bilhões anualmente.</p>
     <blockquote>Estima-se que o serviço de polinização das abelhas vale 10 vezes mais do que todo o mel produzido no mundo.</blockquote>','assets/image/pi_t2.html-txt.3.jpg','Equipe OBAM','2025-08-31','publicado',0,1,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','Meliponicultura e Polinização','melipoli','Meliponicultura','','<p>A <strong>meliponicultura</strong> é a criação de abelhas sem ferrão — as meliponíneas — nativas das regiões tropicais e subtropicais do Brasil. Essas abelhas são especialmente importantes para a polinização de espécies nativas da Mata Atlântica e Caatinga.</p>
     <h3>Espécies mais comuns</h3>
     <ul><li><strong>Jataí</strong> (<em>Tetragonisca angustula</em>): dócil, produtora de mel fino</li><li><strong>Mandaçaia</strong> (<em>Melipona quadrifasciata</em>): mel apreciado na medicina popular</li><li><strong>Uruçu</strong> (<em>Melipona scutellaris</em>): espécie endêmica da Bahia</li><li><strong>Tiúba</strong> (<em>Melipona compressipes</em>): muito criada no Nordeste</li></ul>
     <h3>Importância ecológica</h3>
     <p>As abelhas sem ferrão são polinizadoras exclusivas de algumas espécies vegetais nativas. Sua criação racional ajuda a conservar populações silvestres e a restaurar ecossistemas degradados.</p>
     <h3>Aspectos legais</h3>
     <p>No Brasil, a criação de meliponíneas é regulamentada pelo IBAMA e pelos órgãos estaduais de meio ambiente. Na Bahia, é necessário obter autorização e registrar os meliponários. O mel de abelhas sem ferrão tem valor de mercado muito superior ao mel convencional.</p>','assets/image/pi_t3.html-txt.1.avif','Equipe OBAM','2025-08-31','publicado',0,1,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','Iniciando na Meliponicultura','iniciarmelipo','Meliponicultura','','<p>A meliponicultura — criação de abelhas sem ferrão — é uma atividade acessível, sustentável e com crescente mercado. Diferente da apicultura convencional, não requer equipamentos de proteção pesada e pode ser praticada em quintais urbanos e rurais.</p>
     <h3>Vantagens das abelhas sem ferrão</h3>
     <ul><li>Não ferroam (apenas mordem levemente quando perturbadas)</li><li>Podem ser criadas em espaços menores</li><li>O mel tem alto valor de mercado</li><li>Contribuem para a biodiversidade local</li><li>São criadas em ninhos de madeira compactos</li></ul>
     <h3>Primeiros passos</h3>
     <p>Comece identificando a espécie de meliponini mais adaptada à sua região. Na Bahia, a uruçu, a jandaíra e a mandaçaia são as mais comuns. Nunca retire ninhos do ambiente natural — adquira colônias de meliponicultores registrados.</p>
     <h3>Aspectos legais</h3>
     <p>A criação de meliponíneos requer autorização do órgão ambiental estadual (INEMA na Bahia). O OBAM pode orientar sobre os procedimentos de regularização. Regularizar é simples e garante a legalidade do seu negócio.</p>
     <h3>Mercado e comercialização</h3>
     <p>O mel de abelhas sem ferrão pode ser vendido de 5 a 20 vezes mais caro que o mel comum, dependendo da espécie e da qualidade. Cosméticos, própolis e pólen também têm mercado crescente.</p>','assets/image/pi_t5.html-txt.1.avif','Equipe OBAM','2025-08-31','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','A História da Apicultura no Mundo, Brasil e EuC','historico-apicultura','História','','<p>A apicultura é uma das atividades humanas mais antigas registradas. Pinturas rupestres de 8.000 a.C. já mostram humanos coletando mel de ninhos silvestres. Ao longo dos milênios, a relação com as abelhas evoluiu da simples coleta para o manejo racional de colônias.</p>
     <h3>História mundial</h3>
     <p>No Egito Antigo, as abelhas eram símbolo real e o mel era usado em rituais e como alimento dos faraós. Os gregos e romanos desenvolveram técnicas de manejo com colmeias de barro e vime. A Idade Média viu a apicultura florescer nos mosteiros europeus. No século XIX, o padre Lorenzo Langstroth revolucionou a atividade com a invenção da colmeia de quadros móveis, ainda usada hoje.</p>
     <h3>Apicultura no Brasil</h3>
     <p>As abelhas europeias chegaram ao Brasil com os colonizadores portugueses no século XVI. Em 1956, a introdução acidental da abelha africana (<em>Apis mellifera scutellata</em>) levou ao surgimento da abelha africanizada, que domina a apicultura brasileira. Esse cruzamento resultou em abelhas mais produtivas e adaptadas ao clima tropical, tornando o Brasil um dos maiores exportadores de mel do mundo.</p>
     <h3>Apicultura em Euclides da Cunha e Região</h3>
     <p>O território do Nordeste baiano possui vegetação de Caatinga rica em floradas específicas — como o angico, a catinga-de-porco e o marmeleiro — que produzem méis de características únicas. A região de Euclides da Cunha concentra dezenas de apicultores familiares, muitos organizados em associações e cooperativas que comercializam mel e derivados.</p>','assets/image/pi_t1.html-txt.5.avif','Equipe OBAM','2025-08-31','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','Legislação Apícola: Municipal, Estadual e Federal','legislacao','Legislação','','<p>A atividade apícola no Brasil é regulamentada por uma série de leis e normas que visam garantir a qualidade do mel, a sanidade dos apiários e a proteção das abelhas. Conheça as principais referências legais.</p>
     <h3>Legislação Federal</h3>
     <ul>
       <li><strong>Lei nº 12.854/2013:</strong> fomento e desenvolvimento da apicultura como instrumento de desenvolvimento econômico e social.</li>
       <li><strong>Instrução Normativa MAPA nº 3/2001:</strong> regulamenta a inspeção e fiscalização dos produtos apícolas.</li>
       <li><strong>Portaria MAPA nº 376/2016:</strong> identidade e qualidade do mel de abelha.</li>
       <li><strong>Lei nº 7.802/1989 (Lei dos Agrotóxicos):</strong> regula o uso de defensivos agrícolas, importante para proteger apiários.</li>
     </ul>
     <h3>Legislação Estadual — Bahia</h3>
     <ul>
       <li><strong>Lei Estadual nº 10.431/2006:</strong> política de meio ambiente e proteção à biodiversidade da Bahia.</li>
       <li><strong>ADAB:</strong> agência responsável pelo cadastro e fiscalização dos apiários no estado.</li>
     </ul>
     <h3>Cadastro obrigatório</h3>
     <p>Todo apicultor baiano deve registrar seus apiários na ADAB. O cadastro é gratuito, obrigatório e necessário para comercializar o mel legalmente. Saiba mais em <a href="post.php?slug=cadastro-adab">Como se cadastrar na ADAB</a>.</p>
     <blockquote>A regularização do seu apiário protege você, seu produto e as abelhas. Não deixe para depois!</blockquote>','assets/image/legislation.png','Equipe OBAM','2025-08-31','publicado',0,1,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','Como se Cadastrar na ADAB','cadastro-adab','Legislação','','<p>O cadastro na <strong>ADAB (Agência Estadual de Defesa Agropecuária da Bahia)</strong> é obrigatório para todos os apicultores e meliponicultores do estado. O processo é gratuito e pode ser feito presencialmente ou online.</p>
     <h3>Por que se cadastrar?</h3>
     <ul><li>É exigência legal para comercializar mel e derivados</li><li>Permite participar de programas estaduais de fomento</li><li>Facilita o acesso a crédito rural</li><li>Contribui para o monitoramento sanitário dos apiários</li></ul>
     <h3>Documentos necessários</h3>
     <ul><li>RG e CPF do responsável</li><li>Comprovante de endereço</li><li>Documento do imóvel onde se localiza o apiário (escritura, contrato ou declaração)</li><li>Número de colmeias (aproximado)</li></ul>
     <h3>Passo a passo</h3>
     <ol><li>Acesse o site da ADAB (www.adab.ba.gov.br)</li><li>Clique em "Defesa Animal" e depois em "Registro de Estabelecimentos"</li><li>Preencha o formulário com os dados do apiário</li><li>Aguarde a vistoria do técnico da ADAB</li><li>Após aprovação, receba seu número de registro</li></ol>
     <p>Para dúvidas, entre em <a href="contato.php">contato com o OBAM</a> — nossa equipe pode orientar você durante o processo de regularização.</p>','assets/image/legislation.png','Equipe OBAM','2025-08-31','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('page','Quem Somos','quem-somos','Institucional','','<p class="lead">Um espaço permanente, plural e diversificado de debate e formulação de propostas para a proteção das abelhas na Bahia.</p>
<p>O <strong>OBAM</strong> nasce da proposta do Observatório Apícola de Euclides da Cunha, voltado inicialmente à apicultura e à meliponicultura, com flexibilidade para atuar em outras áreas ambientais e sociais. Reúne dados confiáveis sobre a cadeia do mel na região, apoia a profissionalização do setor e fortalece a integração entre poder público, organizações civis, pesquisadores e produtores.</p>
<h3>Justificativa</h3>
<p>Há pouca informação sistematizada sobre abelhas, sua mortandade e a cadeia do mel na região, e a articulação entre poder público, sociedade civil e iniciativa privada ainda é frágil. O Observatório surge para levantar, processar e disponibilizar dados que apoiem projetos, investimentos e decisões de gestão.</p>
<h3>Missão</h3>
<p>Ser um instrumento de planejamento que facilita a gestão e amplia o debate sobre o desenvolvimento regional sustentável, com informações técnicas, descentralização participativa e autonomia.</p>
<h3>Objetivos</h3>
<ul><li>Promover o desenvolvimento regional sustentável, facilitando a gestão pública e privada e o planejamento.</li><li>Desenvolver uma estrutura articulada e descentralizada.</li><li>Aproximar instituições e pesquisadores interessados nos temas do Observatório.</li><li>Manter um site atualizado e uma newsletter para as partes interessadas.</li><li>Estimular a produção e a divulgação do conhecimento existente.</li><li>Realizar o cadastro georreferenciado dos criadores de abelhas da região.</li><li>Apoiar a capacitação de apicultores e agricultores em boas práticas e convivência harmônica.</li></ul>
<h3>Estrutura e organograma</h3>
<p>A estrutura é operacional (escritório, centro de documentação e rede de informações) e técnica, com um Comitê Gestor de representação igualitária entre os setores interessados. O organograma é composto por um(a) <strong>Coordenador(a)</strong> e três comissões:</p>
<ul><li>Comissão de Comunicação</li><li>Comissão de Apicultura e Meliponicultura</li><li>Comissão de Educação e Meio Ambiente</li></ul>
<h3>Linhas de pesquisa</h3>
<p>Desenvolvimento sustentável, turismo, ambiente e sociedade.</p>
<h3>Plano de atividades</h3>
<div class="table-responsive"><table class="table table-bordered align-middle"><thead><tr><th>Atividade</th><th>Quando</th><th>Status</th><th>Responsável</th></tr></thead><tbody>
<tr><td>Reunião com a comissão de sustentabilidade ambiental e comunidade externa</td><td>1ª semana de julho</td><td>Concluído</td><td>IFBA</td></tr>
<tr><td>Subcomissão para elaborar as normativas do Observatório</td><td>2ª semana de julho</td><td>Concluído</td><td>IFBA</td></tr>
<tr><td>Missão, princípios, objetivos e atividades</td><td>2ª semana de julho</td><td>Em andamento</td><td>IFBA</td></tr>
<tr><td>Atividade 1: Cadastro dos apicultores</td><td>3ª semana de julho</td><td>Em andamento</td><td>ASSAPEC</td></tr>
<tr><td>Atividade 2: Coleta de vídeos</td><td>3ª semana de julho</td><td>Em andamento</td><td>ASSAPEC</td></tr>
<tr><td>Atividade 3: Boletim de ocorrência</td><td>4ª semana de julho</td><td>Em andamento</td><td>ASSAPEC</td></tr>
<tr><td>Atividade 4: Teste de agrotóxicos</td><td>4ª semana de julho</td><td>Em andamento</td><td>UNEB</td></tr>
<tr><td>Atividade 5: Georreferenciamento</td><td>4ª semana de julho</td><td>Em andamento</td><td>ASSAPEC</td></tr>
<tr><td>Atividade 6: Reunião para definir bases regimentais</td><td>Agosto (data a definir)</td><td>A definir</td><td>ASSAPEC, CAR, IFBA, Promotoria, UNEB, SEBRAE, SEAGRI, Sec. Meio Ambiente</td></tr>
</tbody></table></div>','assets/img/about-company-1.jpg','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('page','Educação Socioambiental','educacao-socioambiental','Educação','','<p>A <strong>Comissão de Educação e Meio Ambiente</strong> do OBAM trabalha para levar informação sobre a importância das abelhas a escolas, produtores e comunidades da região.</p>
<h3>Temas</h3><ul><li>Importância da polinização para a alimentação e a biodiversidade</li><li>Convivência harmônica entre pessoas, lavouras e abelhas</li><li>Uso responsável de defensivos agrícolas</li><li>Preservação de habitats e da vegetação da Caatinga</li></ul>
<p>Está prevista, caso seja possível, a criação de um jogo educativo sobre o tema. Conteúdos, materiais e datas de ações serão publicados aqui.</p>','assets/image/foto11.jpeg','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('page','Boas Práticas na Apicultura e Meliponicultura','boas-praticas','Boas práticas','','<p>Recomendações gerais para quem cria abelhas com ferrão (<em>Apis mellifera</em>) e abelhas sem ferrão (meliponíneos). Para orientações oficiais, procure a ADAB, o SEBRAE ou o SENAR.</p>
<h3>Manejo e instalação</h3><ul><li>Instale o apiário/meliponário longe de áreas com pulverização intensa de agrotóxicos, com água limpa e flora abundante.</li><li>Faça inspeções regulares e registre ocorrências de mortandade.</li><li>Comece com poucas colônias e busque capacitação antes de ampliar.</li></ul>
<h3>Convivência com a agricultura</h3><ul><li>Combine com vizinhos os horários de pulverização para proteger as colmeias.</li><li>Em caso de mortandade, registre fotos e vídeos e faça uma denúncia na página de <a href="denuncias.php">Denúncias</a>.</li></ul>
<h3>Regularização</h3><ul><li>Cadastre seu apiário na ADAB (veja <a href="post.php?slug=cadastro-adab">Cadastro ADAB</a>).</li><li>Faça sua ficha no <a href="cadastro.php">Cadastro do Apicultor</a> do OBAM.</li></ul>','assets/image/man-bee2.jpg','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('page','Meliponicultura: histórico e distinção entre as criações','historico-melipona','Meliponicultura','','<p>A apicultura é a criação de abelhas do gênero <em>Apis</em> (com ferrão), como a <em>Apis mellifera</em>, para a produção de mel, própolis, geleia real, pólen e cera. A <strong>meliponicultura</strong> é a criação de abelhas sem ferrão, da tribo Meliponini, nativas da América Latina.</p>
<p>As duas atividades exigem técnicas diferentes e geram produtos diferentes: o mel de abelhas sem ferrão costuma ser mais ácido, por exemplo. As abelhas sem ferrão são polinizadoras importantes da flora nativa, incluindo a Caatinga.</p>
<p>O histórico mundial, brasileiro e regional da meliponicultura será complementado pela equipe do OBAM. Veja também a seção <a href="literatura.php">Literatura Apícola / Melípona</a>.</p>','assets/image/foto22.jpeg','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('post','Apicultura em Euclides da Cunha: números da região','dados-euclides-da-cunha','História','Cadastros, colmeias e produção de mel na região de Euclides da Cunha.','<p>Dados do projeto do Observatório Apícola de Euclides da Cunha, alinhados ao relatório do IBGE de 2022:</p>
<ul><li><strong>54</strong> apicultores cadastrados na ADAB, dos quais <strong>15</strong> também na ASSAPEC (número em atualização)</li><li><strong>7.407</strong> colmeias</li><li>Produção média de <strong>10 kg</strong> por apiário, totalizando cerca de <strong>74.070 kg</strong> de mel (aprox. 1.372 kg por apicultor)</li><li>Uma Unidade de Beneficiamento do Mel (UBM) que agrega valor à produção dos apicultores</li></ul>
<p>Esses números alimentam o cadastro georreferenciado de apiários, que ajuda a direcionar ações como o enfrentamento da mortandade de abelhas possivelmente associada ao uso de defensivos agrícolas.</p>','assets/image/man-bee.jpg','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('page','Política de Privacidade','politica-privacidade','Legal','','<p>Esta política descreve como o OBAM trata dados pessoais, em conformidade com a <strong>Lei nº 13.709/2018 (LGPD)</strong>.</p>
<h3>Dados coletados</h3><p>No <em>Cadastro do Apicultor</em>: nome, CPF, endereço, localidade, telefone, núcleo, data de filiação e assinatura digital. Em denúncias, mensagens e depoimentos: os dados que você escolher informar.</p>
<h3>Finalidade</h3><p>Cadastro e identificação de apicultores, comunicação, apoio a políticas públicas e ações do Observatório. Os dados não são usados para fins discriminatórios.</p>
<h3>Base legal</h3><p>Consentimento do titular (art. 7º, I) e, quando aplicável, cumprimento de obrigação legal ou execução de políticas públicas.</p>
<h3>Acesso e compartilhamento</h3><p>O acesso é restrito a usuários autorizados do painel administrativo. Dados pessoais não são publicados no site; apenas números agregados podem ser divulgados.</p>
<h3>Seus direitos (art. 18)</h3><p>Confirmação de tratamento, acesso, correção, anonimização, eliminação, portabilidade, informação sobre compartilhamento e revogação do consentimento. Solicite pela página de <a href="contato.php">Contato</a>.</p>','','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('page','Termos de Uso','termos-de-uso','Legal','','<p>Ao usar este site, você concorda em fornecer informações verdadeiras nos formulários e em não utilizá-lo para fins ilícitos. Os conteúdos do OBAM têm caráter informativo e técnico. O OBAM pode atualizar estes termos a qualquer momento.</p>','','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO posts (tipo,titulo,slug,categoria,resumo,conteudo,imagem,autor,publicado_em,status,destaque,lista_home,ordem) VALUES ('page','Cookies','cookies','Legal','','<p>Este site usa apenas cookies essenciais, como o de sessão do painel administrativo e o de segurança dos formulários. Não utilizamos cookies de publicidade.</p>','','Equipe OBAM','2026-10-05','publicado',0,0,0);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (1,NULL,'Principal','index.php',1,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (2,NULL,'Quem Somos','sobre.php',2,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (3,NULL,'Abelhas','#',3,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (31,3,'A importância das abelhas','post.php?slug=abelhas',1,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (32,3,'Ciclo de vida e saúde','post.php?slug=vidasaude',2,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (33,3,'Mortandade das abelhas','post.php?slug=mortalidade',3,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (34,3,'Galeria de fotos','galeria.php',4,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (35,3,'Vídeos','videos.php',5,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (4,NULL,'Apicultura','#',4,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (41,4,'Histórico mundial, Brasil e EuC','post.php?slug=historico-apicultura',1,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (42,4,'Iniciando na apicultura','post.php?slug=iniciar',2,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (43,4,'Apicultura e polinização','post.php?slug=apiculturaepoli',3,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (44,4,'Números em Euclides da Cunha','post.php?slug=dados-euclides-da-cunha',4,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (5,NULL,'Meliponicultura','#',5,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (51,5,'Histórico','post.php?slug=historico-melipona',1,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (52,5,'Iniciando na meliponicultura','post.php?slug=iniciarmelipo',2,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (53,5,'Meliponicultura e polinização','post.php?slug=melipoli',3,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (6,NULL,'Informações','#',6,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (61,6,'Municipal','informacoes.php?esfera=Municipal',1,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (62,6,'Estadual','informacoes.php?esfera=Estadual',2,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (63,6,'Federal','informacoes.php?esfera=Federal',3,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (64,6,'Cadastro ADAB','post.php?slug=cadastro-adab',4,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (65,6,'Legislação','post.php?slug=legislacao',5,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (66,6,'Literatura Apícola / Melípona','literatura.php',6,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (67,6,'Boas Práticas','post.php?slug=boas-praticas',7,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (68,6,'Educação Socioambiental','post.php?slug=educacao-socioambiental',8,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (7,NULL,'Notícias e Eventos','#',7,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (71,7,'Notícias','publicacoes.php?cat=Not%C3%ADcias',1,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (72,7,'Eventos','publicacoes.php?cat=Eventos',2,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (73,7,'Todas as publicações','publicacoes.php',3,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (8,NULL,'Participe','#',8,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (81,8,'Cadastro do Apicultor','cadastro.php',1,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (82,8,'Mapa de Apiários / Meliponários','mapa.php',2,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (83,8,'Denúncias','denuncias.php',3,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (84,8,'Elogios, sugestões e críticas','feedbacks.php',4,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (9,NULL,'Parceiros','parceiros.php',9,1);
INSERT INTO menu_items (id,parent_id,label,url,ordem,visivel) VALUES (10,NULL,'Contatos','contato.php',10,1);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Instituto Federal da Bahia — campus Euclides da Cunha','IFBA','Instituição sede do OBAM; hospeda a plataforma de dados e o site do Observatório.','Parceiro Fundador','bi-mortarboard-fill',1);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Agência de Defesa Agropecuária da Bahia','ADAB','Órgão responsável pelo cadastro e fiscalização dos apiários no Estado da Bahia.','Parceiro Institucional','bi-shield-check',2);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Associação dos Apicultores do Município de Euclides da Cunha','ASSAPEC','Responsável pelo cadastro dos apicultores, coleta de vídeos, boletim de ocorrência e georreferenciamento.','Parceiro Executor','bi-people-fill',3);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Companhia de Desenvolvimento e Ação Regional','CAR','Parceira no desenvolvimento regional e na construção das bases do Observatório.','Parceiro','bi-diagram-3-fill',4);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Cooperativa de Agricultores de Tucano','COOAPIT','Cooperativa de agricultores da região, fonte de dados sobre a produção apícola.','Rede Produtiva','bi-basket-fill',5);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Ministério Público da União','MPU','Parceiro na defesa jurídica das abelhas e no enfrentamento ao uso indevido de agrotóxicos.','Parceiro Legal','bi-bank',6);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Serviço Brasileiro de Apoio às Micro e Pequenas Empresas','SEBRAE','Apoia apicultores no desenvolvimento de negócios sustentáveis na cadeia do mel.','Apoio Técnico','bi-graph-up',7);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Secretaria Municipal de Agricultura','SEMAGRI','Secretaria municipal parceira, fonte de cadastros e dados locais.','Poder Público','bi-building',8);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Serviço Nacional de Aprendizagem Rural','SENAR','Capacitação de produtores rurais e apicultores.','Capacitação','bi-journal-check',9);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Sindicato dos Trabalhadores Rurais','','Representação dos trabalhadores rurais da região.','Parceiro','bi-people',10);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Secretaria de Agricultura, Pecuária, Meio Ambiente e Recursos Hídricos de Euclides da Cunha','','Secretaria municipal responsável pelas políticas agrícolas e ambientais do município.','Poder Público','bi-tree-fill',11);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Superintendência de Agricultura Familiar','SUAF','Apoio à agricultura familiar e à produção apícola.','Parceiro','bi-house-heart-fill',12);
INSERT INTO parceiros (nome,sigla,descricao,funcao,icone,ordem) VALUES ('Universidade do Estado da Bahia — campus XXII','UNEB','Pesquisa científica, incluindo o teste de agrotóxicos ligado à mortandade de abelhas.','Pesquisa','bi-mortarboard',13);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Municipal','Secretaria Municipal de Agricultura, Pecuária e Meio Ambiente','Praça da Bandeira, 294','https://www.euclidesdacunha.ba.gov.br/secretaria/secretaria-de-agricultura-pecuaria-meio-ambiente-e-recursos-hidricos/3',1);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Municipal','Secretaria Municipal de Agricultura e Meio Ambiente (SEMAGRI)','','',2);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Municipal','Secretaria Municipal de Desenvolvimento Rural','','',3);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Municipal','Defesa Civil Municipal','Coordenação na Prefeitura Municipal','',4);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Municipal','Vigilância Sanitária Municipal','','',5);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Estadual','Secretaria do Meio Ambiente da Bahia (SEMA)','Av. Luís Viana Filho, 600 - Centro Administrativo da Bahia, Salvador - BA','https://www.meioambiente.ba.gov.br',1);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Estadual','Instituto do Meio Ambiente e Recursos Hídricos (INEMA)','Av. Luís Viana Filho, 6ª Avenida, nº 600, CAB, Salvador - BA','https://www.inema.ba.gov.br',2);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Estadual','Secretaria de Desenvolvimento Rural (SDR)','Centro Administrativo da Bahia, 2, Salvador - BA','https://www.sdr.ba.gov.br/',3);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Estadual','Fórum Baiano de Combate aos Agrotóxicos','','https://forumbaianodecombateaosagrotoxicos.org/',4);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Estadual','ADAB - Agência de Defesa Agropecuária da Bahia','R. Isaías Ferreira Canário, 145 - Euclides da Cunha, BA, 48500-000','http://www.adab.ba.gov.br/',5);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Estadual','EMBRAPA (unidade Cruz das Almas)','R. Embrapa, s/n, Cruz das Almas - BA, 44380-000','https://www.embrapa.br/',6);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Federal','Ministério do Meio Ambiente e Mudança do Clima (MMA)','Brasília - DF (Esplanada dos Ministérios, Bloco D)','https://www.gov.br/mma/pt-br',1);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Federal','Instituto Brasileiro do Meio Ambiente e dos Recursos Naturais Renováveis (IBAMA)','Brasília - DF (Setor de Clubes Esportivos Norte, Trecho 2)','https://www.gov.br/ibama/pt-br',2);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Federal','Empresa Brasileira de Pesquisa Agropecuária (Embrapa)','Brasília - DF (Parque Estação Biológica, PqEB)','https://www.embrapa.br/',3);
INSERT INTO orgaos (esfera,nome,sede,link,ordem) VALUES ('Federal','Agência Nacional de Vigilância Sanitária (Anvisa)','Brasília - DF (Setor de Indústrias e Abastecimento, Trecho 5)','https://www.gov.br/anvisa/pt-br',4);
INSERT INTO videos (titulo,youtube_url,ordem) VALUES ('Vídeo sobre abelhas 1','https://www.youtube.com/watch?v=_G2NN7UlClY&t=471s',1);
INSERT INTO videos (titulo,youtube_url,ordem) VALUES ('Vídeo sobre abelhas 2','https://www.youtube.com/watch?v=kY21euyIC0I',2);
INSERT INTO videos (titulo,youtube_url,ordem) VALUES ('Vídeo sobre abelhas 3','https://www.youtube.com/watch?v=4n3JgfzEfo8',3);
INSERT INTO videos (titulo,youtube_url,ordem) VALUES ('Vídeo sobre abelhas 4','https://www.youtube.com/watch?v=NLWVF-McuRo',4);
INSERT INTO videos (titulo,youtube_url,ordem) VALUES ('Vídeo sobre abelhas 5','https://www.youtube.com/watch?v=kY21euyIC0I&t=15s',5);
INSERT INTO videos (titulo,youtube_url,ordem) VALUES ('Vídeo sobre abelhas 6','https://www.youtube.com/watch?v=ASqC83Y2VLY',6);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto1.jpeg','Galeria OBAM 1',1);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto2.jpeg','Galeria OBAM 2',2);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto3.jpeg','Galeria OBAM 3',3);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto4.jpeg','Galeria OBAM 4',4);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto5.jpeg','Galeria OBAM 5',5);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto6.jpeg','Galeria OBAM 6',6);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto7.jpeg','Galeria OBAM 7',7);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto8.jpeg','Galeria OBAM 8',8);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto9.jpeg','Galeria OBAM 9',9);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto10.jpeg','Galeria OBAM 10',10);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto11.jpeg','Galeria OBAM 11',11);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto12.jpeg','Galeria OBAM 12',12);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto13.jpeg','Galeria OBAM 13',13);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto14.jpeg','Galeria OBAM 14',14);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto15.jpeg','Galeria OBAM 15',15);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto16.jpeg','Galeria OBAM 16',16);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto17.jpeg','Galeria OBAM 17',17);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto18.jpeg','Galeria OBAM 18',18);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto19.jpeg','Galeria OBAM 19',19);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto20.jpeg','Galeria OBAM 20',20);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto21.jpeg','Galeria OBAM 21',21);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto22.jpeg','Galeria OBAM 22',22);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto23.jpeg','Galeria OBAM 23',23);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto24.jpeg','Galeria OBAM 24',24);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto25.jpeg','Galeria OBAM 25',25);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto26.jpeg','Galeria OBAM 26',26);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto27.jpeg','Galeria OBAM 27',27);
INSERT INTO galeria (imagem,legenda,ordem) VALUES ('assets/image/foto28.jpeg','Galeria OBAM 28',28);
INSERT INTO literatura (titulo,descricao,arquivo,ordem) VALUES ('Manual de Meliponicultura','Manual técnico sobre criação de abelhas sem ferrão (Banco Mundial).','uploads/literatura/manual-meliponicultura.pdf',1);
INSERT INTO literatura (titulo,descricao,arquivo,ordem) VALUES ('Abelha sem ferrão','Publicação sobre abelhas sem ferrão (meliponíneos).','uploads/literatura/abelha-sem-ferrao.pdf',2);
INSERT INTO literatura (titulo,descricao,arquivo,ordem) VALUES ('Observatório Apícola de Euclides da Cunha','Projeto de criação do Observatório: justificativa, dados regionais, parcerias e plano de atividades.','uploads/literatura/observatorio-apicola.pdf',3);
INSERT INTO equipe (nome,cargo,foto,ordem) VALUES ('Ysac Moura','Equipe técnica','assets/img/team/team-1.jpg',1);
INSERT INTO equipe (nome,cargo,foto,ordem) VALUES ('Leonardo Melo','Equipe técnica','assets/img/team/team-2.jpg',2);
INSERT INTO equipe (nome,cargo,foto,ordem) VALUES ('Reuber Silva','Idealizador do projeto','assets/img/team/team-3.jpg',3);
INSERT INTO feedbacks (nome,perfil,tipo,nota,mensagem,status) VALUES ('Carlos M.','Apicultor','Elogio',5,'O OBAM foi fundamental para organizar a nossa categoria. Graças ao trabalho deles, conseguimos acionar o MP quando apiários da nossa região foram afetados por agrotóxicos.','aprovado');
INSERT INTO feedbacks (nome,perfil,tipo,nota,mensagem,status) VALUES ('Ana P.','Pesquisadora UFBA','Elogio',5,'Uma iniciativa indispensável. Os dados coletados pelo observatório têm alimentado pesquisas importantíssimas sobre polinização na Bahia.','aprovado');
INSERT INTO feedbacks (nome,perfil,tipo,nota,mensagem,status) VALUES ('João S.','Produtor Rural','Elogio',5,'Aprendi muito sobre meliponicultura através dos conteúdos do OBAM. Hoje mantenho colônias de abelhas nativas na minha propriedade.','aprovado');
INSERT INTO feedbacks (nome,perfil,tipo,nota,mensagem,status) VALUES ('Maria L.','Professora','Elogio',5,'Uso os materiais do OBAM nas minhas aulas de biologia. Os alunos adoram e ficam muito motivados a proteger as abelhas.','aprovado');
INSERT INTO feedbacks (nome,perfil,tipo,nota,mensagem,status) VALUES ('Pedro A.','Técnico Agrícola','Elogio',5,'O mapa de apiários é uma ferramenta incrível. Já salvou vidas de abelhas ao alertar apicultores sobre aplicações de defensivos na região.','aprovado');
INSERT INTO feedbacks (nome,perfil,tipo,nota,mensagem,status) VALUES ('Lucia R.','Ativista Ambiental','Elogio',5,'O OBAM é uma referência em advocacia ambiental. Trabalho sério, baseado em ciência e comprometido com a causa.','aprovado');
