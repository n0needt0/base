<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Lasdorf\EmdApi\EmdApi as EmdApi;

class DayEmd extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:dayemd';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Connect to eMds and analyzes its daily data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function debug($msg)
    {
        if(Config::get('app.debug'))
        {
            $this->info("DEBUG: $msg" );
        }
    }

    public function build_schema()
    {
        return;
    }


        /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $env = $this->option('env');

        if(empty($env))
        {
            $this->error('--env option required! local|production');
            die;
        }

        try{

            $res = EmdApi::get_filtered( array('payment_ar::'), 'WEEK');
            $ar_buckets = Config::get('dailyemd.billing.ar_buckets');

            $FD = ",";
            $RD = "\n";
            $PRF = 'payment_ar::';
            $Q = '"';

            $str = $Q . 'MONTH' . $Q . $FD;

            foreach($ar_buckets as $k=>$v)
            {
                $str .= $Q . $v . $Q . $FD;
            }

            $str = trim($str,$FD);

            echo $str . $RD;

            foreach($res as $month=>$data)
            {
                $line = $Q . $month . $Q . $FD ;

                foreach($ar_buckets as $k=>$v)
                {
                    if(!empty($data[$PRF . $v]))
                    {
                        $line .= $Q . $data[$PRF . $v]['val'] . $Q . $FD;
                    }
                      else
                    {
                        $line .= $Q . '0' . $Q . $FD;
                    }
                }

                $line = trim($line,$FD);
                echo $line . $RD;
            }

            //now lets do history data from already stored results


        }
        catch(Exception $e)
        {
            $this->error($e->getMessage());
        }
    }

}