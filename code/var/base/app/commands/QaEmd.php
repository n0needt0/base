<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class QaEmd extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:qaemd';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command Connect to eMds and moves its data to consumers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->services = Config::get('app.validservices');
        $this->valid_services = array_keys($this->services);
        $this->available_services = array();
        $this->service_hash=array();

        foreach($this->services as $service => $service_opts)
        {
            if(isset($service_opts['note']))
            {
                $service =  strtolower($service . ' (' . $service_opts['note'] . ')');
            }
            $this->available_services[] = strtolower($service);
            $this->service_hash[$service] = 1;
        }
    }

    public function build_schema()
    {
        //create main errors

        $table_name= 'emd_errs';

        if (!Schema::hasTable($table_name))
        {
                Schema::create($table_name, function($table)
                {
                    $table->increments('id');
                    $table->string('emd_invoice_id');
                    $table->string('ext_key');
                    $table->string('during');
                    $table->string('error');
                    $table->text('invoice');
                    $table->text('charges');
                    $table->text('payments');
                    $table->timestamps();

                     $table->unique(array('emd_invoice_id', 'during', 'error'));
                    // We'll need to ensure that MySQL uses the InnoDB engine to
                    // support the indexes, other engines aren't affected.
                    $table->engine = 'InnoDB';
                });

                //stupid schema can not create right

                $sql = "ALTER TABLE $table_name CHANGE COLUMN updated_at updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
                DB::statement($sql);
       }

       //truncate table
       DB::table('emd_errs')->delete();

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
        $this->build_schema();

        $this->errors = array();

        try{
                $invoices = DB::connection('emds')->table("VIEW_API_InvoiceIndex")->get();

                $this->info('Checking Invoices..');

                foreach($invoices as $invoice)
                {
                    $this->check_invoice($invoice);
                }

               $payments = DB::connection('emds')->table("VIEW_API_PaymentIndex")->get();

                $this->info('Checking Payments..');

                foreach($payments as $payment)
                {
                    $this->check_payment($payment);
                }

                $data = array('errors'=>$this->errors,'valid_services'=>$this->available_services);

                Mail::send('emd.emailerrors', $data, function($message)
                {
                    $message->to(Config::get('app.emails.admin'))->subject('IMPORTANT!!! QA Emd Data Results');

                    foreach(Config::get('app.emails.billing') as $billingemail)
                    {
                        $message->cc($billingemail);
                    }
                });
        }
          catch(Exception $e)
        {
                $this->error($e->getMessage());
        }
    }

    protected function translateLocation($location='na')
    {
        $locs = Config::get('app.locationmap');
        if(empty($locs) || empty($locs[$location]))
        {
            return $location;
        }
        return $locs[$location];
    }

    private function log_qa_db($invoice, $error, $during)
    {

         $data = array('emd_invoice_id'=>$invoice->InvoiceNumber_EMD, 'error'=>$error,'during'=>$during );
         $this->errors[] = array('invoice_number'=>$invoice->InvoiceNumber_EMD, 'error'=>$error);
         $this->error($invoice->InvoiceNumber_EMD . ':' . $error);

         $res = DB::table('emd_errs')->where('emd_invoice_id', $invoice->InvoiceNumber_EMD)->where('error',$error)->where('during',$during)->get();

         if(!$res)
         {
             DB::table('emd_errs')->insert( $data );
         }
    }


    protected function check_invoice($invoice)
    {
        $today = date('Ymd', time());

        if($invoice->Invoice_DAY > $today)
        {
            $this->log_qa_db($invoice,"Invoice Date is in the future",'all');
        }

        if(empty($invoice->Invoice_Comment))
        {
            $this->log_qa_db($invoice,"Service Empty",'all');
        }

        if(empty($invoice->InvoiceStatus_Code) || '----' == $invoice->InvoiceStatus_Code)
        {

            $this->log_qa_db($invoice,"Invalid Status Code \"" . $invoice->InvoiceStatus_Code . '"' ,'all');

        }


        if($invoice->PaymentTotal > $invoice->InvoiceTotal)
        {
            $this->log_qa_db($invoice, "Payment amount is greater than Invoice due amount!",'netsuite');
        }

        if(($invoice->InvoiceStatus_Code == 'PAID') && (intval($invoice->InvoiceTotal) > intval($invoice->PaymentTotal)))
        {
            $this->log_qa_db($invoice, "Invoice closed with outstanding balance. Please use write off codes" , 'warning');
        }

        if(!isset($this->service_hash[strtolower(trim($invoice->Invoice_Comment))] ) )
        {
             $this->log_qa_db($invoice, "Invalid Service: " . $invoice->Invoice_Comment , 'warning');
        }

        foreach($this->services as $k=>$v)
        if( isset($v['price_check']) && $invoice->Invoice_Comment == $k )
        {
            if($v['price_check']['operator'] == 'mod')
            {
                if($invoice->InvoiceTotal==0 || $invoice->InvoiceTotal%$v['price_check']['value'] != 0)
                {
                    $this->log_qa_db($invoice, "Invoiced for wrong service $k for $ " . number_format($invoice->InvoiceTotal,2), 'netsuite');
                }
            }
        }
    }

    protected function check_payment($payment)
    {
        $today = date('Ymd', time());

        if($payment->Payment_DayPosted_DAY > $today)
        {
            $this->log_qa_db($payment,"Payment Posted Date is in the future",'all');
        }

        if($payment->Payment_DateCheck_DAY > $today)
        {
            $this->log_qa_db($payment,"Payment Check Date is in the future",'all');
        }
    }
}