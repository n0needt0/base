<?php namespace Lasdorf\Es;

use \Elasticsearch\Client;

Class EsBase{
    public $sig;
    public $debug_level;

    public function __construct(){
        $this->sig = md5(time());
        $this->debug_level = false;
    }

     public function set_debug_level($level)
     {
         $this->debug_level = $level;
     }

    public function debug($str)
    {

        if($this->debug_level)
        {
           echo print_r($str,1);
        }
    }
}
