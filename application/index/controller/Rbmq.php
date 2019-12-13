<?php
namespace app\index\controller;

use think\Controller;
use think\Config;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class Rbmq extends Controller
{
    public $channel;
    private static $instance = null;

    public function __construct()
    {
        $conf = Config::get('rbmq');
        $conn = new AMQPStreamConnection( //建立生产者与mq之间的连接
            $conf['host'], $conf['port'], $conf['user'], $conf['pwd'], $conf['vhost']
        );
        $channel = $conn-> channel();

        $this->channel = $channel;
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            //如果没有,则创建当前类的实例
            self::$instance = new self();
        }
        //如果已经有了当前类实例,就直接返回,不要重复创建类实例
        return self::$instance;
    }
}
