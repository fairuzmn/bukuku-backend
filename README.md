# Bukuku Backend ☁︎

## Project Structure

The project is organized as a typical Laravel application, with the addition of a `docker` directory containing the Docker configurations and scripts. These are separated by environments and services. There are two main Docker Compose projects in the root directory:

- **compose.dev.yaml**: Orchestrates the development environment.
- **compose.prod.yaml**: Orchestrates the production environment.

### Directory Structure

```
project-root/ 
├── app/ # Laravel app folder
├── ...  # Other Laravel files and directories 
├── docker/ 
│   ├── common/ # Shared configurations
│   ├── development/ # Development-specific configurations 
│   ├── production/ # Production-specific configurations
├── compose.dev.yaml # Docker Compose for development 
├── compose.prod.yaml # Docker Compose for production 
└── .env.example # Example environment configuration
```

This modular structure ensures shared logic between environments while allowing environment-specific customizations.


### Development Environment

The development environment is configured using the `compose.dev.yaml` file and is built on top of the production version. This ensures the development environment is as close to production as possible while still supporting tools like Xdebug and writable permissions.

Key features include:
- **Close Parity with Production**: Mirrors the production environment to minimize deployment issues.
- **Development Tools**: Includes Xdebug for debugging and writable permissions for mounted volumes.
- **Hot Reloading**: Volume mounts enable real-time updates to the codebase without rebuilding containers.
- **Services**: PHP-FPM, Nginx, Redis, PostgreSQL, and Node.js (via NVM).
- **Custom Dockerfiles**: Extends shared configurations to include development-specific tools.

To set up the development environment, follow the steps in the **Getting Started** section.


## Getting Started

Follow these steps to set up and run the Laravel Docker Examples Project:

### Prerequisites
Ensure you have Docker and Docker Compose installed. You can verify by running:

```bash
docker --version
docker compose version
```

If these commands do not return the versions, install Docker and Docker Compose using the official documentation: [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/).

### Setting Up the Development Environment

1. Copy the .env.example file to .env and adjust any necessary environment variables:

```bash
cp .env.example .env
```

Hint: adjust the `UID` and `GID` variables in the `.env` file to match your user ID and group ID. You can find these by running `id -u` and `id -g` in the terminal.

2. Start the Docker Compose Services:

```bash
docker compose -f compose.dev.yaml up -d
```

3. Install Laravel Dependencies:

```bash
docker compose -f compose.dev.yaml exec workspace bash
composer install
npm install
npm run dev
```

4. Run Migrations:

```bash
docker compose -f compose.dev.yaml exec workspace php artisan migrate
```

5. Access the Application:

Open your browser and navigate to [http://localhost](http://localhost).

## Usage

Here are some common commands and tips for using the development environment:

### Accessing the Workspace Container

The workspace sidecar container includes Composer, Node.js, NPM, and other tools necessary for Laravel development (e.g. assets building).

```bash
docker compose -f compose.dev.yaml exec workspace bash
```

There `docksh` file also that can be used to simplify 

```bash
chmod +x docksh
./docksh
```

### Run Artisan Commands:

```bash
docker compose -f compose.dev.yaml exec workspace php artisan migrate
```

### Rebuild Containers:

```bash
docker compose -f compose.dev.yaml up -d --build
```

### Stop Containers:

```bash
docker compose -f compose.dev.yaml down
```

### View Logs:

```bash
docker compose -f compose.dev.yaml logs -f
```

For specific services, you can use:

```bash
docker compose -f compose.dev.yaml logs -f web
```

## Port Mapping Overview

| Service      | Host Port | Container Port | Purpose                                      |
|--------------|-----------|----------------|----------------------------------------------|
| Nginx (web)  | 8800      | 80             | Serves the Laravel application               |
| Workspace    | 5173      | 5173           | Vite development server                      |
| MySQL        | 3306      | 3306           | Database access from host                    |
| phpMyAdmin   | 8808      | 80             | phpMyAdmin UI                                |
| Adminer      | 8809      | 8080           | Adminer database UI                          |
| MailHog UI   | 8025      | 8025           | Mail inspection interface                    |
| MailHog SMTP | 1025      | 1025           | SMTP endpoint for captured emails            |
| MinIO API    | 9000      | 9000           | S3-compatible storage endpoint               |
| MinIO Console| 9001      | 9001           | MinIO management interface                   |


## Deploying

The production image can be deployed to any Docker-compatible hosting environment, such as AWS ECS, Kubernetes, or a traditional VPS.

## Tech Stack

- **PHP**: Version **8.4 FPM** is used for optimal performance in both development and production environments.
- **Node.js**: Version **22.x** is used in the development environment for building frontend assets with Vite.
- **PostgreSQL**: Version **16** is used as the database in the examples, but you can adjust the configuration to use MySQL if preferred.
- **Redis**: Used for caching and session management, integrated into both development and production environments.
- **Nginx**: Used as the web server to serve the Laravel application and handle HTTP requests.
- **MailHog**: Provides a local SMTP capture service, allowing the application to send emails without delivering them externally. Useful for testing mail flows and inspecting messages through its built-in web interface.
- **MinIO**: Provides an S3-compatible object storage service, enabling the application to store and retrieve files using the same interface as AWS S3. Useful for local development and production-like storage behavior.
- **Docker Compose**: Orchestrates the services, simplifying the process of starting and stopping the environment.
- **Health Checks**: Implemented in the Docker Compose configurations and Laravel application to ensure all services are operational.

