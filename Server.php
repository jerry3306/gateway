<?php
namespace lib\gateway;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Worker;

class Server
{
    private $protocols;
    private $is_reg;
    private $config;
    private $gateway_worker;
    private $register;
    private $business_worker;

    /**
     * Server constructor.
     * @param string $protocols
     * @param bool $is_reg
     */
    public function __construct($protocols="websocket",$is_reg = false)
    {
        $this->check();
        $this->initLoad();
        $this->initConfig();
        $this->initEvents();
        $this->initProtocols($protocols);
        $this->initIsReg($is_reg);

        $this->initServer();
    }

    public function check()
    {
        if(!$this->checkOs()){
            if(!$this->checkPcntlExt()){
                exit('Please install pcntl extension. See http://doc3.workerman.net/appendices/install-extension.html'."\n");
            }

            if(!$this->checkPosixExt()){
                exit('Please install posix extension. See http://doc3.workerman.net/appendices/install-extension.html'."\n");
            }
        }
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
       /* if(!$this->checkOs()){
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
        if($this->is_reg){
            $this->initReg();
        }else{
            $this->initReg();
            $this->initGateWay();
            $this->initBusinessWorker();
        }
    }

    /**
     * 设置注册服务类
     */
    public function initReg()
    {
        $this->register = new Register($this->getRegisterSocketName());
        if($this->checkOs()){
            $this->runServer();
        }
    }


    /**
     * 设置网关类
     */
    public function initGateWay()
    {
        $this->gateway_worker = new Gateway($this->getGateWaySocketName());
        $this->setGateWayOptions();
        $this->setGateWayCallBacks();

        if($this->checkOs()){
            $this->runServer();
        }
    }

    /**
     * 设置worker类
     */
    public function initBusinessWorker()
    {
        $this->business_worker = new BusinessWorker();
        $this->setBusinessWorkerOptions();

        if($this->checkOs()){
            $this->runServer();
        }
    }

    public function getRegisterSocketName()
    {
        return $this->getRegisterProtocols()."://".$this->getLanIp().':'.$this->getRegisterPort();
    }

    public function getRegisterProtocols()
    {
        return $this->config['protocols'][$this->protocols]['register_address']['options']['protocols'];
    }

    public function getLanIp()
    {
        return $this->config['protocols'][$this->protocols]['gateway_worker']['options']['lanIp'];

    }

    public function getRegisterPort()
    {
        return $this->config['protocols'][$this->protocols]['register_address']['options']['port'];
    }

    public function getGateWayIp()
    {
        return $this->config['protocols'][$this->protocols]['gateway_worker']['options']['collection']['ip'];
    }

    public function getGateWayPort()
    {
        return $this->config['protocols'][$this->protocols]['gateway_worker']['options']['collection']['port'];
    }

    public function getGateWaySocketName()
    {
        return $this->protocols."://".$this->getGateWayIp().':'.$this->getGateWayPort();
    }

    public function getRegisterAddress()
    {
        return $this->getLanIp().':'.$this->getRegisterPort();
    }

    private function setGateWayOptions()
    {
        $gateway_options = $this->config['protocols'][$this->protocols]['gateway_worker']['options'];
        foreach ($gateway_options as $k=>$v){
            if($k!='collection'){
                $this->gateway_worker->{$k} = $v;
            }
        }

        $this->gateway_worker->registerAddress = $this->getRegisterAddress();
    }


    private function setGateWayCallBacks()
    {
        $this->gateway_worker->onConnect = [$this,'gateWayonConnect'];
    }

    public function gateWayonConnect($connection)
    {

    }


    private function setBusinessWorkerOptions()
    {
        $business_worker_options = $this->config['protocols'][$this->protocols]['business_worker']['options'];
        foreach ($business_worker_options as $k=>$v){
            $this->business_worker->{$k} = $v;
        }
        $this->business_worker->registerAddress = $this->getRegisterAddress();
    }


    public function getGateWayWorker()
    {
        return $this->gateway_worker;
    }

    public function getBusinessWorker()
    {
        return $this->business_worker;
    }


    public function runServer()
    {
        Worker::runAll();
    }


    /**
     * @funcName 检查系统环境
     * @return bool
     */
    public function checkOs()
    {
        return strpos(strtolower(PHP_OS), 'win')===0;
    }

    /**
     * @funcName 检查pcntl拓展
     * @return bool
     */
    public function checkPcntlExt()
    {
        return extension_loaded('pcntl');
    }

    /**
     * @funcName 检查posix拓展
     * @return bool
     */
    public function checkPosixExt()
    {
        return extension_loaded('posix');
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