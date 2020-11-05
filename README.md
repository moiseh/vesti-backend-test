# VESTI Backend Test

## Instalação

### 1. Clonar e acessar o repositório

```bash
git clone https://github.com/moiseh/vesti-backend-test.git
cd vesti-backend-test
```

### 2. Subindo containers Docker (Lumen + PostgreSQL)

```bash
docker-compose up
```

### 3. Instalando dependencias Composer

```bash
docker exec lumen composer install
```

### 4. Instalando e preparando banco de dados
```bash
docker exec lumen php artisan migrate
```

## Instruções gerais

### 1. Sincronização de produtos e estoques
```bash
docker exec lumen php artisan sync:products
```

### 2. Acessando banco de dados

```bash
psql -Upostgres -hlocalhost
Senha: LumenPass123
```

### 3. Execução dos testes PHPUnit (cuidado: irá sempre zerar o banco de dados)

```bash
docker exec lumen ./vendor/bin/phpunit tests
```
