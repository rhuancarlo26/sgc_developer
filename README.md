
Este pacote é uma implementação do [Breeze Starter Kit](https://laravel.com/docs/10.x/starter-kits#breeze-and-inertia) com Vue e [InteriaJS](https://inertiajs.com/),
onde a stack do front-end foi substituída por uma adaptação do [Tabler Dashboard](https://preview.tabler.io/) com **Bootstrap 5**.

# Novas funcionalidades
- Módulo para cadastro de perfis de acesso com permissões dinâmicas baseadas nas rotas da aplicação.
- Novos componentes Vue para Tabelas, Modais, Navegação e mais...
- Módulo para visualização de Logs da aplicação através da rota `/logs`

# Alterações em relação ao Breeze Starter kit
- Stack front-end substituída pelo **Tabler Dashboard** com **Bootstrap 5**
- Tela de boas vindas substituída pela tela de login.
- Verificação de Email obrigatória p/ novos usuários por padrão. Para reverter, basta remover `implements MustVerifyEmail` de `App\Models\User.php`, e remover a middleware `verified` das rotas em `web.php`

# Como usar
- Clone o Projeto:  `git clone URL`
- Instale as dependências do PHP e Node: `composer install && npm install`
- Crie um arquivo `.env` e ajuste suas variáveis
- Crie uma chave p/ a aplicação: `php artisan key:generate`
- Execute as migrações de bancos de dados: `php artisan migrate`
- Crie um usuário 'Super Admin' para o primeiro acesso: `php artisan create:super-admin`

# Comandos
- Criar service: `php artisan make:model-service App/Domain/Example/Services/ExampleService`;
- Criar Página Vue: `php artisan make:vue-page-component Example/ComponentName`
- Restante dos comandos do `php artisan make:` devem estar seguidos do namespace completo do arquivo. Ex: `php artisan make:controller App/Domain/Example/Controllers/ExampleController`
