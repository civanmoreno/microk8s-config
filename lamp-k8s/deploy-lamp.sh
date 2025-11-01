#!/bin/bash
set -e

# Cargar variables de entorno
source .env

echo "=== Construyendo imagen Docker ==="
docker build -t $IMAGE_NAME:$IMAGE_TAG -f docker/apache-php/Dockerfile .
docker tag $IMAGE_NAME:$IMAGE_TAG $REGISTRY/$IMAGE_NAME:$IMAGE_TAG
docker push $REGISTRY/$IMAGE_NAME:$IMAGE_TAG

echo ""
echo "=== Desplegando en MicroK8s ==="
microk8s kubectl create namespace $NAMESPACE --dry-run=client -o yaml | microk8s kubectl apply -f -
microk8s kubectl apply -f k8s/

echo ""
echo "=== Esperando pods ==="
microk8s kubectl wait --for=condition=ready pod -l app=mysql -n $NAMESPACE --timeout=180s
microk8s kubectl wait --for=condition=ready pod -l app=apache-php -n $NAMESPACE --timeout=180s

echo ""
echo "✓ Despliegue completado"
echo "Accede en: http://localhost:30080"
