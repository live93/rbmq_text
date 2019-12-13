<?php
namespace app\index\controller;

use think\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Rbmq extends Controller
{
    public function index()
    {
        $conf = array(
            'host' => '47.93.245.233',
            'vhost' => 'vhost_hc',
            'port' => 5672,
            'user' => 'user_hc',
            'pwd' => '123456'
        );
        $exchangeName = 'kd_sms_send_ex'; //交换机名
        $queueName = 'send_test_0906'; //队列名称
        $routingKey = 'logs'; //路由关键字(也可以省略)

        $conn = new AMQPStreamConnection( //建立生产者与mq之间的连接
            $conf['host'], $conf['port'], $conf['user'], $conf['pwd'], $conf['vhost']
        );
        $channel = $conn->channel();
        $channel-> exchange_declare($routingKey, 'fanout', false, false, false);          //交换机
        list($queue_name,,) = $channel-> queue_declare($queueName, false, false, false, false);
        dump($queue_name);
        $msg2 = new AMQPMessage('Hello World!---2');
        $channel->basic_publish($msg2, $routingKey);         //发送到路由

//        $channel->queue_declare($queueName1, false, false, false, false);
//        queue：这没什么好说的，队列名
//
//        durable：是否持久化，那么问题来了，这是什么意思？持久化，指的是队列持久化到数据库中。在之前的博文中也说过，如果RabbitMQ服务挂了怎么办，队列丢失了自然是不希望发生的。持久化设置为true的话，即使服务崩溃也不会丢失队列
//
//        exclusive：是否排外，what？ 这又是什么呢。设置了排外为true的队列只可以在本次的连接中被访问，也就是说在当前连接创建多少个channel访问都没有关系，但是如果是一个新的连接来访问，对不起，不可以，下面是我尝试访问了一个排外的queue报的错。还有一个需要说一下的是，排外的queue在当前连接被断开的时候会自动消失（清除）无论是否设置了持久化
//
//        autoDelete：这个就很简单了，是否自动删除。也就是说queue会清理自己。但是是在最后一个connection断开的时候
//
//        arguments：这个值得拿出来单讲一次，暂时不说
//
        sleep(10);
        $channel-> queue_bind($queue_name,'logs');

        $msg = new AMQPMessage('Hello World!---1');
//        $channel->basic_publish($msg, '', $queueName);      //发送到队列
        $channel->basic_publish($msg, $routingKey);         //发送到路由

        $channel->close();
        $conn->close();
    }
}
