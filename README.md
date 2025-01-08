# giphy-api

## Project Deployment

1. **Clone the repository**:
   ```bash
   git clone https://github.com/enzonotario/giphy-api.git
   cd giphy-api
   ```
1. Install dependencies:

    ```bash
    composer install
    ```

1.  Create the .env file:

    ```bash
    cp .env.example .env
    ```

1.  Generate the application key:

    ```bash
    php artisan key:generate
    ```
    
1. Edit the .env file and set the following variables:

    ```bash
    GIPHY_API_KEY=YOUR_GIPHY_API_KEY
    ```

1.  Start the development server:
    
    Using Makefile:

    ```bash
    make up
    ```
    
    Or manually:

    ```bash
    ./vendor/bin/sail up -d
    ```
    
1.  Run initial setup:

    Using Makefile:

    ```bash
    make install
    ```
    
    Or manually:

    ```bash
    ./vendor/bin/sail exec laravel.test php artisan migrate
	./vendor/bin/sail exec laravel.test php artisan passport:keys
    ```

1.  Run the tests:

    Using Makefile:

    ```bash
    make test
    ```
    
    Or manually:

    ```bash
    ./vendor/bin/sail exec laravel.test php artisan test
    ```
    
7.  Fresh the database:

    Using Makefile:

    ```bash
    make fresh
    ```
    
    Or manually:

    ```bash
    ./vendor/bin/sail exec laravel.test php artisan migrate:fresh --seed
    ```

## Use Cases

```mermaid
flowchart LR
A((User)) -->|User gets a token| B((Login))
A -->|User searches for GIFs by keyword| C((Search GIFs))
A -->|User finds a GIF by ID| E((Get GIF))
A -->|User marks a GIF as favorite| D((Favorites))
```

## Sequence Diagrams

```mermaid
sequenceDiagram
participant U as User
participant API as API
participant DB as Database

    U->>API: POST /api/auth/login
    API->>DB: Verifies credentials
    DB-->>API: Returns valid credentials
    API->>DB: Log request
    API-->>U: Returns Token valid for 30 minutes

    U->>API: GET /api/gifs/search?query=cats (With Bearer Token)
    API->>Giphy API: Search GIFs in Giphy API by keyword
    Giphy API-->>API: Returns list of GIFs
    API->>DB: Log request
    API-->>U    : Returns GIFs

    U->>API: GET /api/gifs/{id} (With Bearer Token)
    API->>Giphy API: Get GIF by ID
    Giphy API-->>API: Returns GIF
    API->>DB: Log request
    API-->>U: Returns GIF
    
    U->>API: POST /api/gifs/favorite
    API->>DB: Inserts into favorites table
    DB-->>API: Returns success message
    API->>DB: Log request
    API-->>U: Returns success message
```

## Data Diagram

```mermaid
erDiagram
"User" ||--|{ "Favorite" : "has"
"Favorite" }|--|| "GIF (External)" : "associates"
"RequestLog" }|--|| "User" : "logs"
```

## Postman Collection

You can find the Postman Collection [here](./giphy-api.postman_collection.json).
