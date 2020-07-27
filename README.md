# swoole-eureka-client
 
## 简介

swoole-eureka-client 是一款基于swoole process是实现的spring clound eureka 客户端，继承了swoole协程的高性能特征
默认采用守护进程的方式，可以结合swoole_server & systemd & supervisor 来实现长期运行，自动重启
此版本类似sidecar，但是非sidecar版本，并未劫持应用流量，后续会添加sidecar版本


## 功能

基本实现java版本的全部功能
- 内存存储服务列表
- 服务变更更新实时变更到内存

## 运行环境

- [PHP 7.1+](https://github.com/php/php-src/releases)
- [Swoole 4.4+](https://github.com/swoole/swoole-src/releases)
- [Composer](https://getcomposer.org/)
- [apcu](https://github.com/krakjoe/apcu)

## 运行示例
```
cd ./swoole-eureka-client/src && php index.php
```

## 停止运行
```
 kill -15 $masterpid
```

## License

swoole-eureka-client is an open-source software licensed under the MIT
