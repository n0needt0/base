<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Lasdorf\Es\EsApi as EsApi;
use Lasdorf\MailHard\MailHard as MailHard;

class DayEs extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:dayes';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command pull all Help data into Elastic search';

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
             $es = new EsApi();
             $res = $es->delete_index('datastructures');
             $res = $es->emds_struct_to_es('datastructures');
             $res = $es->add_trac_to_es('ptrac', 'datastructures');
             $res = $es->add_trac_to_es('billtrac', 'datastructures');
             $res = $es->add_trac_to_es('ctrac', 'datastructures');

        }
        catch(Exception $e)
        {
            $this->error($e->getMessage());
        }
    }

}