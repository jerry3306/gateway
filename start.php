<?php
namespace lib\gateway;

use Workerman\Worker;

require_once 'Server.php';

function main()
{
    $prol = ['websocket','http'];
    foreach ($prol as $v){
        new Server($v,false);
    }

    Worker::runAll();
}

main();