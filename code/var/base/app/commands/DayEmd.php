<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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
        //create main errors
        $table_name= 'emd_daily';

        DB::statement("truncate $table_name");

        return;

        Schema::drop($table_name);

        if (!Schema::hasTable($table_name))
        {
                Schema::create($table_name, function($table)
                {
                    $table->increments('id');
                    $table->integer('interval');
                    $table->string('interval_type');
                    $table->longtext('billed');
                    $table->longtext('paid');
                    $table->longtext('ar');
                    $table->timestamps();
                    $table->unique(array('interval'));
                    // We'll need to ensure that MySQL uses the InnoDB engine to
                    // support the indexes, other engines aren't affected.
                    $table->engine = 'InnoDB';
                });

                //stupid schema can not create right

                $sql = "ALTER TABLE $table_name CHANGE COLUMN updated_at updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
                DB::statement($sql);
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
        $this->build_schema();

        $this->day_splits = array(-8,31,61,91,121,181,366,730,1500); //this is buckets for ar
        //negative is sto compnsate week vconvesion dates



        try{

            foreach(array('DAY','WEEK') as $k=>$v)
            {
                //create fresh copy of invoices
                $this->invoices = $this->get_invoices_meta();

                $intervals = $this->get_intervals($v);
                //foreach day bucket
                foreach($intervals as $interval=>$v)
                {
                    //set empty data slice
                    $this->info("$interval...start");
                    $data = array();

                    $data['interval'] = $interval;
                    $data['interval_type'] = $this->itype($interval);

                    $data['billed'] = $this->get_interval_billing($interval);
                    $this->debug('billed');
                    $data['paid'] = $this->get_interval_payment($interval);
                    $this->debug('paid');
                    $data['ar'] = $this->get_interval_ar($interval);
                    $this->debug('ar');
                    $this->info("$interval...end");
                   $this->store_interval($data);
                }
            }

        }
          catch(Exception $e)
        {
                $this->error($e->getMessage());
        }
    }

    protected function store_interval($data){
        $interval = "interval:missing";

        if(!empty($data['interval']))
        {
            $interval = $data['interval'];
        }

        print_r($data);

        try{
            $data['billed'] = json_encode($data['billed']);
            $data['paid'] = json_encode($data['paid']);
            $data['ar'] = json_encode($data['ar']);
            DB::table('emd_daily')->insert($data);
        }catch(Exception $e){

            $this->error('for interval: $interval' . $e->getMessage());
        }
    }

    protected function get_intervals($interval='DAY',$buckets = array())
    {

        $pk = "Payment_$interval";
        $ik = "Invoice_$interval";

        //get all dates from payments and Invoices
        $res = DB::connection('emds')->table("VIEW_API_PaymentIndex")->get();
        foreach($res as $r){

            $buckets[intval($r->$pk)] = 1;
        }

        $res = DB::connection('emds')->table("VIEW_API_InvoiceIndex")->get();

        foreach($res as $r)
        {
            $buckets[intval($r->$ik)] = 1;
        }

        foreach($buckets as $k=>$v)
        {
            if (empty($k))
            {
                unset( $buckets[$k] );
            }
        }

        ksort($buckets);

        return $buckets;
    }

    protected function itype($interval)
    {
        if(strlen($interval) > 6) //this is week
        {
            return 'DAY';
        }

        return 'WEEK';
    }

    protected function fix_ins_name($name)
    {
        //uppercase it
        $name = strtoupper($name);

        $npart = explode(' ', $name);

        if($npart[0] == 'THE')
        {
            return urlencode($npart[1]);
        }

        return urlencode($npart[0]);
    }

    protected function add(&$array, $key, $value, $origin)
    {
        if(empty($array[$key])){
            $array[$key] = array('val'=>0,'origin'=>array());
        }
        $array[$key]['val'] += $value;
        if(!empty($this->invoices[$origin]))
        $array[$key]['origin'][] = $this->invoices[$origin]['emd'];
        return;
    }

    protected function get_interval_billing($interval)
    {
        $it = $this->itype($interval);

        $data = array();

        //this function gets stats for billing dimension
        $res = DB::connection('emds')
                    ->table('VIEW_API_InvoiceIndex')
                    ->where('Invoice_' . $it , $interval)
                    ->whereIn('InvoiceStatus_Code', array('FILED','DAISY','PAR','PAID'))
                    ->get();

        foreach($res as $r)
        {
            //total by location
            $this->add($data, 'billed::total', $r->InvoiceTotal, $r->Invoice_ID);

            //total by location
            $this->add($data, 'billed_location::' . urlencode($this->translateLocation($r->Organization_Name)), $r->InvoiceTotal, $r->Invoice_ID);

            //total by status
            $this->add($data, 'billed_status::' . $r->InvoiceStatus_Code, $r->InvoiceTotal, $r->Invoice_ID);

            //total by insurance
            $this->add($data, 'billed_insurance::' . $this->fix_ins_name($r->InsuranceCompName), $r->InvoiceTotal, $r->Invoice_ID);

            //total by provider
            $this->add($data, 'billed_provider::' . urlencode($r->ProviderName), $r->InvoiceTotal, $r->Invoice_ID);

            //total by service
            $this->add($data, 'billed_service::' . urlencode($r->Invoice_Comment), $r->InvoiceTotal, $r->Invoice_ID);
        }
        return $data;
    }

    protected function get_offset($it, $interval)
    {

        if($it == 'DAY'){
            //rebuild date back to time YYYYMMDD = Ymd format
            $ctime = mktime(0, 0, 0, substr($interval, 4,2), substr($interval, 6,2), substr($interval, 0,4));
            $this->debug( "offset to date " . date("Y-m-d", $ctime) );
            $offset = floor( (time() - $ctime) / 86400);
            if($offset < 0 )
            {
                $offset = 0;
            }
        }

        //week cutoff is keys as friday
        if($it == 'WEEK')
        {
            $this->debug( " str $interval in ");

            //make ISO week
            $s = substr($interval, 0,4);

            $e = substr($interval, 4 );
            if(strlen($e) == 1)
            {
                //padd it
                $e = '0' . $e;
            }

            $interval = $s . "W" . $e;
            $this->debug( " str $interval out ");

            $ctime = strtotime($interval) + 4*24*3600;

            $this->debug( "offset to date " . date("Y-m-d", $ctime) );
            $offset = floor( (time() - $ctime) / 86400);

            if($offset < 0 )
            {
                $offset = 0;
            }
        }
        return $offset;
    }

    protected function get_interval_to_day($it, $interval)
    {

        if($it == 'DAY')
        {
            return $interval; //nothing to do
        }

        //week cutoff is keys as friday
        if($it == 'WEEK')
        {
            $this->debug( " str $interval in ");

            //make ISO week
            $s = substr($interval, 0,4);

            $e = substr($interval, 4 );
            if(strlen($e) == 1)
            {
                //padd it
                $e = '0' . $e;
            }

            $interval = $s . "W" . $e;
            $this->debug( " str $interval out ");

            $ctime = strtotime($interval) + 4*24*3600;

            $this->debug( "offset to date " . date("Y-m-d", $ctime) );
            return date("Ymd", $ctime);
        }
    }

/*
* Each day we receive payment from various buckets
*/
    protected function get_interval_payment($interval)
    {
        $it = $this->itype($interval);

        //all invoices date have day old value ad date diff in days from today
        //we need to get offset to calculate relative days old from interval date right side exclusive
        //as dayold = invoicedateold - interval

        $offset = $this->get_offset($it, $interval);

        $data = array();

        //this function gets stats for billing dimension
        $res = DB::connection('emds')
                    ->table('VIEW_API_PaymentIndex')
                    ->where('Payment_' . $it , $interval)
                    ->get();

        foreach($res as $r)
        {
            //first lets find total outstanding on that day
            $this->add($data, 'posted::total', $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID);
            $this->add($data, 'payment::total', $r->Payment_Payment, $r->Invoice_ID);
            $this->add($data, 'adjustment::total', $r->Payment_Adjustment, $r->Invoice_ID);

            //calculate running totals for use in AR calculations
            if(!empty($this->invoices[$r->Invoice_ID]))
            {
                    $this->invoices[$r->Invoice_ID]['running_paid'] += $r->Payment_Payment;
                    $this->invoices[$r->Invoice_ID]['running_adj'] += $r->Payment_Adjustment;
            }

            //now each of these three items can be sliced in various dimensions by looking at actual underlaying invoice
            if($meta = $this->get_meta($r->Invoice_ID))
            {
                //add location

                $this->add($data, 'posted_location::' . $meta['location'], $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID);
                $this->add($data, 'payment_location::' . $meta['location'], $r->Payment_Payment, $r->Invoice_ID);
                $this->add($data, 'adjustment_location::' . $meta['location'], $r->Payment_Adjustment, $r->Invoice_ID);

                //add provider
                $this->add($data, 'posted_provider::' . $meta['provider'], $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID);
                $this->add($data, 'payment_provider::' . $meta['provider'], $r->Payment_Payment, $r->Invoice_ID);
                $this->add($data, 'adjustment_provider::' . $meta['provider'], $r->Payment_Adjustment, $r->Invoice_ID);

                //add service
                $this->add($data, 'posted_service::' . $meta['service'], $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID);
                $this->add($data, 'payment_service::' . $meta['service'], $r->Payment_Payment, $r->Invoice_ID);
                $this->add($data, 'adjustment_service::' . $meta['service'], $r->Payment_Adjustment, $r->Invoice_ID);

                //add insurance
                $this->add($data, 'posted_insurance::' . $meta['insurance'], $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID);
                $this->add($data, 'payment_insurance::' . $meta['insurance'], $r->Payment_Payment, $r->Invoice_ID);
                $this->add($data, 'adjustment_insurance::' . $meta['insurance'], $r->Payment_Adjustment, $r->Invoice_ID);

                //add provider
                $this->add($data, 'posted_provider::' . $meta['provider'], $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID);
                $this->add($data, 'payment_provider::' . $meta['provider'], $r->Payment_Payment, $r->Invoice_ID);
                $this->add($data, 'adjustment_provider::' . $meta['provider'], $r->Payment_Adjustment, $r->Invoice_ID);

                //add from ar bucket
                foreach($this->day_splits as $k=>$v)
                {
                    if($meta['old'] - $offset < $v)
                    {
                        //goes from that bucket
                        //$this->debug( "interval is $interval invoice is:: " . $meta['old'] . " on " . $meta['day'] . " offset is $offset posting to $v");
                        //add ar
                        $this->add($data, 'posted_ar::' . $this->day_splits[$k-1] . '-' . $v , $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID);
                        $this->add($data, 'payment_ar::' . $this->day_splits[$k-1] . '-' . $v, $r->Payment_Payment, $r->Invoice_ID);
                        $this->add($data, 'adjustment_ar::' . $this->day_splits[$k-1] . '-' . $v, $r->Payment_Adjustment, $r->Invoice_ID);
                        break;
                    }
                }
            }

            //from ar bucket

        }
        return $data;
    }

    protected function get_meta($invoice)
    {
            if(!empty($this->invoices[$invoice]))
            {
                return $this->invoices[$invoice];
            }

            return false;
    }


    /*
     * AR Data is pretty simple, every pass we run through $this->invoices, using only its invoices older than current range
     */
    protected function get_interval_ar($interval)
    {
        $it = $this->itype($interval);
        $offset = $this->get_offset($it, $interval);

        $data = array();

         //now each of these items can be sliced in various dimensions by looking at actual underlaying invoice
         foreach($this->invoices as $z=>$meta)
         {
             //we only want invoice if it is of same interval and dont forget to conver week interval to day stamp
             if($meta['day'] <= $this->get_interval_to_day($it, $interval))
             {
                  //now put it in proper bucket
                  //add from ar bucket
                  foreach($this->day_splits as $k=>$v)
                 {
                        if($meta['old'] - $offset < $v)
                        {
                            //goes from that bucket
                            //$this->debug( "ar interval is $interval meta invoice#". $z ." day old: " . $meta['old'] . " on " . $meta['day'] . " offset is $offset posting to $v");
                            //$this->debug(print_r($meta,true));
                            //add ar
                            $outstanding = $meta['total'] - $meta['running_paid'] - $meta['running_adj'];

                            $this->add($data, 'ar_total::' . $this->day_splits[$k-1] . '-' . $v , $outstanding, $z);
                            $this->add($data, 'ar_location_' . $meta['location'] . '::' . $this->day_splits[$k-1] . '-' . $v , $outstanding, $z);
                            $this->add($data, 'ar_provider_' . $meta['provider'] . '::' . $this->day_splits[$k-1] . '-' . $v , $outstanding, $z);
                            $this->add($data, 'ar_service_' . $meta['service'] . '::' . $this->day_splits[$k-1] . '-' . $v , $outstanding, $z);
                            $this->add($data, 'ar_insurance_' . $meta['insurance'] . '::' . $this->day_splits[$k-1] . '-' . $v , $outstanding, $z);

                            break;
                        }
                    }
              }
          }

          return $data;
  }

    /*
     * memoise the basicas of invoice
     * number-
     *
     */
    protected function get_invoices_meta(){
        $result = array();

        $res = DB::connection('emds')->table("VIEW_API_InvoiceIndex")->get();

        foreach($res as $r)
        {
            $result[$r->Invoice_ID] = array(    'emd' => $r->InvoiceNumber_EMD,
                                                                'location'=> urlencode($this->translateLocation($r->Organization_Name)),
                                                                'day'=>$r->Invoice_DAY,
                                                                'week'=>$r->Invoice_WEEK,
                                                                'old'=>floor((time()- strtotime($r->Invoice_Date))/86400),
                                                                'provider'=>urlencode($r->ProviderName),
                                                                'service'=>urlencode($r->Invoice_Comment),
                                                                'insurance'=>$this->fix_ins_name($r->InsuranceCompName),
                                                                'total'=>$r->InvoiceTotal,
                                                                'paid'=>$r->InsurancePaid,
                                                                'adj'=>$r->InsuranceAdjustment,
                                                                'running_paid'=>0,
                                                                'running_adj'=>0
                            );
        }
        return $result;
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

}