# VESTI Backend Test

## Instruções

### 1. Subindo containers Docker (lumen + postgresql)

```bash
docker-compose up
```

### 1. Instalando dependencias Composer

```bash
docker exec lumen composer install
```

### 2. Preparando banco de dados
```bash
docker exec lumen php artisan migrate
```

### 3. Execução dos testes PHPUnit

```bash
docker exec lumen ./vendor/bin/phpunit tests
```
