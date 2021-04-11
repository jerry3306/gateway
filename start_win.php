<?php
namespace lib\gateway;

require_once 'Server.php';

function main()
{
    $prol = ['websocket','http'];
    foreach ($prol as $v){
        new Server($v,false);
    }
}

main();