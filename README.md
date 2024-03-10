### Build project
```bash
docker build --target prod -t mkldevops/cs-cosmetics .
```

### Run project
```bash
docker run -it --rm --name cs-cosmetics -p 8021:80 -e DATABASE_URL=$DATABASE_URL_POSTGRES -e APP_SECRET=$(echo openssl rand -base64 32) -d mkldevops/cs-cosmetics
```

### Run zsh
```bash
docker exec -it cs-cosmetics zsh
``` 


### Run configuration
```bash
docker exec -ti cs-cosmetics symfony composer i
docker exec -ti cs-cosmetics symfony console d:d:c --if-not-exists
docker exec -ti cs-cosmetics symfony console d:m:m -n
docker exec -ti cs-cosmetics symfony console h:f:l -n
```

### logs
```bash
docker logs -f cs-cosmetics
```

### Stop project
```bash
docker rm -f cs-cosmetics
```

