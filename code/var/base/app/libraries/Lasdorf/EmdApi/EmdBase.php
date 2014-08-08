<?php namespace Lasdorf\EmdApi;

use Illuminate\Support\Facades\Config as Config;

Class EmdBase{
    public function __construct(){

    }

    static public function debug ($str)
    {
        if(Config::get('app.debug'))
        {
            \Log::info($str);
        }
    }
}

