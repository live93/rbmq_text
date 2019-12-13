<?php
namespace app\index\controller;
use think\Controller;
use Elasticsearch\ClientBuilder;

class Search extends Controller
{
    public function index()
    {
        return $this->fetch();
    }
    public function getData()
    {
        $request = input('get.');
        $keyword = $request['q'];
        $hosts = [
            '192.168.126.128:9200',         // IP + Port
            '192.168.126.128:9201',         // IP + Port
        ];
        $client = ClientBuilder::create()           // Instantiate a new ClientBuilder
        ->setHosts($hosts)      // Set the hosts
        ->setConnectionPool('\Elasticsearch\ConnectionPool\StaticConnectionPool', [])
            ->build();

        $params = [
            'index' => 'myindex2',
            'type' => 'article',
            'body' => [
                'from' => 0,
                'size' => 10,
                'query' => [
                    'match' => [
                        'title' => $keyword
                    ]
                ],
                'highlight' => [
                    'pre_tags' => ["<em>"],
                    'post_tags' => ["</em>"],
                    'fields' => [
                        "title" => new \stdClass()
                    ]
                ]
            ]
        ];
        $response = $client->search($params);
        exit(json_encode($response['hits']));
    }
    public function testSearch()
    {
        $hosts = [
            '192.168.126.128:9200',         // IP + Port
        ];
        $client = ClientBuilder::create()           // Instantiate a new ClientBuilder
        ->setHosts($hosts)      // Set the hosts
            ->build();
        $params = [
            'scroll' => '30s',          // how long between scroll requests. should be small!
            'size'   => 980,             // how many results *per shard* you want back
            'index'  => 'my_index',
            'body'   => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];
        $response = $client->search($params);
//        dump($response);
        $i = 0;
        while (isset($response['hits']['hits']) && count($response['hits']['hits']) > 0) {
            $scroll_id = $response['_scroll_id'];
            $response = $client->scroll([
                    'scroll_id' => $scroll_id,  //...using our previously obtained _scroll_id
                    'scroll'    => '30s'        // and the same timeout window
                ]
            );
            $i++;
            if($i==5){
                die;
            }else{
                dump($response);
            }

        }
    }
    public function test(){
        $hosts = [
            '192.168.126.128:9200',         // IP + Port
        ];
        $client = ClientBuilder::create()           // Instantiate a new ClientBuilder
        ->setHosts($hosts)      // Set the hosts
        ->build();
        $params['body'] = [];
        $nameArr = ['赵','钱','孙','李','周','吴','郑','王'];
        for ($i = 1; $i <= 1000; $i++) {
            $params['body'][] = [
                'create' => [   #创建
                    '_index' => 'my_index',
                    '_type' => 'my_type',
                    '_id' => $i,
                    'routing' => mt_rand(1, 100),
                ]
            ];
            $params['body'][] = [
                'name' => $nameArr[mt_rand(0, 7)],
                'age' => mt_rand(18, 60)
            ];
        }

        $res = $client->bulk($params);
    }

}
