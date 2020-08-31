<?php
declare(strict_types=1);

namespace EurekaService\Test;


use EurekaService\EurekaClient;
use EurekaService\Exceptions\EurekaException;
use PHPUnit\Framework\TestCase;

class EurekaTest extends TestCase
{

    public function testEureka(){
       $eurekaClient = $this->getMockBuilder(EurekaClient::class)
                    ->setMethods(['run'])
                    ->getMock();
       $eurekaClient->method('run');
       $this->expectException(EurekaException::class);
    }
}