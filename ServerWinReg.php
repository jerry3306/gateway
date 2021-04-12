<?php
namespace lib\gateway;

use GatewayWorker\Register;
use Workerman\Worker;

class ServerWinReg
{
    private $protocols;
    private $is_reg;
    private $config;
    private $register;

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
        $this->initReg();
    }

    /**
     * 设置注册服务类
     */
    public function initReg()
    {
        $this->register = new Register($this->getRegisterSocketName());
        $this->runServer();
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

new ServerWinReg('websocket');