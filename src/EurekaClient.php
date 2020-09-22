<?php
declare(strict_types=1);

namespace EurekaService;


use EurekaService\Exceptions\EurekaException;
use EurekaService\Helper\Helper;
use Swoole\Coroutine;
use Swoole\Process\Pool;
use Swoole\Process;
use Swoole\Coroutine\Scheduler;
use function register_shutdown_function;
use function error_get_last;

class EurekaClient
{

    /**
     * @var EurekaClient
    */
    private static $instance;

    /**
     * @var EurekaApi
    */
    private $eurekaApi;

    /**
     * @var  float
    */
    const VERSION="0.1";

    /**
     * @var int
    */
    private $workerNum =1;

    /**
     * @var Pool
    */
    private $pool;

    /**
     * @var string
    */
    const EUREKA_CLIENT_NAME="eureka-client-process";

    /**
     * @var string
    */
    const EUREKA_MASTER_NAME="eureka-master-process";

    /**
     * master pid file
     * @var string
    */
    const MASTER_PID_FILE='/opt/eureka_master.pid';

    /**
     * @return self
    */
    public static function getInstance():self {
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

     /**
      * worker start
      * @param $pool
      * @param $workerId
     */
     private function workerStart($pool,$workerId){
         //set process name
         swoole_set_process_name(self::EUREKA_CLIENT_NAME);
         //handle error & signal
         $this->handleShutdownAndSignal();
         //register services
         $this->eurekaApi->register();
         //heartbeat
         $this->eurekaApi->heartbeat();
         //fetch all services
         $this->eurekaApi->instances();
     }

     /**
      * worker stop
      * @param $pool
      * @param $workerId
     */
     private function workerStop($pool,$workerId){
         //deregister
         \Co\run(function(){
             $res = $this->eurekaApi->deregister();
             Helper::getLogger()->info("eureka-client-stop-result is ".var_export($res,true));
         });
     }

    /**
     * handle shutdown and signal
     */
    private function handleShutdownAndSignal(){
        //register shutdown handler
        register_shutdown_function(function(){
            $errors = error_get_last();
            if($errors && (
                    $errors['type'] === \E_ERROR ||
                    $errors['type'] === \E_PARSE ||
                    $errors['type'] === \E_CORE_ERROR ||
                    $errors['type'] === \E_COMPILE_ERROR ||
                    $errors['type'] === \E_RECOVERABLE_ERROR
                )){
                $mess = $errors['message'];
                $file = $errors['file'];
                $line = $errors['line'];
                $errMsg = "error occured :".$errors["type"].":".$mess."in ".$file."on the ".$line;
                Helper::getLogger()->error($errMsg);
            }
        });
        //handler signal
        Process::signal(SIGTERM,function($signo){
            $this->eurekaApi->setRunningStatus(false);
        });
    }
    /**
     * run eureka client
     *
     * @param $eurekaAPi
     *
     * @return void
     * @throws EurekaException
    */
    public function run(EurekaAPi $eurekaAPi){
        try{
            //Daemon
            Process::daemon(false,false);
            //int pool
            $this->pool = new Pool($this->workerNum,0,0,true);
            //set coroutine config
            Coroutine::set([
               'hook_flags' => SWOOLE_HOOK_ALL,
               'max_coroutine' =>30000,
               'stack_size' => 4096,
               'log_level'  =>SWOOLE_LOG_ERROR,
               'socket_connect_timeout' => 10,
               'socket_timeout' =>10,
               'dns_server' =>'8.8.8.8',
               'exit_condition' => function(){
                   return Coroutine::stats()['coroutine_num'] === 0;
               }
           ]);
           $this->eurekaApi = $eurekaAPi;
            //set master process name
            swoole_set_process_name(self::EUREKA_MASTER_NAME);
            //save master pid to file
            file_put_contents(self::MASTER_PID_FILE,getmypid());
            //set handler
           $this->pool->on('WorkerStart',function($pool,$workerId){
               $this->workerStart($pool,$workerId);
           });
           $this->pool->on("WorkerStop",function($pool,$workerId){
               $this->workerStop($pool,$workerId);
           });
           $this->pool->start();
        }catch (EurekaException $ex){
            Helper::getLogger()->error("eureka-client-exception is ".$ex->getMessage());
        }catch (\Throwable $ev){
            Helper::getLogger()->error("eureka-client-exception is ".$ev->getMessage());
        }
    }
}