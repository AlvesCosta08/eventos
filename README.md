ADTC2 Eventos
Sistema web para gerenciamento de eventos e usuários das congregações da ADTC2, desenvolvido com Laravel 12 e MySQL 8.0.
Funcionalidades
- Autenticação de usuários (login e logout).
- Dashboard.
- Consulta, cadastro, edição e exclusão de eventos.
- Consulta, cadastro, edição e exclusão de usuários.
- Perfis previstos na aplicação: Administrador Geral, Líder de Congregação e Visualizador.
- Associação de eventos a congregações.
Nota: As rotas e os perfis estão presentes no projeto. As regras de autorização específicas de cada perfil devem ser validadas antes de colocar o sistema em produção.

Tecnologias
- PHP e Laravel 12
- MySQL 8.0
- Docker (para o banco de dados no ambiente de desenvolvimento utilizado)
- Composer
Requisitos
- PHP compatível com Laravel 12, com extensões necessárias ao projeto.
- Composer.
- MySQL 8.0 acessível (localmente ou via Docker).
- Node.js e npm, caso o projeto utilize compilação de recursos frontend.
Instalação e configuração local
Clone o repositório e entre na pasta do projeto:
git clone <URL_DO_REPOSITORIO>
cd adtc2-eventos
composer install
Crie o arquivo de ambiente e a chave da aplicação:
cp .env.example .env
php artisan key:generate
Configure a conexão MySQL no .env. No ambiente de desenvolvimento utilizado neste projeto, o MySQL em Docker é acessado pela porta 3307 do host:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=eventos
DB_USERNAME=eventos
DB_PASSWORD=<SENHA_CONFIGURADA_LOCALMENTE>
A porta, o usuário e o nome do banco podem variar em outras instalações. Não versione senhas nem o arquivo .env.

Garanta que o banco eventos exista e que o usuário configurado tenha permissões apropriadas. Depois execute as migrations:
php artisan migrate
Para uma instalação nova com tabelas vazias, execute a carga inicial:
php artisan db:seed
Atenção: o EventoSeeder utiliza Evento::create() e insere novamente os eventos a cada execução. Não repita db:seed em um banco já populado sem revisar esse comportamento. Evite comandos destrutivos como migrate:fresh em bancos com dados que devem ser preservados.

Se o projeto exigir assets compilados, instale e execute as tarefas frontend previstas no package.json:
npm install
npm run dev
Inicie o servidor de desenvolvimento:
php artisan serve
Acesse http://127.0.0.1:8000 (ou a URL indicada no terminal).
Acesso inicial
O UserSeeder cria três contas de desenvolvimento:
Perfil	E-mail
Administrador Geral	admin@adtc2.local
Líder de Congregação (SEDE)	lider.sede@adtc2.local
Visualizador	viewer@adtc2.local


As senhas iniciais estão definidas em database/seeders/UserSeeder.php e são apenas para desenvolvimento. Troque-as antes de disponibilizar a aplicação em rede ou produção e remova/desative contas de demonstração desnecessárias.

Página de login: /login.
Rotas principais
Método	Rota	Descrição
GET	/	Página inicial
GET	/login	Formulário de login
POST	/login	Autenticação
POST	/logout	Encerrar sessão
GET	/dashboard	Dashboard
GET	/eventos	Listagem de eventos
GET	/eventos/create	Formulário de novo evento
POST	/eventos	Criar evento
GET	/eventos/{evento}/edit	Formulário de edição
PUT/PATCH	/eventos/{evento}	Atualizar evento
DELETE	/eventos/{evento}	Excluir evento
GET	/users	Listagem de usuários
GET	/users/create	Formulário de novo usuário
POST	/users	Criar usuário
GET	/users/{user}/edit	Formulário de edição
PUT/PATCH	/users/{user}	Atualizar usuário
DELETE	/users/{user}	Excluir usuário


Consulte a relação completa com:
php artisan route:list
Dados iniciais
Na instalação de desenvolvimento validada, os Seeders geraram:
Entidade	Quantidade
Congregações	13
Usuários	3
Eventos	130


Os eventos iniciais incluem registros com datas entre 2024 e 2026.

Seeders disponíveis:
- CongregacaoSeeder: cadastra congregações com firstOrCreate.
- UserSeeder: cadastra contas iniciais com firstOrCreate.
- EventoSeeder: importa eventos com create (não é idempotente).
Factories disponíveis: CongregacaoFactory, UserFactory e EventoFactory. Elas servem para gerar dados de teste e não precisam ser executadas separadamente para importar os eventos iniciais.

Para consultar as quantidades atuais sem modificar os dados:
php artisan tinker --execute="dump(['congregacoes' => \\App\\Models\\Congregacao::count(), 'usuarios' => \\App\\Models\\User::count(), 'eventos' => \\App\\Models\\Evento::count()]);"
Testes
Execute os testes automatizados existentes com:
php artisan test
A existência e a cobertura dos testes devem ser verificadas no repositório.
Cuidados antes da produção
- Definir APP_ENV=production e APP_DEBUG=false.
- Configurar uma senha forte e exclusiva para o banco de dados.
- Alterar as credenciais iniciais e revisar as contas de demonstração.
- Validar autorização e restrições de acesso por perfil.
- Configurar HTTPS, backups e logs.
- Revisar a estratégia de Seeders para evitar duplicação de eventos.
Situação do projeto
A instalação local foi validada com migrations executadas, carga inicial concluída, rotas registradas e acesso administrativo funcionando.
Licença
Consulte os arquivos do repositório para identificar os termos de licença aplicáveis.
# eventos
