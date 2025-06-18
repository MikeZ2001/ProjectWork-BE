# App Stack

A full-stack application consisting of:
- Laravel API Backend
- React Frontend
- MySQL Database

## Features

// TODO: 

## Requirements

- Docker and Docker Compose
- Git

## Installation

1. Clone the repository:

```bash
# Create a directory for the projects
mkdir ProjectWork
cd ProjectWork

# Clone the launcher
git clone https://github.com/MikeZ2001/ProjectWork-Launcher.git

# Clone the BE and FE components
git clone https://github.com/MikeZ2001/ProjectWork-FE.git
git clone https://github.com/MikeZ2001/ProjectWork-BE.git
```

2. Start & setup the application stack:

```bash
# Navigate to the launcher directory
cd ProjectWork-Launcher

# Start the stack
./launcher.sh start

# Setup the stack
./launcher.sh setup
```

The stack includes:
- API: https://localhost:8000
- Frontend: https://localhost:3000
- Database: localhost:3306

## Development

### Available Commands

```bash
# Start the stack
./launcher.sh start

# Stop the stack
./launcher.sh stop

# Restart the stack
./launcher.sh restart

# View logs
./launcher.sh logs

# Check status
./launcher.sh status

# Clean everything
./launcher.sh clean

# Initial setup
./launcher.sh setup
```

### Test User

For testing purposes, a default user is created:
- Email: john.doe@example.com
- Password: password

## Project Structure

```
ProjectWork/
├── ProjectWork-Launcher/  # Docker setup and scripts
├── ProjectWork-BE/       # Laravel backend
└── ProjectWork-FE/       # React frontend
```

## License

This project is licensed under the MIT License.
