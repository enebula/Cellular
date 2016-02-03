<?php
/**
 *
 * Cellular Framework
 * Mongo 封装
 *
 * @author Cloud 66999882@qq.com
 * @version 1.0 2016-02-03
 *
 */
namespace core;

class Mongo {

    public function __construct($config) {
        $this->connect();
    }

    /**
     * 连接数据库
     */
    private function connect() {
      $config = Cellular::loadFile('config/mongo.php');
      $mongo = new MongoClient('mongodb://' . $config['host'] . ':' . $config['port']);
  		return $mongo;
    }

}
?>
