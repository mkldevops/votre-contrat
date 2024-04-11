### Build project
```bash
docker build --target prod -t mkldevops/cs-cosmetics .
```

### Create volume
```bash
docker volume create cs-cosmetics-dev
```

### Run project
```bash
docker run -it --rm --name cs-cosmetics-dev -p 8021:80 -e DATABASE_URL=$DATABASE_URL_POSTGRES -e APP_SECRET=$(echo openssl rand -base64 32) -d mkldevops/cs-cosmetics
```

### Run zsh
```bash
docker exec -it cs-cosmetics-dev zsh
``` 


### Run configuration
```bash
docker exec -ti cs-cosmetics-dev symfony composer i
docker exec -ti cs-cosmetics-dev symfony console d:d:c --if-not-exists
docker exec -ti cs-cosmetics-dev symfony console d:m:m -n
docker exec -ti cs-cosmetics-dev symfony console h:f:l -n
```

### logs
```bash
docker logs -f cs-cosmetics-dev
```

### Stop project
```bash
docker rm -f cs-cosmetics-dev
```

