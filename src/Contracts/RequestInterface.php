<?php
declare(strict_types=1);

namespace EurekaService\Contracts;


use EurekaService\Exceptions\EurekaException;
use EurekaService\Models\Response;

/**
 * Interface RequestInterface
 */
interface RequestInterface
{
    /**
     * single request
     *
     * @param string $uri
     * @param array $options
     * @param string $method
     *
     * @return object
     * @throws EurekaException
     */
    public function request(string $uri, array $options, string $method): Response;

    /**
     * batch request
     *
     * @param array $requests
     * @param int $timeout
     *
     * @return object
     * @throws EurekaException
     */
    public function requestBatch(array $requests): array;

}