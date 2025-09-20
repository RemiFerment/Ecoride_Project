## Local Installation Tutorial

Follow these steps to set up the project on your local machine:

### Prerequisites

- [Docker](https://www.docker.com/get-started) and [Docker Compose](https://docs.docker.com/compose/install/) installed
- [Composer](https://getcomposer.org/) installed on your host PC (optional, if you want to run Composer outside Docker)
- PHP installed on your host PC (for running Symfony commands outside Docker)

### 1. Clone the Repository

```bash
git clone <repository-url>
cd Ecoride_Project
```

### 2. Start Docker Services

This will start the database, phpMyAdmin, Composer, and Mailhog containers.

```bash
docker compose up -d
```

### 3. Install PHP Dependencies

Run Composer inside the Docker container:

```bash
docker compose run --rm composer install
```

### 4. Install Importmap

On your host PC, run:

```bash
php bin/console importmap:install
```

### 5. Run Database Migrations

On your host PC, execute:

```bash
php bin/console doctrine:migrations:migrate
```

### 6. Build and Start the Application

If you haven't already, build and start the app with Docker Compose:

```bash
docker compose up --build
```

### 7. Access the Application

- **Web App:** [http://localhost:8000](http://localhost:8000)
- **phpMyAdmin:** [http://localhost:8080](http://localhost:8080)
- **Mailhog:** [http://localhost:8025](http://localhost:8025)

---

Your project should now be running locally!