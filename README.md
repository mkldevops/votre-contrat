### Build project
```bash
docker build --target prod -t mkldevops/cs-cosmetics .
```

### Run project
```bash
docker run -it --rm --name cs-cosmetics -p 8021:80 -e DATABASE_URL=postgresql://app:CiaLhPYpThbLIVD@techndcall.com:5432/cs-cosmetics -e APP_SECRET=488e51ef0817b160814bb98691c14e29 -d mkldevops/cs-cosmetics
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

