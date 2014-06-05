<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class QaEmdDaisy extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:qaemddaisy';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Connect to eMds and Compares its billing to that of Daisy bill';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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

        $this->errors = array();

        /**
         * get all emd invoices put index in array
         * get all daisy invoices put index in array
         * there only one common element in both emd invoice number, thats index
         * find array diff att to array
         * find array union -> do qa
         * file qa errors
         */
        $daisy = $emd = $emdindaisy=array();
        $skipemd = array('----'=>1,'NEW'=>1,'FILLED'=>1);

        try{

                $this->info('Build Index for Daisy');
                $daisy_invoices = DB::table("daisybill")->get();

                foreach($daisy_invoices as $daisy_invoice)
                {
                    $daisy[$daisy_invoice->bill_id] = array( 'emd_id'=>$daisy_invoice->practice_bill_id,
                                                                                'total'=>$daisy_invoice->charge_amount,
                                                                                'date'=>date('Ymd',$daisy_invoice->created_on),
                                                                                'patient'=>$daisy_invoice->patient_last_name . ', ' . $daisy_invoice->patient_first_name);
                    $emdindaisy[$daisy_invoice->practice_bill_id] = 1;
                }

                $emd_invoices = DB::connection('emds')->table("VIEW_API_InvoiceIndex")->get();
                $this->info('build index for emd');

                foreach($emd_invoices as $emd_invoice)
                {
                    if( !empty($skipemd[$emd_invoice->InvoiceStatus_Code]))
                    {
                        continue;
                    }
                    $emd[$emd_invoice->InvoiceNumber_EMD] = array('total'=>$emd_invoice->InvoiceTotal, 'date'=>$emd_invoice->Invoice_DAY, 'patient'=>$emd_invoice->PatientName);

                    if(!isset($emdindaisy[$emd_invoice->InvoiceNumber_EMD]))
                    {
                        $this->errors[] = "EMD # " . $emd_invoice->InvoiceNumber_EMD . " is NOT in Daisy";
                    }
                }

                //find common elements

                foreach($daisy as $k=>$v)
                {
                    if(!empty($v['emd_id']))
                    {
                        if( isset($emd[$v['emd_id']]) )
                        {
                             //match do basc qa
                             if(strtolower($emd[$v['emd_id']]['patient']) != strtolower($v['patient']))
                             {
                                 //patient name dont match
                                 $this->errors[] = "Patient name dont match between Daisy # $k, and EMD #" . $v['emd_id'] ;
                             }

                             if(strtolower($emd[$v['emd_id']]['total']) != strtolower($v['total']))
                             {
                                 //totals dont match
                                 $this->errors[] = "Total amount dont match between Daisy # $k, and EMD #" . $v['emd_id'] ;
                             }

                             if(strtolower($emd[$v['emd_id']]['date']) != strtolower($v['date']))
                             {
                                 //dates dont match
                                 $this->errors[] = "Dates dont match between Daisy # $k, and EMD #" . $v['emd_id'] ;
                                 //TODO update emd to daisy date
                             }
                        }
                          else
                        {
                             //something is wrong emd number does not match
                             $this->errors[] = "Daisy Invoice# $k has Invalid EMD field";
                        }
                    }
                     else
                    {
                        //log daisy # has no corresponding emd #
                        $this->errors[] = "Daisy Invoice# $k has empty EMD field";
                    }
                }


                print_r($this->errors);
                die;

                //see if all emd bills are in daisy
                foreach($emd as $k=>$v )

                $data = array('errors'=>$this->errors);

                Mail::send('emd.daisyerrors', $data, function($message)
                {
                    $message->to(Config::get('app.emails.admin'))->subject('QA Emd Daisy Results');

                    //foreach(Config::get('app.emails.billing') as $billingemail)
                    //{
                        //$message->cc($billingemail);
                    //}
                });
        }
          catch(Exception $e)
        {
                $this->error($e->getMessage());
        }
    }
}