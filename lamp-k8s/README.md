# LAMP Stack on MicroK8s

LAMP Stack (Linux, Apache, MySQL, PHP) deployed on MicroK8s.

## Requirements

- MicroK8s installed and running
- Docker installed
- MicroK8s registry enabled: `microk8s enable registry`
- Ingress enabled: `microk8s enable ingress`

## Build Image

```bash
source .env
docker build -t $REGISTRY/$IMAGE_NAME:$IMAGE_TAG -f docker/apache-php/Dockerfile .
docker push $REGISTRY/$IMAGE_NAME:$IMAGE_TAG
```

## Update Image in MicroK8s

After making changes to the application (e.g., updating `index.php`), rebuild and redeploy:

```bash
# 1. Rebuild the Docker image
source .env
docker build -t $REGISTRY/$IMAGE_NAME:$IMAGE_TAG -f docker/apache-php/Dockerfile .
docker push $REGISTRY/$IMAGE_NAME:$IMAGE_TAG

# 2. Restart the Apache deployment to use the new image
microk8s kubectl rollout restart deployment lamp-k8s-apache -n lamp-k8s

# 3. Check rollout status
microk8s kubectl rollout status deployment lamp-k8s-apache -n lamp-k8s
```

## Start Deployments

```bash
microk8s kubectl apply -f k8s/
```

## Stop Deployments

```bash
microk8s kubectl delete namespace lamp-k8s
```

## Access

### Via Ingress (recommended)

Add to `/etc/hosts`:
```
127.0.0.1 lamp-k8s.local
```

Access at:
```
http://lamp-k8s.local
```

### Via NodePort (alternative)

```
http://localhost:30081
```

## View Status

```bash
microk8s kubectl get all -n lamp-k8s
```

## Troubleshooting

### View Apache logs
```bash
microk8s kubectl logs -n lamp-k8s deployment/lamp-k8s-apache -f
```

### View MySQL logs
```bash
microk8s kubectl logs -n lamp-k8s deployment/lamp-k8s-mysql -f
```

### Check pod status
```bash
microk8s kubectl get pods -n lamp-k8s
microk8s kubectl describe pod <pod-name> -n lamp-k8s
```
