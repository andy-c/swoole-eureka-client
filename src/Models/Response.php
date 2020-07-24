<?php
declare(strict_types=1);

namespace EurekaService\Models;

class Response
{

    /**
     * @var Response
    */
    private static  $instance;

    /**
     * @var string|false
     */
    private $body;

    /**
     * @var  int|false
     */
    private $code;

    /**
     * @var array|false
     */
    private $header;

    /**
     * @var array|false
     */
    private $cookie;

    /**
     * @return false|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return false|int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return array|false
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return array|false
     */
    public function getCookie()
    {
        return $this->cookie;
    }



    public function __construct(int $responseCode,string $responseBody,array $responseCookie=[],array $responseHeader=[])
    {
        $this->code = $responseCode;
        $this->body = $responseBody;
        $this->cookie = $responseCookie;
        $this->header = $responseHeader;
    }

    /**
     * get instance
     * @param $responseCode
     * @param $responseBody
     * @param $responseCookie
     * @param $responseHeader
     *
     * @return Response
    */
    public static function build(int $responseCode,string $responseBody,array  $responseCookie=[],array $responseHeader=[]):self {
        if(!self::$instance){
            self::$instance = new self($responseCode,$responseBody,$responseCookie,$responseHeader);
        }else{
            self::$instance->code = $responseCode;
            self::$instance->body = $responseBody;
            self::$instance->cookie = $responseCookie;
            self::$instance->header = $responseHeader;
        }
        return self::$instance;
    }
}