# Smarty Hardware

E-commerce de **peças de PC** e **assistência técnica**, feito para portfólio: catálogo, estoque, checkout com CEP/cartão, painel admin (vendas no balcão e notas) e chatbot Mia.

- **API**: Laravel 11 + PHP 8.2, Sanctum, MySQL  
- **Front**: Vue 3, Vite, TypeScript, Pinia, Tailwind CSS  
- **Arquitetura**: Controller → Use Case → Repository (sem Prisma/Eloquent no domínio)

## Funcionalidades

- Catálogo de hardware e serviços, carrinho e checkout
- Busca de endereço por CEP (ViaCEP)
- Cadastro de cartão em sandbox (grava só bandeira e os 4 últimos dígitos)
- Pagamento fictício: PIX, crédito, débito, boleto, dinheiro e TED
- Admin: produtos, entrada de estoque, notas e venda no balcão
- Chatbot de atendimento (Gemini com fallback local)

## Como rodar

```bash
git clone git@github.com:exkgred/smarty-hardware.git
cd smarty-hardware

docker compose up -d mysql

cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000

cd ../frontend
npm install
npm run dev -- --host 127.0.0.1
```

Loja: [http://127.0.0.1:5173](http://127.0.0.1:5173) · API: [http://127.0.0.1:8000](http://127.0.0.1:8000)

O Vite já encaminha `/api` para o Laravel.

## Contas de demonstração

| Perfil  | Email                      | Senha    |
|---------|----------------------------|----------|
| Admin   | admin@marketplace.test     | password |
| Cliente | cliente@marketplace.test   | password |
| Balcão  | balcao@smartyhardware.test | password |

Cartão de teste no checkout: `4242 4242 4242 4242`, validade futura, CVV `123`.

## Testes

```bash
cd backend
php artisan test
```
