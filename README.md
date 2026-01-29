# Vending Machine (Symfony Console + DDD)

Implementación del reto **Vending Machine** en PHP usando **Symfony Console** y una estructura **DDD**.  
Incluye **tests unitarios** y **Docker/Docker Compose** para ejecutar todo sin depender del entorno local.


---

## Requisitos

- **Docker** + **Docker Compose** (recomendado para revisión)
- (Opcional) **PHP 8.4+** y **Composer** si quieres ejecutarlo sin Docker

---

## Quick start (Docker)

### 1) Build de la imagen
    docker compose build

---

### 2) Ejecutar los comandos

1. **Buy Soda with exact change**
    ```bash
    docker compose run --rm app php bin/console app:vending "1,0.25,0.25,GET-SODA"
    # SODA
2. **Retun Coin**
    ```bash
    docker compose run --rm app php bin/console app:vending "0.10,0.10,     RETURN-COIN"
    # 0.10,0.10
3. **Return Coin (including 1)**
    ```bash
   docker compose run --rm app php bin/console app:vending "1,RETURN-COIN"
    # 1
4. **Buy Water without exact change**
    ```bash
   docker compose run --rm app php bin/console app:vending "1,GET-WATER"
    # WATER,0.25,0.10
5. **Insufficient Funds**
    ```bash
   docker compose run --rm app php bin/console app:vending "0.25,GET-SODA"
    # INSUFFICIENT_FUNDS: required=150 inserted=25
6. **SERVICE- Configure stock and change**
    ```bash
    docker compose run --rm app php bin/console app:vending \
    "SERVICE items=WATER:10;JUICE:10;SODA:10 change=5:20;10:20;25:20,1,GET-WATER"
7. **SOLD_OUT**
    ```bash
    docker compose run --rm app php bin/console app:vending \
    "SERVICE items=WATER:0;JUICE:10;SODA:10 change=5:20;10:20;25:20,1,GET-WATER"
    # SOLD_OUT: WATER
8. **CANNOT_MAKE_CHANGE**
    ```bash
    docker compose run --rm app php bin/console app:vending \
    "SERVICE items=WATER:10;JUICE:10;SODA:10 change=5:0;10:0;25:0,1,GET-WATER"
    # CANNOT_MAKE_CHANGE: 35
8. **Limited Coins (Greedi Fail Case)**
    ```bash
    docker compose run --rm app php bin/console app:vending \
    "SERVICE items=WATER:10;JUICE:10;SODA:10 change=5:0;10:3;25:1,0.25,0.25,0.25,0.10,0.10,GET-WATER"
    # WATER,0.10,0.10,0.10
---

### 3) Ejecutar test unitarios
    docker compose run -rm app ./vendor/bin/phpunit
