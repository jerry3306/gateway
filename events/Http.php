<?php
namespace lib\gateway\events;

use GatewayWorker\Lib\Gateway;

class Http
{
    /**
     * @param $client_id
     */
    public static function onConnect($client_id)
    {
        Gateway::sendToCurrentClient("Your client_id is $client_id");
    }

    /**
     * @param $client_id
     * @param $message
     */
    public static function onMessage($client_id, $message)
    {
       return Gateway::sendToAll($message);
    }

    /**
     * @param $client_id
     */
    public static function onClose($client_id)
    {

    }
}