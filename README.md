# LAMP Stack on MicroK8s

This project contains a LAMP (Linux, Apache, MySQL, PHP) application deployed on MicroK8s, a lightweight Kubernetes distribution ideal for local development.

## Table of Contents

- [Prerequisites](#prerequisites)
- [MicroK8s Installation](#microk8s-installation)
- [Initial Setup](#initial-setup)
- [Application Deployment](#application-deployment)
- [Accessing the Application](#accessing-the-application)
- [Useful Commands](#useful-commands)
- [Troubleshooting](#troubleshooting)

## Prerequisites

- **Operating System**: Ubuntu 20.04 or higher (also compatible with other Linux distributions, macOS, and Windows)
- **Minimum Resources**:
  - 4GB RAM
  - 20GB disk space
  - Multi-core processor

## MicroK8s Installation

### Linux (Ubuntu/Debian)

```bash
# Install MicroK8s using snap
sudo snap install microk8s --classic

# Add your user to the microk8s group (to avoid using sudo)
sudo usermod -a -G microk8s $USER
sudo chown -f -R $USER ~/.kube

# Reload user groups
newgrp microk8s

# Verify the installation
microk8s status --wait-ready
```

### macOS

```bash
# Install using Homebrew
brew install ubuntu/microk8s/microk8s

# Install MicroK8s
microk8s install

# Verify the installation
microk8s status --wait-ready
```

### Windows

```powershell
# Using Chocolatey
choco install microk8s

# Or download the installer from:
# https://microk8s.io/docs/install-windows
```

For more installation options, visit: https://microk8s.io/#install-microk8s

## Initial Setup

### 1. Enable Required Addons

```bash
# Enable DNS for name resolution
microk8s enable dns

# Enable local image registry
microk8s enable registry

# Enable Ingress for HTTP routing
microk8s enable ingress

# Enable persistent storage
microk8s enable storage

# Verify that addons are active
microk8s status
```

### 2. Configure kubectl (optional but recommended)

```bash
# Create alias for easier use
alias kubectl='microk8s kubectl'

# Or export the config to use native kubectl
microk8s config > ~/.kube/config
```

### 3. Build the Docker Image

```bash
# Navigate to the application directory
cd lamp-k8s

# Build the Docker image
docker build -t localhost:32000/lamp-k8s:latest .

# Push the image to the local MicroK8s registry
docker push localhost:32000/lamp-k8s:latest

# Or use MicroK8s docker directly
microk8s ctr image import lamp-k8s.tar
```

## Application Deployment

### Apply Kubernetes Manifests

The manifests should be applied in order:

```bash
# 1. Create the namespace
microk8s kubectl apply -f lamp-k8s/k8s/00-lamp-k8s-namespace.yaml

# 2. Create secrets with credentials
microk8s kubectl apply -f lamp-k8s/k8s/01-lamp-k8s-secret.yaml

# 3. Deploy Apache/PHP
microk8s kubectl apply -f lamp-k8s/k8s/02-lamp-k8s-apache.yaml

# 4. Deploy MySQL
microk8s kubectl apply -f lamp-k8s/k8s/03-lamp-k8s-mysql.yaml

# 5. Configure Ingress
microk8s kubectl apply -f lamp-k8s/k8s/04-lamp-k8s-ingress.yaml

# Or apply all at once
microk8s kubectl apply -f lamp-k8s/k8s/
```

### Verify Deployment

```bash
# Check pod status
microk8s kubectl get pods -n lamp-k8s

# Check services
microk8s kubectl get services -n lamp-k8s

# Check ingress
microk8s kubectl get ingress -n lamp-k8s

# View logs from a specific pod
microk8s kubectl logs -n lamp-k8s <pod-name>
```

## Accessing the Application

### Option 1: Using NodePort

The application will be available at:

```
http://localhost:30081
```

### Option 2: Using Ingress

If you configured Ingress, you need to add an entry to your `/etc/hosts` file:

```bash
# Edit the hosts file
sudo nano /etc/hosts

# Add this line
127.0.0.1 lamp-k8s.local
```

Then access:

```
http://lamp-k8s.local
```

### Verify MySQL Connection

The PHP application should automatically connect to MySQL and display:
- Connection status
- MySQL version
- Database information

## Useful Commands

### MicroK8s Management

```bash
# Check status
microk8s status

# Start MicroK8s
microk8s start

# Stop MicroK8s
microk8s stop

# Restart MicroK8s
microk8s stop && microk8s start

# View available addons
microk8s enable --help
```

### Application Management

```bash
# View all resources in the namespace
microk8s kubectl get all -n lamp-k8s

# Describe a pod
microk8s kubectl describe pod <pod-name> -n lamp-k8s

# Access a pod
microk8s kubectl exec -it <pod-name> -n lamp-k8s -- /bin/bash

# View logs in real-time
microk8s kubectl logs -f <pod-name> -n lamp-k8s

# Restart a deployment
microk8s kubectl rollout restart deployment/lamp-k8s-apache -n lamp-k8s
microk8s kubectl rollout restart deployment/lamp-k8s-mysql -n lamp-k8s
```

### Update the Application

```bash
# 1. Rebuild the image
docker build -t localhost:32000/lamp-k8s:latest ./lamp-k8s

# 2. Push to registry
docker push localhost:32000/lamp-k8s:latest

# 3. Restart the deployment
microk8s kubectl rollout restart deployment/lamp-k8s-apache -n lamp-k8s
```

### Clean Up Deployment

```bash
# Delete all resources
microk8s kubectl delete -f lamp-k8s/k8s/

# Or delete the entire namespace (this removes everything)
microk8s kubectl delete namespace lamp-k8s
```

## Troubleshooting

### Pods Won't Start

```bash
# Check events
microk8s kubectl get events -n lamp-k8s --sort-by='.lastTimestamp'

# Describe the problematic pod
microk8s kubectl describe pod <pod-name> -n lamp-k8s
```

### MySQL Connection Error

```bash
# Verify MySQL is running
microk8s kubectl get pods -n lamp-k8s | grep mysql

# View MySQL logs
microk8s kubectl logs -n lamp-k8s <mysql-pod-name>

# Check the secret
microk8s kubectl get secret lamp-k8s-secret -n lamp-k8s -o yaml
```

### Image Not Found

```bash
# List images in local registry
curl http://localhost:32000/v2/_catalog

# Verify registry addon is enabled
microk8s status | grep registry

# Rebuild and push the image
docker build -t localhost:32000/lamp-k8s:latest ./lamp-k8s
docker push localhost:32000/lamp-k8s:latest
```

### Ingress Not Working

```bash
# Verify ingress is enabled
microk8s status | grep ingress

# View ingress controller
microk8s kubectl get pods -n ingress

# Check ingress configuration
microk8s kubectl describe ingress lamp-k8s-ingress -n lamp-k8s
```

### Complete Restart

```bash
# Stop MicroK8s
microk8s stop

# Restart
microk8s start

# Wait until ready
microk8s status --wait-ready
```

## Project Structure

```
.
├── lamp-k8s/
│   ├── app/
│   │   └── index.php          # Main PHP application
│   ├── k8s/
│   │   ├── 00-lamp-k8s-namespace.yaml
│   │   ├── 01-lamp-k8s-secret.yaml
│   │   ├── 02-lamp-k8s-apache.yaml
│   │   ├── 03-lamp-k8s-mysql.yaml
│   │   └── 04-lamp-k8s-ingress.yaml
│   ├── Dockerfile
│   └── docker-compose.yml
└── README.md
```

## Default Credentials

> **⚠️ IMPORTANT**: These credentials are for local development only. Change them for production.

- **MySQL Root Password**: `rootpassword`
- **MySQL Database**: `lamp_db`
- **MySQL User**: `lamp_user`
- **MySQL Password**: `lamp_password`

To change credentials, edit the [lamp-k8s/k8s/01-lamp-k8s-secret.yaml](lamp-k8s/k8s/01-lamp-k8s-secret.yaml) file

## Additional Resources

- [Official MicroK8s Documentation](https://microk8s.io/docs)
- [MicroK8s Tutorials](https://microk8s.io/tutorials)
- [Kubernetes Documentation](https://kubernetes.io/docs/home/)

## License

This project is open source and available under the MIT License.
