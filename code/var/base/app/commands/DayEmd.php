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
        $remotefile =  '/home/ftpuser/HELP' . date('Ymd') . '.zip';

        $zip = new ZipArchive;

        if ($zip->open($zipfile) === TRUE) {


            $handle = opendir($dir);
            while (false !== $f = readdir($handle)) {
              if ($f != '.' && $f != '..') {
                $filePath = "$dir/$f";
                // Remove prefix from file path before add to zip.
                $localPath = substr($filePath, $exclusiveLength);
                if (is_file($filePath)) {
                  $zipFile->addFile($filePath, $localPath);
                }
              }
            }
            closedir($handle);


            $zip->close();

            //scp to server

            $connect = ssh2_connect('54.68.229.123', 22);
            ssh2_auth_pubkey_file($connect, 'ftpuser', './ftpuser.pem');
            ssh2_scp_send($connect, $zipfile, $remotefile, 0644);

        } else {
             $this->info("DEBUG: zip failed" );
             exit(1);
        }




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