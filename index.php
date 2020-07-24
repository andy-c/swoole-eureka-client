<?php
declare(strict_types=1);

use EurekaService\EurekaClient;
use EurekaService\EurekaConfig\EurekaInfoConfig;
use EurekaService\EurekaApi;

require_once './vendor/autoload.php';

$eurekaInfoConfig = new EurekaInfoConfig();
$eurekaInfoConfig->setIpAddr(swoole_get_local_ip()['eth0']);
$eurekaInfoConfig->setHostName(swoole_get_local_ip()['eth0']);
$eurekaInfoConfig->setLastDirtyTimestamp((string)(round(microtime(true) * 1000)));
$eurekaApi = new EurekaApi($eurekaInfoConfig);
EurekaClient::getInstance()->run($eurekaApi);