<?php
declare(strict_types=1);

namespace EurekaService\EurekaConfig;


class EurekaInfoConfig
{
    /**
     * @var string
     */
    private $name = "eureka-demo-v1";
    /**
     * @var string
     */
    private $status = "UP";

    /**
     * @var string
     */
    private $hostName="127.0.0.1";
    /**
     * @var string
     */
    private $ipAddr="127.0.0.1";
    /**
     * @var int
     */
    private $port=80;

    /**
     * @var int
     */
    private $securePort=443;

    /**
     * @var string
     */
    private $homePageUrl="info";
    /**
     * @var string
     */
    private $statusPageUrl="status";

    /**
     * @var string
     */
    private $heathCheckUrl="health";
    /**
     * @var string
     */
    private $vipAddress="127.0.0.1";
    /**
     * @var string
     */
    private $secureVipAddress="127.0.0.1";
    /**
     * @var string
     */
    private $overriddenstatus="UNKNOWN";
    /**
     * @var int
     */
    private $countryId=1;
    /**
     * @var int
     */
    private $renewalIntervalInSecs=15;

    /**
     * @var string
    */
    private $lastDirtyTimestamp = (string)(round(microtime(true) * 1000));

    /**
     * @return callable
     */
    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback): void
    {
        $this->callback = $callback;
    }

    /**
     * @var callable
    */
    private $callback;


    /**
     * @return string
     */
    public function getLastDirtyTimestamp(): string
    {
        return $this->lastDirtyTimestamp;
    }

    /**
     * @param string $lastDirtyTimestamp
     */
    public function setLastDirtyTimestamp(string $lastDirtyTimestamp): void
    {
        $this->lastDirtyTimestamp = $lastDirtyTimestamp;
    }

    /**
     * @var array
     */
    private $eurekaHost=[
        [
            'host'=>'127.0.0.1',
            'port' =>1111,
            'prefix' =>'eureka'
        ],
        [
            'host'=>'127.0.0.2',
            'port' =>1111,
            'prefix' =>'eureka'
        ]
    ];

    /**
     * @return string
     */
    public function getEurekaHost(): ?array
    {
        return $this->eurekaHost;
    }

    /**
     * @param string $eurekaHost
     */
    public function setEurekaHost(array $eurekaHost): void
    {
        $this->eurekaHost = $eurekaHost;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getHostName(): ?string
    {
        return $this->hostName;
    }

    /**
     * @param string $hostName
     */
    public function setHostName(string $hostName): void
    {
        $this->hostName = $hostName;
    }

    /**
     * @return string
     */
    public function getIpAddr(): ?string
    {
        return $this->ipAddr;
    }

    /**
     * @param string $ipAddr
     */
    public function setIpAddr(string $ipAddr): void
    {
        $this->ipAddr = $ipAddr;
    }

    /**
     * @return int
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @return int
     */
    public function getSecurePort(): ?int
    {
        return $this->securePort;
    }

    /**
     * @param int $securePort
     */
    public function setSecurePort(int $securePort): void
    {
        $this->securePort = $securePort;
    }

    /**
     * @return string
     */
    public function getHomePageUrl(): ?string
    {
        return $this->homePageUrl;
    }

    /**
     * @param string $homePageUrl
     */
    public function setHomePageUrl(string $homePageUrl): void
    {
        $this->homePageUrl = $homePageUrl;
    }

    /**
     * @return string
     */
    public function getStatusPageUrl(): ?string
    {
        return $this->statusPageUrl;
    }

    /**
     * @param string $statusPageUrl
     */
    public function setStatusPageUrl(string $statusPageUrl): void
    {
        $this->statusPageUrl = $statusPageUrl;
    }

    /**
     * @return string
     */
    public function getHeathCheckUrl(): ?string
    {
        return $this->heathCheckUrl;
    }

    /**
     * @param string $heathCheckUrl
     */
    public function setHeathCheckUrl(string $heathCheckUrl): void
    {
        $this->heathCheckUrl = $heathCheckUrl;
    }

    /**
     * @return string
     */
    public function getVipAddress(): ?string
    {
        return $this->vipAddress;
    }

    /**
     * @param string $vipAddress
     */
    public function setVipAddress(string $vipAddress): void
    {
        $this->vipAddress = $vipAddress;
    }

    /**
     * @return string
     */
    public function getSecureVipAddress(): ?string
    {
        return $this->secureVipAddress;
    }

    /**
     * @param string $secureVipAddress
     */
    public function setSecureVipAddress(string $secureVipAddress): void
    {
        $this->secureVipAddress = $secureVipAddress;
    }

    /**
     * @return string
     */
    public function getOverriddenstatus(): ?string
    {
        return $this->overriddenstatus;
    }

    /**
     * @param string $overriddenstatus
     */
    public function setOverriddenstatus(string $overriddenstatus): void
    {
        $this->overriddenstatus = $overriddenstatus;
    }

    /**
     * @return int
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     */
    public function setCountryId(int $countryId): void
    {
        $this->countryId = $countryId;
    }

    /**
     * @return int
     */
    public function getRenewalIntervalInSecs(): ?int
    {
        return $this->renewalIntervalInSecs;
    }

    /**
     * @param int $renewalIntervalInSecs
     */
    public function setRenewalIntervalInSecs(int $renewalIntervalInSecs): void
    {
        $this->renewalIntervalInSecs = $renewalIntervalInSecs;
    }

    /**
     * @return int
     */
    public function getDurationInSecs(): ?int
    {
        return $this->durationInSecs;
    }

    /**
     * @param int $durationInSecs
     */
    public function setDurationInSecs(int $durationInSecs): void
    {
        $this->durationInSecs = $durationInSecs;
    }

    /**
     * @return bool
     */
    public function isCoordinatingDiscoveryServer(): ?bool
    {
        return $this->isCoordinatingDiscoveryServer;
    }

    /**
     * @param bool $isCoordinatingDiscoveryServer
     */
    public function setIsCoordinatingDiscoveryServer(bool $isCoordinatingDiscoveryServer): void
    {
        $this->isCoordinatingDiscoveryServer = $isCoordinatingDiscoveryServer;
    }
    /**
     * @var int
     */
    private $durationInSecs=90;

    /**
     * @var bool
     */
    private $isCoordinatingDiscoveryServer =false;

    /**
     * fetch instance
     */
    public function getInstance(){
        return [
            'instanceId' => $this->getHostName().':'.$this->getName().':'.$this->getPort(),
            'hostName' =>$this->getHostName() ?? '127.0.0.1',
            'app' => $this->getName(),
            'ipAddr' => $this->getIpAddr() ?? '127.0.0.1',
            'status' => $this->getStatus(),
            'overriddenstatus' => $this->getOverriddenstatus() ?? 'UNKNOWN',
            'port' =>[
                '$' => $this->getPort(),
                '@enabled' => 'true'
            ],
            'securePort' =>[
                '$' => $this->getSecurePort(),
                '@enabled' =>'false'
            ],
            'countryId' =>$this->getCountryId(),
            'dataCenterInfo' =>[
                '@class' => 'com.netflix.appinfo.InstanceInfo$DefaultDataCenterInfo',
                'name' =>'MyOwn'
            ],
            'leaseInfo' =>[
                'renewalIntervalInSecs' => $this->getRenewalIntervalInSecs(),
                'durationInSecs' =>$this->getDurationInSecs(),
                'registerationTimestamp' => round(microtime(true)*1000),
                'lasterRenewalTimestamp' => 0,
                'evictionTimestamp' =>0,
                'serviceUpTimestamp' =>round(microtime(true)*1000)
            ],
            'metadata'=>[
                '@class' =>''
            ],
            'homePageUrl' => $this->getIpAddr().'/'.$this->getHomePageUrl(),
            'statusPageUrl'=>$this->getIpAddr().'/'.$this->getStatusPageUrl(),
            'healthCheckUrl'=>$this->getIpAddr().'/'.$this->getHeathCheckUrl(),
            'vipAddress' =>$this->getVipAddress(),
            'secureVipAddress'=>$this->getSecureVipAddress(),
            'isCoordinatingDiscoveryServer' => $this->isCoordinatingDiscoveryServer(),
            'lastUpdatedTimestamp' => (string)(round(microtime(true)*1000)),
            'lastDirtyTimestamp' =>$this->getLastDirtyTimestamp()
        ];
    }
}