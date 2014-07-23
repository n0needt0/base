<?php namespace Lasdorf\Es;

use \Elasticsearch\Client;

Class EsBase{
    public $sig;

    public function __construct(){
        $this->sig = md5(time());
    }

    public function debug($str)
    {
        echo print_r($str,1);
    }
}
