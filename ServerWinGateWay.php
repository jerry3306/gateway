<?php
namespace lib\gateway;

use GatewayWorker\Gateway;
use Workerman\Worker;

class ServerWinGateWay
{
    private $protocols;
    private $is_reg;
    private $config;
    private $gateway_worker;

    /**
     * Server constructor.
     * @param string $protocols
     * @param bool $is_reg
     */
    public function __construct($protocols="websocket",$is_reg = false)
    {

        $this->initLoad();
        $this->initConfig();
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
        $this->initGateWay();
    }

    /**
     * 设置网关类
     */
    public function initGateWay()
    {
        $this->gateway_worker = new Gateway($this->getGateWaySocketName());
        $this->setGateWayOptions();
        $this->setGateWayCallBacks();

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


    public function getGateWayWorker()
    {
        return $this->gateway_worker;
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

new ServerWinGateWay('websocket');