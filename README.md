# Sistema de Gestão de Tarefas (Todo App)

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.4-4FC08D?style=flat-square&logo=vue.js&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)

## 📋 Sobre o Projeto

Sistema completo de gestão de tarefas desenvolvido com Laravel + Vue.js, oferecendo funcionalidades avançadas para gerenciamento de projetos e tarefas com sistema de permissões, autenticação robusta e documentação automática da API.

### ✨ Funcionalidades Principais

-   **Sistema de Autenticação Completo**

    -   Login/Registro com Laravel Sanctum
    -   Diferenciação entre usuários (admin/user)
    -   Controle de sessões e tokens de acesso

-   **Gestão de Tarefas**

    -   CRUD completo de tarefas
    -   Sistema de prioridades (baixa, média, alta, urgente)
    -   Status de tarefas (pendente, em progresso, concluída, cancelada)
    -   Atribuição de tarefas para usuários
    -   Data de vencimento e controle de atrasos
    -   Comentários em tarefas

-   **Gestão de Projetos**

    -   Organização de tarefas por projetos
    -   Cores personalizadas para projetos
    -   Controle de progresso automático
    -   Status de projetos (ativo, arquivado, concluído)

-   **Dashboards e Relatórios**

    -   Estatísticas detalhadas de tarefas
    -   Exportação de dados para CSV
    -   Métricas por prioridade e status
    -   Relatórios de progresso de projetos

-   **Sistema de Permissões**
    -   Políticas de autorização (TaskPolicy, ProjectPolicy)
    -   Controle de acesso baseado em função
    -   Proteção de rotas e operações

## 🚀 Tecnologias Utilizadas

### Backend

-   **PHP 8.2+** - Linguagem de programação
-   **Laravel 12.0** - Framework PHP moderno
-   **Laravel Sanctum** - Autenticação de APIs
-   **Laravel Breeze** - Scaffolding de autenticação
-   **Swagger/OpenAPI** - Documentação automática da API
-   **PHPUnit** - Testes automatizados

### Frontend

-   **Vue.js 3.4** - Framework JavaScript reativo
-   **Inertia.js** - Stack moderno SPA sem complexidade
-   **Tailwind CSS 3.2** - Framework CSS utilitário
-   **Vite 6.2** - Build tool moderno
-   **Axios** - Cliente HTTP

-   **Laravel Sanctum** - Autenticação de APIs
-   **Laravel Breeze** - Scaffolding de autenticação
-   **Swagger/OpenAPI** - Documentação automática da API

### Banco de Dados

-   **MySQL/SQLite** - Suporte para múltiplos SGBDs
-   **Eloquent ORM** - ORM nativo do Laravel
-   **Migrations** - Controle de versão do banco

### Ferramentas de Desenvolvimento

-   **Laravel Pint** - Formatação de código
-   **Laravel Sail** - Ambiente Docker
-   **Composer** - Gerenciador de dependências PHP
-   **NPM/Vite** - Gerenciamento de assets frontend

## 🛠️ Instalação e Configuração

### Pré-requisitos

-   PHP 8.2 ou superior
-   Composer
-   Node.js 18+ e NPM
-   MySQL 8.0+ ou SQLite
-   Git

### 1. Clone o Repositório

```bash
git clone <url-do-repositorio>
cd todo-app
```

### 2. Instale as Dependências

#### Dependências do PHP (Backend)

```bash
composer install
```

#### Dependências do Node.js (Frontend)

```bash
npm install
```

### 3. Configuração do Ambiente

#### Copie o arquivo de configuração

```bash
cp .env.example .env
```

#### Configure as variáveis de ambiente

Edite o arquivo `.env` com suas configurações:

```env
# Configurações da Aplicação
APP_NAME="Todo App"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configurações do Banco de Dados
# Para SQLite (padrão - mais simples para desenvolvimento)
DB_CONNECTION=sqlite

# Para MySQL (produção recomendada)
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=todo_app
# DB_USERNAME=root
# DB_PASSWORD=

# Configurações de Cache e Sessão
CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_CONNECTION=sync
```

### 4. Configuração do Banco de Dados

#### Gere a chave da aplicação

```bash
php artisan key:generate
```

#### Execute as migrations

```bash
php artisan migrate
```

#### Execute os seeders (dados de exemplo)

```bash
php artisan db:seed
```

### 5. Build dos Assets Frontend

#### Para desenvolvimento

```bash
npm run dev
```

#### Para produção

```bash
npm run build
```

### 6. Inicie o Servidor

```bash
php artisan serve
```

A aplicação estará disponível em `http://localhost:8000`

## 📚 Estrutura do Projeto

```
todo-app/
├── app/
│   ├── Services/           # Services para lógica de negócio
│   │   ├── Project/       # Services relacionadas a projetos
│   │   ├── Task/          # Services relacionadas a tarefas
│   │   └── User/          # Services relacionadas a usuários
│   ├── Http/
│   │   ├── Controllers/   # Controllers da aplicação
│   │   ├── Middleware/    # Middlewares customizados
│   │   └── Requests/      # Form requests
│   ├── Models/            # Models Eloquent
│   │   ├── Project.php    # Model de projetos
│   │   ├── Task.php       # Model de tarefas
│   │   ├── User.php       # Model de usuários
│   │   └── ...
│   └── Policies/          # Políticas de autorização
├── database/
│   ├── factories/         # Factories para testes
│   ├── migrations/        # Migrations do banco
│   └── seeders/           # Seeders de dados
├── resources/
│   ├── css/              # Arquivos CSS
│   ├── js/               # Arquivos JavaScript/Vue
│   └── views/            # Views Blade
├── routes/
│   ├── api.php           # Rotas da API
│   └── web.php           # Rotas web
└── tests/                # Testes automatizados
```

## 🔑 Usuários Padrão

Após executar os seeders, os seguintes usuários estarão disponíveis:

### Administrador

-   **Email:** admin@todoapp.com
-   **Senha:** admin123
-   **Tipo:** admin

### Usuários Comuns

-   **Email:** user1@todoapp.com / **Senha:** user123
-   **Email:** user2@todoapp.com / **Senha:** user123
-   **Tipo:** user

## 📡 API Endpoints

### Autenticação

```
POST /api/auth/login       # Login
POST /api/auth/register    # Registro
POST /api/auth/logout      # Logout
GET  /api/auth/me          # Dados do usuário autenticado
```

### Tarefas

```
GET    /api/tasks              # Listar tarefas
POST   /api/tasks              # Criar tarefa
GET    /api/tasks/{id}         # Visualizar tarefa
PUT    /api/tasks/{id}         # Atualizar tarefa
DELETE /api/tasks/{id}         # Deletar tarefa
PATCH  /api/tasks/{id}/toggle  # Alternar status
GET    /api/tasks/statistics   # Estatísticas
GET    /api/tasks/export/csv   # Exportar CSV
```

### Projetos

```
GET    /api/projects           # Listar projetos
POST   /api/projects           # Criar projeto
GET    /api/projects/{id}      # Visualizar projeto
PUT    /api/projects/{id}      # Atualizar projeto
DELETE /api/projects/{id}      # Deletar projeto
GET    /api/projects/{id}/stats # Estatísticas do projeto
```

### Usuários (Admin)

```
GET    /api/users              # Listar usuários
GET    /api/users/{id}         # Visualizar usuário
PUT    /api/users/{id}         # Atualizar usuário
DELETE /api/users/{id}         # Deletar usuário
```

## 📖 Documentação da API

A documentação completa da API está disponível via Swagger:

```bash
# Gerar documentação
php artisan l5-swagger:generate

# Acessar documentação
# http://localhost:8000/api/documentation
```

## 🧪 Executando Testes

```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter TaskTest

# Executar com cobertura
php artisan test --coverage
```

## 🚢 Deploy em Produção

### 1. Configurações de Produção

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

# Configure banco de dados de produção
DB_CONNECTION=mysql
DB_HOST=seu-host
DB_DATABASE=seu-banco
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha
```

### 2. Otimizações

```bash
# Cache de configuração
php artisan config:cache

# Cache de rotas
php artisan route:cache

# Cache de views
php artisan view:cache

# Otimizar autoloader
composer install --optimize-autoloader --no-dev

# Build de produção
npm run build
```

### 3. Comandos Úteis

```bash
# Setup completo da aplicação
php artisan app:setup

# Limpar todos os caches
php artisan optimize:clear

# Recriar banco de dados
php artisan migrate:fresh --seed
```

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🔧 Comandos Personalizados

### Setup da Aplicação

```bash
# Comando personalizado para configurar a aplicação
php artisan app:setup
```

Este comando irá:

-   Criar usuários padrão (admin e usuários comuns)
-   Criar projetos de exemplo
-   Criar tarefas de demonstração

### Limpeza de Cache

```bash
# Limpar todos os tipos de cache
php artisan optimize:clear
```

## 📊 Características Técnicas

### Arquitetura

-   **Padrão MVC** - Separação clara de responsabilidades
-   **Actions Pattern** - Lógica de negócio isolada em classes dedicadas
-   **Repository Pattern** - Abstração da camada de dados
-   **Policy-based Authorization** - Autorização baseada em políticas

### Segurança

-   **CSRF Protection** - Proteção contra ataques CSRF
-   **SQL Injection Prevention** - Proteção via Eloquent ORM
-   **XSS Protection** - Sanitização automática de dados
-   **Rate Limiting** - Controle de taxa de requisições
-   **Authentication Tokens** - Tokens seguros via Sanctum

### Performance

-   **Database Indexing** - Índices otimizados para consultas
-   **Eager Loading** - Carregamento eficiente de relacionamentos
-   **Caching** - Sistema de cache configurável
-   **Asset Optimization** - Minificação e compressão de assets

## 🐛 Resolução de Problemas

### Problemas Comuns

#### Erro de permissão de diretório

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Erro na instalação de dependências

```bash
# Limpar cache do composer
composer clear-cache
composer install

# Limpar cache do npm
npm cache clean --force
npm install
```

#### Erro de conexão com banco de dados

-   Verifique as configurações no arquivo `.env`
-   Certifique-se de que o banco de dados está rodando
-   Para SQLite, certifique-se de que o arquivo `database/database.sqlite` existe

#### Assets não carregando

```bash
# Reconstruir assets
npm run build
php artisan optimize:clear
```

## 📞 Suporte

Para suporte ou dúvidas sobre o projeto:

1. Verifique a documentação acima
2. Consulte os issues existentes no repositório
3. Crie um novo issue descrevendo o problema
4. Entre em contato com a equipe de desenvolvimento

---

Desenvolvido com ❤️ usando Laravel + Vue.js

-   **MySQL/SQLite** - Suporte para múltiplos SGBDs
-   **Eloquent ORM** - ORM nativo do Laravel
-   **Migrations** - Controle de versão do banco

### Ferramentas de Desenvolvimento

-   **Laravel Pint** - Formatação de código
-   **Laravel Sail** - Ambiente Docker
-   **Composer** - Gerenciador de dependências PHP
-   **NPM/Vite** - Gerenciamento de assets frontend

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
