# 🌾 TAKE HOME LARAVEL-VUE APP

APP para o gerenciamento de pedidos de exames com pacotes. Desenvolvido com **PHP/Laravel**, **Vue2**, **PostgreSQL**, **Eloquent ORM** e **TailWindCss**.

---

## 🚀 Como iniciar o projeto

### ✅ Pré-requisitos
- Docker + Docker Compose
- Node >= 16
- NPM
---

### 🧱 STEP 1 - Build e start com Docker

**Renomeie o arquivo .env.example para .env e execute:**

```bash
cd frontend
npm install
cd ..
docker compose build --no-cache
docker compose up -d
```

### 📦 STEP 2 - Criar banco e aplicar migrações Prisma
```bash
#Acesse o bash da aplicação
docker compose exec -it app bash
#Execute as migrations
php artisan migrate

#Para rodar as migrations com o seed de infos para teste execute:
php artisan migrate:fresh --seed
```

### 🖥️ STEP 3 - Iniciar a aplicação
A aplicação se inicia automaticamente com o Docker após alguns segundos. Basta acessar: 
```bash
http://localhost:8080
```

### 📚 DOCUMENTAÇÃO:
Um arquivo Exam_Requests_API.postman_collection está presente na raiz do projeto.
Crie um Enviroment no Postaman adicionando a constante **baseUrl = http://127.0.0.1:8000**

**Como usar Exam_Requests_API.postman_collection no Postman**
Vá em Import > File > Upload Files > Exam_Requests_API.postman_collection
Os endpoints serão importados automaticamente.

### 🧪 TESTES:
Após executar os testes será criado os PDFs de teste na pasta **backend/storage/app/testing/**
```bash
#Acesse o bash da aplicação
docker compose exec -it app bash
#Execute os teste
php artisan test
```


**Estrutura usada para testes:**
Usado PHPUnit com Faker do Laravel para construção dos testes presentes na pasta **backend/tests**


### 📘 Tecnologias Utilizadas
- PHP
- Laravel
- PostgreSQL
- Eloquent ORM
- Docker
- Vue 2
- TailWindCss