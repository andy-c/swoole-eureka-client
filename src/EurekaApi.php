<?php
declare(strict_types=1);

namespace EurekaService;


use EurekaService\EurekaConfig\EurekaInfoConfig;
use EurekaService\Helper\Helper;
use EurekaService\Contracts\RegisterCenterInterface;
use EurekaService\Contracts\RequestInterface;
use EurekaService\Exceptions\EurekaException;
use Swoole\Coroutine;
use Swoole\Timer;
use function apcu_fetch;
use function apcu_store;
use function json_encode;
/**
 * eureka client instance
*/
class EurekaApi implements RegisterCenterInterface
{

    /**
     * @var RequestInterface
    */
    private $request;

    /**
     * @var EurekaInfoConfig
    */
    private $eurekaInfoConfig;

    /**
     * @var int
    */
    private $updateAllAppsTimeInterval = 5000;

    /**
     * @var string
    */
    const VERSION_DELTA = 'APP_VERSION_DELTA';

    /**
     * @var string
    */
    const APP_PREFIX = "APP_PREFIX";

    /**
     * @param bool $runningStatus
     */
    public function setRunningStatus(bool $runningStatus): void
    {
        $this->runningStatus = $runningStatus;
    }

    /**
     * @var timer
    */
    private $heartBeatTimer;

    /**
     * @var timer
    */
    private $updateTimer;

    /**
     * @var bool
    */
    private $runningStatus = false;


    /**
     * @var array
    */
    private $defaultHeader =[
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ];

    /**
     * cache file
    */
    public static  $cache_file='/opt/eureka_instances/full_app_cache_file.json';

    /**
     * instance
     * @param $eurekaInfoConfig
     *
     * @return void
     * @throws  null
    */
   public function __construct(EurekaInfoConfig $eurekaInfoConfig)
   {
       $this->request = new EurekaRequest();
       $this->eurekaInfoConfig = $eurekaInfoConfig;
   }
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        //options for request
        $options['body'] = json_encode(['instance' => $this->eurekaInfoConfig->getInstance()],JSON_UNESCAPED_SLASHES);
        $options['headers'] = $this->buildHeader();
        $options['headers']['Content-Type'] ="application/json;charset=utf-8";
        //split eureka client info
        $eurekas = $this->eurekaInfoConfig->getEurekaHost();
        //loop register
        foreach($eurekas as $k => $v){
            $uri = '/'.$v['prefix'].'/apps/'.$this->eurekaInfoConfig->getName();
            $this->request->setHost($v['host']);
            $this->request->setPort($v['port']);
            $res = $this->request->post($uri,$options);
            if($res->getCode() !=204){
                Helper::getLogger()->info("eureka-register-status-code is".$res->getCode());
                continue;
            }else{
                Helper::getLogger()->info("eureka-register-result success,register-address is ".$v['host'].':'.$v['port']);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function deregister(): bool
    {
        $deregisterStatus = false;
        //split eureka client info
        $eurekas = $this->eurekaInfoConfig->getEurekaHost();
        //loop deregister
        foreach($eurekas as $k=>$v){
            $this->request->setHost($v['host']);
            $this->request->setPort($v['port']);
            $uri = '/'.$v['prefix'].'/apps/'.$this->eurekaInfoConfig->getName().'/'.$this->eurekaInfoConfig->getHostName().':'
                .$this->eurekaInfoConfig->getName().':'.$this->eurekaInfoConfig->getPort();
            $options['headers'] = $this->defaultHeader;
            $deregisterResult = $this->request->delete($uri,$options);
            Helper::getLogger()->info("eureka-deregister-status is ".$deregisterResult->getCode()." eureka-client-info is ".$v['host'].':'.$v['port']);
            if($deregisterResult->getCode()==200){
                $deregisterStatus = true;
            }else{
                $deregisterStatus = false;
            }
        }
        return $deregisterStatus;
    }

    /**
     * @inheritDoc
     */
    public function heartbeat(): void
    {
        $this->runningStatus = true;
        $this->heartBeatTimer = Timer::tick($this->eurekaInfoConfig->getRenewalIntervalInSecs()*1000,function(){
                 if(!$this->runningStatus){
                     Timer::clear($this->heartBeatTimer);
                     return;
                 }
                //rand a instance
                $heartBeatStatus = false;
                $instance = array_rand($this->eurekaInfoConfig->getEurekaHost());
                $options['query'] = [
                    'status' => 'UP',
                    'lastDirtyTimestamp' => $this->eurekaInfoConfig->getLastDirtyTimestamp()
                ];

                //check application heath
                $this->request->setHost($this->eurekaInfoConfig->getIpAddr());
                $this->request->setPort($this->eurekaInfoConfig->getPort());
                $appHealthy = $this->request->get('/'.$this->eurekaInfoConfig->getHeathCheckUrl());
                if($appHealthy->getCode()!= 200){
                    $options['query']['status'] = 'DOWN';
                }
                //check application body
                $body = $appHealthy->getBody() ? json_decode($appHealthy->getBody(),true) :[];
                if(!is_array($body) ||$body['health']=='UNHEALTHY'){
                    $options['query']['status'] = 'DOWN';
                }
                unset($appHealthy,$body);

                Helper::getLogger()->info("application-health-check-status is ".$options['query']['status']);

                //send the result to eureka server
                $options['headers'] = $this->buildHeader();
                $this->request->setPort($this->eurekaInfoConfig->getEurekaHost()[$instance]['port']);
                $this->request->setHost($this->eurekaInfoConfig->getEurekaHost()[$instance]['host']);
                $instanceId = $this->eurekaInfoConfig->getHostName().':'.$this->eurekaInfoConfig->getName().':'.$this->eurekaInfoConfig->getPort();
                $eurekaResponse = $this->request->put('/'.$this->eurekaInfoConfig->getEurekaHost()[$instance]['prefix'].'/apps/'.$this->eurekaInfoConfig->getName().'/'.$instanceId);
                if($eurekaResponse->getCode() == 404){
                    //application has't been register ,need to register
                    Helper::getLogger()->info("application-heartbeat-info code is 404");
                    $registerStatus  = $this->register();
                    if($registerStatus){
                        Helper::getLogger()->info("application-register-result is success");
                    }else{
                        Helper::getLogger()->info("application-register-result is failed");
                    }

                }else if($eurekaResponse->getCode()!=200){
                    Helper::getLogger()->info("heartbeat-error is ".$eurekaResponse->getCode());
                }else{
                    $heartBeatStatus = true;
                }
                unset($eurekaResponse);
                return $heartBeatStatus;
            });
    }

    /**
     * @inheritDoc
     */
    public function instances(): void
    {
        $callback = $this->eurekaInfoConfig->getCallBack();
        $this->runningStatus = true;
        $this->updateTimer = Timer::tick($this->updateAllAppsTimeInterval,function() use ($callback){
            try{
                if(!$this->runningStatus){
                    Timer::clear($this->updateTimer);
                    return;
                }
                $version_delta = apcu_fetch(self::VERSION_DELTA);
                //rand a eureka server to check apps version
                $eurekaServInfo = array_rand($this->eurekaInfoConfig->getEurekaHost());
                //get version_delta
                $this->request->setPort($this->eurekaInfoConfig->getEurekaHost()[$eurekaServInfo]['port']);
                $this->request->setHost($this->eurekaInfoConfig->getEurekaHost()[$eurekaServInfo]['host']);
                $versionDeltaResult = $this->request->get('/'.$this->eurekaInfoConfig->getEurekaHost()[$eurekaServInfo]['prefix'].'/apps/delta');
                if($versionDeltaResult->getCode() != 200){
                    Helper::getLogger()->info("eureka-version-delta-status is ".$versionDeltaResult->getCode());
                    return false;
                }
                $lastestVersionDelta = $versionDeltaResult->getBody() ? json_decode($versionDeltaResult->getBody(),true)['applications']['versions_delta'] : '';
                if($version_delta && $version_delta == $lastestVersionDelta){
                    return [];
                }
                $version_delta = $lastestVersionDelta;

                //pull the all apps
                $options['headers'] = $this->defaultHeader;
                $fullApps = $this->request->get('/'.$this->eurekaInfoConfig->getEurekaHost()[$eurekaServInfo]['prefix'].'/apps',$options);
                if($fullApps->getCode() !=200){
                    Helper::getLogger()->info("eureka-pull-full-apps-status is ".$fullApps->getCode());
                    return false;
                }
                //cache file
                file_put_contents(self::$cache_file,$fullApps->getBody());
                $apps = $fullApps->getBody() ? json_decode($fullApps->getBody(),true):[];
                if(!is_array($apps) && !empty($apps)){
                    return false;
                }
                //cache apps
                foreach($apps['applications']['application'] as $app){
                    apcu_store(self::APP_PREFIX.md5($app['name']),$app['instance']);
                }
                //cache version delta
                apcu_store(self::VERSION_DELTA ,$version_delta);
                //callback local service
                $status = $callback ? $callback() :"no callback";
                Helper::getLogger()->info("callback-local-service-status is ".$status);
                return true;
            }catch(EurekaException $ex){
                Helper::getLogger()->error("eureka-fetch-instances-error ".$ex->getMessage());
            }
        });
    }

    /**
     * @inheritDoc
     */
    public function instance(string $appId): array
    {
        $info = apcu_fetch(self::APP_PREFIX.md5($appId));
        if(!is_array($info)){
            return [];
        }
        return $info;
    }

    /**
     * build header
     *
     * @return array
    */
    private function buildHeader(){
         return  [
            'Accept-Encoding' => 'gzip',
            'DiscoveryIdentity-Name' => 'DefaultClient',
            'DiscoveryIdentity-Version' => '1.4',
            'DiscoveryIdentity-Id' => $this->eurekaInfoConfig->getHostName(),
            'Connection' => 'Keep-Alive',
        ];
    }

}