#!/bin/bash

# Laravel QR Scanner Production Deployment Script
set -e

echo "🚀 Starting Laravel QR Scanner deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    print_error "Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create necessary directories
print_status "Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p database
mkdir -p ssl

# Set proper permissions
print_status "Setting proper permissions..."
chmod -R 755 storage
chmod -R 755 database

# Check if .env file exists
if [ ! -f .env ]; then
    print_warning ".env file not found. Creating from .env.example..."
    if [ -f .env.example ]; then
        cp .env.example .env
        print_warning "Please edit .env file with your production settings before continuing."
        exit 1
    else
        print_error ".env.example file not found. Please create .env file manually."
        exit 1
    fi
fi

# Build and start containers
print_status "Building Docker image..."
docker-compose -f docker-compose.yml build --no-cache

print_status "Starting containers..."
docker-compose -f docker-compose.yml up -d

# Wait for container to be ready
print_status "Waiting for application to be ready..."
sleep 30

# Check if container is running
if docker-compose -f docker-compose.yml ps | grep -q "Up"; then
    print_status "✅ Application deployed successfully!"
    print_status "🌐 Application is running at: http://localhost"
    print_status "📊 Health check: http://localhost/health"
    
    # Show container logs
    print_status "📋 Container logs:"
    docker-compose -f docker-compose.yml logs --tail=20
    
    # Show container status
    print_status "📊 Container status:"
    docker-compose -f docker-compose.yml ps
else
    print_error "❌ Deployment failed. Check logs:"
    docker-compose -f docker-compose.yml logs
    exit 1
fi

print_status "🎉 Deployment completed successfully!"
print_status "💡 To view logs: docker-compose -f docker-compose.yml logs -f"
print_status "💡 To stop: docker-compose -f docker-compose.yml down"
print_status "💡 To restart: docker-compose -f docker-compose.yml restart"
