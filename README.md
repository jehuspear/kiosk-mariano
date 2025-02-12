# SINCO CAFE Kiosk Ordering System

A PHP-based kiosk ordering system for SINCO CAFE with customer and admin interfaces.

## Local Development Setup

### Prerequisites
- Docker
- Docker Compose

### Running Locally
1. Clone the repository:
```bash
git clone <repository-url>
cd kiosk-mariano
```

2. Start the containers:
```bash
docker-compose up -d
```

3. Access the application:
- Customer Interface: http://localhost:8080/customer/home.php
- Admin Interface: http://localhost:8080/admin-staff/login.php

## Azure Deployment

### Prerequisites
1. Azure Account with an active subscription
2. Azure CLI installed
3. GitHub account

### Setup Azure Resources

1. Create an Azure App Service:
```bash
az group create --name sinco-cafe-rg --location southeastasia
az appservice plan create --name sinco-cafe-plan --resource-group sinco-cafe-rg --sku B1 --is-linux
az webapp create --resource-group sinco-cafe-rg --plan sinco-cafe-plan --name sinco-cafe-kiosk --runtime "PHP|8.2"
```

2. Create an Azure Database for MySQL:
```bash
az mysql flexible-server create \
  --resource-group sinco-cafe-rg \
  --name sinco-cafe-db \
  --admin-user myadmin \
  --admin-password <your-password> \
  --sku-name Standard_B1ms
```

### GitHub Actions Setup

1. Add the following secrets to your GitHub repository:
- `AZURE_CREDENTIALS`: Service principal credentials
- `AZURE_RESOURCE_GROUP`: Resource group name
- `DB_HOST`: MySQL server hostname
- `DB_USER`: Database username
- `DB_PASSWORD`: Database password
- `DB_NAME`: Database name (kiosk_ordering_system_db)

2. Push to main branch to trigger deployment:
```bash
git push origin main
```

## Features

### Customer Interface
- Browse menu items
- Add items to cart
- Place orders
- Track order status
- Provide feedback

### Admin Interface
- Manage menu items
- Process orders
- View sales reports
- Manage staff accounts
- Monitor order status

## Environment Variables

The application uses the following environment variables:
- `DB_HOST`: Database hostname
- `DB_USER`: Database username
- `DB_PASSWORD`: Database password
- `DB_NAME`: Database name

## Project Structure

```
├── admin-staff/          # Admin interface
├── customer/            # Customer interface
├── docker-compose.yml   # Docker configuration
├── Dockerfile          # Docker build instructions
└── .github/workflows/  # GitHub Actions workflows
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
