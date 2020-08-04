<?php
declare(strict_types=1);

use EurekaService\EurekaClient;
use EurekaService\EurekaConfig\EurekaInfoConfig;
use EurekaService\EurekaApi;

require_once './vendor/autoload.php';

$eurekaInfoConfig = new EurekaInfoConfig();
$eurekaApi = new EurekaApi($eurekaInfoConfig);
EurekaApi::$cache_file ='/opt/eureka_info/eureka_instances_cache.json';
EurekaClient::getInstance()->run($eurekaApi);