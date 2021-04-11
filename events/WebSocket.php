<?php
namespace lib\gateway\events;

use GatewayWorker\Lib\Gateway;

class WebSocket
{
    /**
     * @param $client_id
     */
    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id,json_encode([
            'type' => 'init',
            'data' => [
                'client_id' => $client_id
            ]
        ]));
    }

    /**
     * @param $client_id
     * @param $message
     */
    public static function onMessage($client_id, $message)
    {

    }

    /**
     * @param $client_id
     */
    public static function onClose($client_id)
    {

    }
}