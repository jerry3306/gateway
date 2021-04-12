<?php
namespace lib\gateway;

use GatewayWorker\BusinessWorker;
use Workerman\Worker;

class ServerWinWorker
{
    private $protocols;
    private $is_reg;
    private $config;
    private $business_worker;

    /**
     * Server constructor.
     * @param string $protocols
     * @param bool $is_reg
     */
    public function __construct($protocols="websocket",$is_reg = false)
    {

        $this->initLoad();
        $this->initConfig();
        $this->initEvents();
        $this->initProtocols($protocols);
        $this->initIsReg($is_reg);

        $this->initServer();
    }


    /**
     * 载入框架自动加载文件
     */
    public function initLoad()
    {
        require_file(__DIR__ . '/vendor/autoload.php');
    }

    /**
     * 载入配置
     */
    public function initConfig()
    {
        $this->config = getConfig();
    }

    /**
     * 载入回调文件
     */
    public function initEvents()
    {
        /*if(!$this->checkOs()){
            foreach (glob(__DIR__.'/events/*.php') as $k=>$v){
                require_file($v);
            }
        }else{
            require_file(__DIR__."/events/WebSocket.php");
            require_file(__DIR__."/events/Http.php.php");
        }*/

        foreach (glob(__DIR__.'/events/*.php') as $k=>$v){
            require_file($v);
        }

    }

    /**
     * @funcName 设置协议
     * @param string $protocols
     */
    public function initProtocols($protocols="")
    {
        $this->protocols = $protocols;
    }

    /**
     * @funcName 设置是否为单注册服务类
     * @param bool $is_reg
     */
    public function initIsReg($is_reg = false)
    {
        $this->is_reg = $is_reg;
    }


    /**
     * 设置服务
     */
    public function initServer()
    {
        $this->initBusinessWorker();
    }


    /**
     * 设置worker类
     */
    public function initBusinessWorker()
    {
        $this->business_worker = new BusinessWorker();
        $this->setBusinessWorkerOptions();
        $this->runServer();
    }


    public function getLanIp()
    {
        return $this->config['protocols'][$this->protocols]['gateway_worker']['options']['lanIp'];

    }

    public function getRegisterPort()
    {
        return $this->config['protocols'][$this->protocols]['register_address']['options']['port'];
    }

    public function getRegisterAddress()
    {
        return $this->getLanIp().':'.$this->getRegisterPort();
    }


    private function setBusinessWorkerOptions()
    {
        $business_worker_options = $this->config['protocols'][$this->protocols]['business_worker']['options'];
        foreach ($business_worker_options as $k=>$v){
            $this->business_worker->{$k} = $v;
        }
        $this->business_worker->registerAddress = $this->getRegisterAddress();
    }


    public function getBusinessWorker()
    {
        return $this->business_worker;
    }


    public function runServer()
    {
        Worker::runAll();
    }

}


function require_file($path="")
{
    require_once($path);
}

function getConfig()
{
    return require('./config/gateway_worker.php');
}


new ServerWinWorker('websocket');