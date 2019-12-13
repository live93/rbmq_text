<?php
namespace app\index\controller;

use think\Controller;
use PhpAmqpLib\Message\AMQPMessage;
class SubscribeProducer extends Controller
{
    public $channel;

    public function __construct()
    {

        $instance = Rbmq::getInstance();
        $this -> channel = $instance -> channel;
    }

    public function exchange($exchangeName='')
    {
        $this -> exchangeName = $exchangeName;
            //声明交换机
        $this -> channel -> exchange_declare($this -> exchangeName,'fanout',false,false,false);
        return $this;
    }
    public function msg($msg)
    {
        $this -> msg = new AMQPMessage($msg);
        return $this;
    }
    public function push()
    {
        $this -> channel -> basic_publish($this -> msg,$this -> exchangeName);
        return $this;
    }

}
