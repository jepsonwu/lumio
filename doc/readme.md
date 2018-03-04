## Api doc
```
apidoc --debug  -v  -i modules/ -o doc/api/
```

```
http-server doc/api/
```


## BanyanDb docker
```
docker run --name banyandb  -d -it -p 5001:22 -p 5002:10700 jepson/banyandb:v1 /data1/shell/banyan_start.sh
```
