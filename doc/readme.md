## Api doc
```
apidoc --debug  -v  -i modules/ -o doc/api/
```

```
http-server doc/api/
```


## BanyanDb docker

### 安装 
banyan_docker 改成对应目录
```
docker run --name banyanDB -d -it -p 5002:10700 -v /banyan_docker:/data/ centos /data/shell/banyan_start.sh
```

连接
```
./data/packages/banyan_client 172.17.0.2 10700
```

