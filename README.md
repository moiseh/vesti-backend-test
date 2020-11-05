# VESTI Backend Test

## Instalação

### 1. Subindo containers Docker (Lumen + PostgreSQL)

```bash
docker-compose up
```

### 2. Instalando dependencias Composer

```bash
docker exec lumen composer install
```

### 3. Preparando banco de dados
```bash
docker exec lumen php artisan migrate
```

## Instruções gerais

### 1. Sincronização de produtos e estoques
```bash
docker exec lumen php artisan sync:products
```

### 2. Execução dos testes PHPUnit

```bash
docker exec lumen ./vendor/bin/phpunit tests
```
