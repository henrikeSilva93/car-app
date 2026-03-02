

## Car App

Aplicação web para gestão de carros, abastecimentos, manutenções e histórico de mensagens do chatbot. Desenvolvida com Laravel, Livewire e Tailwind CSS.

## Funcionalidades
- Cadastro e gerenciamento de carros
- Registro de abastecimentos e manutenções
- Histórico de mensagens do chatbot
- Interface interativa com Livewire

## Requisitos
- PHP >= 8.1
- Composer
- MySQL ou outro banco compatível
- Node.js e npm (para assets front-end)

## Instalação
1. Clone o repositório:
	```bash
	git clone https://github.com/seu-usuario/car-app.git
	```
2. Instale as dependências PHP:
	```bash
	composer install
	```
3. Instale as dependências front-end:
	```bash
	npm install && npm run build
	```
4. Configure o arquivo `.env` com suas credenciais de banco de dados e outras variáveis.
5. Execute as migrações:
	```bash
	php artisan migrate --seed
	```
6. Inicie o servidor:
	```bash
	php artisan serve
	```

## Uso
Acesse `http://localhost:8000` no navegador para utilizar o sistema.

## Testes
Para rodar os testes:
```bash
php artisan test
```

## Estrutura do Projeto
- `app/Models`: Modelos Eloquent
- `app/Http/Controllers`: Controllers
- `app/Livewire`: Componentes Livewire
- `resources/views`: Views Blade
- `database/migrations`: Migrações
- `database/seeders`: Seeders

## Contribuição
Contribuições são bem-vindas! Abra uma issue ou envie um pull request.

## Licença
MIT

## Contato
Para dúvidas ou sugestões, envie um e-mail para [aquilahenrique.silva@gmail.com](mailto:aquilahenrique.silva@gmail.com).
