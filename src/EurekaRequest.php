<?php
declare(strict_types=1);

namespace EurekaService;


use EurekaService\Concern\RequestTrait;
use EurekaService\Exceptions\EurekaException;
use EurekaService\Contracts\Exception;
use EurekaService\Contracts\RequestInterface;
use EurekaService\Helper\Helper;
use EurekaService\Models\Response;
use Swoole\Coroutine\Http\Client;
use Swoole\Coroutine\WaitGroup;
use \Throwable;

/**
 *  instance of RequestInterface
*/
class EurekaRequest implements RequestInterface
{
    use RequestTrait;

    /**
     * @var int
    */
    private $timeout = 10;

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort(): int
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
     * @var string
    */
    private $host = '127.0.0.1';
    /**
     * @var int
    */
    private $port = 1111;

    /**
     * @inheritDoc
     */
    public function request(string $uri, array $options=[],string $method): Response
    {
        try {
            $query   = $options['query']   ?? [];
            $timeout = $options['timeout'] ?? $this->timeout;
            $headers = $options['headers'] ?? [];
            $data    = $options['body'] ?? "";
            if (!empty($query)) {
                $query = is_array($query) ?  http_build_query($query) : $query;
                $uri   = sprintf('%s?%s', $uri, $query);
            }
            // Request
            $client = new Client($this->host, $this->port);
            $client->set(['timeout' => $timeout]);
            $client->setMethod($method);
            $client->setHeaders($headers);
            $client->setData($data);
            $client->execute($uri);
            $body   = $client->getBody();
            $status = $client->getStatusCode();
            $header = $client->getHeaders() ?? [];
            $cookie = $client->getCookies() ?? [];
            $client->close();
            //judge the abnormal code
            if ($status == -1 || $status == -2 || $status == -3) {
                throw new EurekaException(
                    sprintf(
                        'Request timeout!(host=%s, port=%d timeout=%d)',
                        $this->host,
                        $this->port,
                        $timeout
                    )
                );
            }
        } catch (EurekaException $e) {
            Helper::getLogger()->error(sprintf('request (%s)  fail!(%s)', $uri, $e->getMessage()));
        }
        return  Response::build($status,$body,$cookie,$header);
    }

    /**
     * @inheritDoc
     */
    public function requestBatch(array $requests): array
    {
        $result = [];
        if(empty($requests)) return $result;
        $wg = new  WaitGroup();
        foreach($requests as $key => $req){
            $wg->add();
            go(function() use ($wg,&$result,$req) {
                try {
                    //Response
                    $response = $req();
                    $result[] = $response;
                } catch (EurekaException $ex) {
                    Helper::getLogger()->error("batch request error is ".$ex->getMessage().' request info is '.json_encode($req));
                    $result[] = false;
                }
                $wg->done();
            });
        }
        $wg->wait();
        return $result;
    }
}