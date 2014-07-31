<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Lasdorf\EmdApi\ApiEmdCalendar as ApiEmdCalendar;

class EmdCalendar extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:emdcalendar';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Moves Emd Calendar';

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

                ApiEmdCalendar::get_calendar();

        }
        catch(Exception $e)
        {
            $this->error($e->getMessage());
        }
    }

}