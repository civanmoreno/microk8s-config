# LAMP Stack en MicroK8s

Stack LAMP (Linux, Apache, MySQL, PHP) desplegado en MicroK8s usando Docker y Kubernetes.

## Requisitos Previos

- MicroK8s instalado y corriendo
- Docker instalado
- Registry de MicroK8s habilitado: `microk8s enable registry`

## Configuración

Todas las variables están definidas en el archivo `.env`:

```bash
# Configuración de MySQL
MYSQL_HOST=mysql
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=lamp_db
MYSQL_USER=lamp_user
MYSQL_PASSWORD=lamp_password

# Configuración del registro
REGISTRY=lampphp
IMAGE_NAME=lamp-apache-php
IMAGE_TAG=latest

# Namespace de Kubernetes
NAMESPACE=php-lamp
```

## Paso 1: Construir la Imagen Docker

```bash
source .env
docker build -t $REGISTRY/$IMAGE_NAME:$IMAGE_TAG -f docker/apache-php/Dockerfile .
```

## Paso 2: Aplicar los Manifiestos en Kubernetes

```bash
source .env
microk8s kubectl apply -f k8s/
```

## Acceder a la Aplicación

```
http://localhost:30080
```
