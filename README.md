Build:
```
docker build -t frankenphp .
```

Up:

```
docker compose up -d
```

Connect:

```
docker exec -it frankenphp sh
```

Re-attach and follow logs:

```
docker compose logs -f frankenphp-server
```
