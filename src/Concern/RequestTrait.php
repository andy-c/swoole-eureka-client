<?php
declare(strict_types=1);

namespace EurekaService\Concern;

use EurekaService\Exceptions\EurekaException;
use EurekaService\Models\Response;

trait RequestTrait
{
   /**
    * http get method
    *
    * @param $uri
    * @param $options
    *
    * @return array
    * @throws EurekaException
   */
   public function get(string $uri,array $options=[]) : Response {
      return $this->request($uri,$options,'GET');
   }

    /**
     * http post method
     *
     * @param $uri
     * @param $options
     *
     * @return array
     * @throws EurekaException
     */
    public function post(string $uri,array $options=[]):Response{
       return $this->request($uri,$options,'POST');
    }

    /**
     * http put method
     *
     * @param $uri
     * @param $options
     *
     * @return array
     * @throws EurekaException
     */
    public function put(string $uri,array $options=[]):Response {
       return $this->request($uri,$options,'PUT');
    }

    /**
     * http delete method
     *
     * @param $uri
     * @param $options
     *
     * @return array
     * @throws EurekaException
     */
    public function delete(string $uri,array $options=[]):Response {
        return $this->request($uri,$options,'DELETE');
    }

    /**
     * http options method
     *
     * @param $uri
     * @param $options
     *
     * @return array
     * @throws EurekaException
     */
    public function options(string $uri,array $options=[]):Response{
        return $this->request($uri,$options,'OPTIONS');
    }

    /**
     * http head method
     *
     * @param $uri
     * @param $options
     *
     * @return array
     * @throws EurekaException
     */
    public function head(string $uri,array $options=[]):Response {
        return $this->request($uri,$options,'HEAD');
    }

    /**
     * http patch method
     *
     * @param $uri
     * @param $options
     *
     * @return array
     * @throws EurekaException
     */
    public function patch(string $uri,array $options=[]):Response {
        return $this->request($uri,$options,'PATCH');
    }
}