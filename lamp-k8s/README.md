# LAMP Stack en MicroK8s

Stack LAMP (Linux, Apache, MySQL, PHP) desplegado en MicroK8s.

## Requisitos

- MicroK8s instalado y corriendo
- Docker instalado
- Registry de MicroK8s habilitado: `microk8s enable registry`

## Construir Imagen

```bash
source .env
docker build -t $REGISTRY/$IMAGE_NAME:$IMAGE_TAG -f docker/apache-php/Dockerfile .
docker push $REGISTRY/$IMAGE_NAME:$IMAGE_TAG
```

## Iniciar Deployments

```bash
microk8s kubectl apply -f k8s/
```

## Detener Deployments

```bash
microk8s kubectl delete namespace lamp-k8s
```

## Acceder

```
http://localhost:30081
```

## Ver Estado

```bash
microk8s kubectl get all -n lamp-k8s
```
