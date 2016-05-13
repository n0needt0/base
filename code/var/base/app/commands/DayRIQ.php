<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Lasdorf\EmdApi\EmdApi as EmdApi;

class DayRIQ extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:dayriq';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Connect to eMds and scps to reviq';

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

            $tables_to_export = Config::get('dailyemd.revenueiq.include_tables');

             $dir='/tmp/revenueiq/';
             $md5file = $dir . "checklist.md5";
             if (file_exists($md5file))
             {
               unlink($md5file);
             }

            if (!is_dir($dir)){
                mkdir($dir, 0700);
            }else{
                //clean
                array_map('unlink', glob("$dir*"));
            }

            foreach ($tables_to_export as $table) {
                $f = EmdApi::save_table_to_csv($table, $dir);
                $cnt = "";

                if (file_exists($md5file))
                {
                    $cnt = file_get_contents($md5file);
                }

                $cnt .= $f . "\t" . md5_file($f) . "\n";
                file_put_contents($md5file, $cnt);

                $this->info("DEBUG: $table done" );
            }

            //zip
          $zipfile = '/tmp/HELP' . date('Ymd') . '.zip';

           //create zp file
           $ret =exec("zip -r $zipfile $dir");
           $this->DEBUG($ret );

           if (file_exists($zipfile))
           {
//scp to server
            $this->DEBUG("sending file" );

            $ret = exec("scp -i /var/www/base/app/commands/ftpuser.pem $zipfile ftpuser@54.68.229.123:/home/ftpuser/");
            $this->DEBUG($ret );

           }else{
               $this->ERROR("Zip failed" );
           }
        }
        catch(Exception $e)
        {
            $this->error($e->getMessage());
        }
    }

}