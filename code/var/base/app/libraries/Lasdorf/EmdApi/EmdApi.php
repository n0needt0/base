<?php namespace Lasdorf\EmdApi;

use Lasdorf\EmdApi\EmdBase;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Redis as Redis;
use Illuminate\Support\Facades\Config as Config;
use Illuminate\Log;
use League\Csv\Writer;


Class EmdApi extends EmdBase{

    public function __construct(){

    }

    protected static function lookup($key)
    {
        $result = Redis::get($key);

        if($result === false)
        {
            \Log::info("miss $key");
            return false;
        }
        \Log::info("hit $key");
        return unserialize($result);
    }

    protected static function store($key, $value, $ttl)
    {

        $r = Redis::set($key, serialize($value));

        if( !$r )
        {
            \Log::info("failed store $key for $ttl sec");
            return;
        }
        Redis::expire($key, $ttl);

        \Log::info("stored $key for $ttl sec");
    }

    /*
     *   Move data from table to csv
     *   */
    static public function save_table_to_csv($table, $path){
        $csv =\League\Csv\Writer::createFromFileObject(new \SplTempFileObject());

         $res = DB::connection('emds')
                    ->table($table)
                    ->get();

         $csv->insertOne(\Schema::getColumnListing($table));

         foreach($res as $line){
             $csv->insertOne($line->to_array());
        }

        $csv->output("$path$table.csv");
        return;
    }

    /**
     * Selects meta data associated with invoices
     */
    static public function get_meta_invoices($selector = array())
    {

        $cachekey = 'meta_invoices';

        $ttl = Config::get('dailyemd.ttl');

        $cache = self::lookup($cachekey);

        if($cache !== false)
        {
            return $cache;
        }

        //otherwise

        $result = array();

        $res = DB::connection('emds')->table("VIEW_API_InvoiceIndex")->orderBy('Invoice_DAY', 'asc')->get();

        foreach($res as $r)
        {
            $result[$r->Invoice_ID] = array(    'emd' => $r->InvoiceNumber_EMD,
                            'location'=>self::fix_name(self::translateLocation($r->Organization_Name)),
                            'day'=>$r->Invoice_DAY,
                            'week'=>$r->Invoice_WEEK,
                            'days_old'=>floor((time()- strtotime($r->Invoice_Date))/86400),
                            'provider'=>self::fix_name($r->ProviderName,'md'),
                            'service'=>self::fix_name($r->Invoice_Comment,'service'),
                            'insurance'=>self::fix_name($r->InsuranceCompName,'org'),
                            'total'=>$r->InvoiceTotal,
                            'paid'=>$r->InsurancePaid,
                            'status'=>$r->InvoiceStatus_Code,
                            'adj'=>$r->InsuranceAdjustment,
                            'running_paid'=>0,
                            'running_adj'=>0
            );
        }

        self::store($cachekey, $result, $ttl);

        return $result;
    }

    /**
     * @param string $name
     * @param string $type [org, md, service]
     * @return string
     */
    static public function fix_name($name, $type=false)
    {
        if($type)
        {
            //lowercase it
            $name = strtolower($name);

           //replace ',' with ' '
            $name = str_replace(',', ' ', $name);
            //replace double space with single
            $name = str_replace('  ', ' ', $name);

            $npart = explode(' ', $name);

            if(($npart[0] == 'the') || ($npart[0] == 'help'))
            {
                array_shift($npart);
            }

            return urlencode(Ucfirst($npart[0]));
       }

        return urlencode($name);
    }

    static public function translateLocation($location='na')
    {
        $locs = Config::get('app.locationmap');
        if(empty($locs) || empty($locs[$location]))
        {
            return $location;
        }
        return $locs[$location];
    }

    /**
     * @param array $meta
     * @param string(YYYYMMDD) $from
     * @param string(YYYYMMDD) $to
     * @param string[both,left,right,''] $include
     * @param string[false,DAY,WEEK,MONTH] $merge
     * @return multitype:
     */
    static public function get_billing(&$meta, $from, $to=false, $include='both', $merge=false)
    {

        $cachekey = 'get_billing'.$from.$to.$include.$merge;
        $ttl = Config::get('dailyemd.ttl');

        $cache = self::lookup($cachekey);

        if($cache !== false)
        {
            return $cache;
        }

        //otherwise

        $data = array();

        $include = strtoupper($include);

        $valid_codes = Config::get('dailyemd.billing.valid_codes');

        //this function gets stats for billing dimension
        if($to)
        {
            $left = '>=';
            $right = '<=';

            if('LEFT' == $include )
            {
                $right = '<';
            }

            if('RIGHT' == $include )
            {
                $left = '>';
            }

            if(empty($include))
            {
                $right = '<';
                $left = '>';
            }

           $res = DB::connection('emds')
            ->table('VIEW_API_InvoiceIndex')
            ->where('Invoice_DAY', $left, $from)
            ->where('Invoice_DAY', $right, $to)
            ->get();
        }
          else
        {
            //only start required
            $res = DB::connection('emds')
            ->table('VIEW_API_InvoiceIndex')
            ->where('Invoice_DAY', $from)
            ->get();
        }

        foreach($res as $r)
        {

            if(!$invoice_meta = self::get_meta($meta, $r->Invoice_ID))
            {
                continue;
            }

            //generate dimensions
            $dimensions = array();
            $dimension['location'] =$invoice_meta['location'];
            $dimension['service'] = $invoice_meta['service'];
            $dimension['provider'] = $invoice_meta['provider'];
            $dimension['insurance'] = $invoice_meta['insurance'];
            $dimension['status'] = $invoice_meta['status'];

            //time bucket dimension if set
            $timebucket = false;

            if(!empty($merge))
            {
                $merge = strtoupper($merge);

                switch( $merge )
                {
                    case 'DAY': //leave it the day it is YYYYMMDD
                        $timebucket = $r->Invoice_DAY;
                    break;
                    case 'WEEK': //leave it a week it is YYYYWW
                        $timebucket = $r->Invoice_WEEK;
                    break;
                    case 'MONTH': //set YYYYMM
                        $timebucket = substr($r->Invoice_DAY,0,6);
                    break;
                }
            }

            //only add if it is valid billing code
            if(!empty($valid_codes[$dimension['status']]))
            {
                    //total billed
                    self::add($meta, $data, 'billed::total', $r->InvoiceTotal, $r->Invoice_ID, $timebucket);

                    foreach($dimension as $k=>$v)
                    {
                         //get clean singular dimensions
                         self::add($meta, $data, 'billed_'. $k .'::' . $v, $r->InvoiceTotal, $r->Invoice_ID, $timebucket);
                         //now permutations
                         foreach($dimension as $kk=>$vv)
                         {
                             if($k != $kk) //no need to self cross ref
                             {
                                 self::add($meta, $data, 'billed_'. $k .'|' . $kk . '|::' . $v . '|' . $vv . '|', $r->InvoiceTotal, $r->Invoice_ID, $timebucket);
                             }
                         }
                    }
             }
        }

        self::store($cachekey, $data, $ttl);

        return $data;
    }

    protected static function add(&$meta, &$array, $key, $value, $origin, $bucket = false)
    {
        if(!$bucket)
        {
            if(empty($array[$key]))
            {
                $array[$key] = array('val'=>0,'origin'=>array());
            }

            $array[$key]['val'] += $value;

            if(!empty($meta[$origin]))
            {
                $array[$key]['origin'][] = $meta[$origin]['emd'];
            }
        }
         else
        {
            if(empty($array[$bucket]))
            {
                $array[$bucket] = array();
            }

            if(empty($array[$bucket][$key]))
            {
                $array[$bucket][$key] = array('val'=>0,'origin'=>array());
            }

            $array[$bucket][$key]['val'] += $value;

            if(!empty($meta[$origin]))
            {
                $array[$bucket][$key]['origin'][] = $meta[$origin]['emd'];
            }
        }

        return;
    }

    /**
     * @param array $meta
     * @param string(YYYYMMDD) $from
     * @param string(YYYYMMDD) $to
     * @param string[both,left,right,''] $include
     * @param string[false,DAY,WEEK,MONTH] $merge
     * @return multitype:
     */
    static public function get_collections(&$meta, $from, $to=false, $include='both', $merge=false)
    {

        $cachekey = 'get_collections' . $from . $to . $include . $merge;

        $ttl = Config::get('dailyemd.ttl');

        $cache = self::lookup($cachekey);

        if($cache !== false)
        {
            return $cache;
        }

        //otherwise

        $data = array();

        $ar_buckets = Config::get('dailyemd.billing.ar_buckets');

        $include = strtoupper($include);

        //this function gets stats for billing dimension

        if($to)
        {
            $left = '>=';
            $right = '<=';

            if('LEFT' == $include )
            {
                $right = '<';
            }

            if('RIGHT' == $include )
            {
                $left = '>';
            }

            if(empty($include))
            {
                $right = '<';
                $left = '>';
            }

            $res = DB::connection('emds')
            ->table('VIEW_API_PaymentIndex')
            ->where('Payment_DateCheck_DAY', $left, $from)
            ->where('Payment_DateCheck_DAY', $right, $to)
            ->get();
        }
          else
        {
            //only start required
            $res = DB::connection('emds')
            ->table('VIEW_API_PaymentIndex')
            ->where('Payment_DateCheck_DAY', $from)
            ->get();
        }

        foreach($res as $r)
        {

            if(!$invoice_meta = self::get_meta($meta, $r->Invoice_ID))
            {
                \Log::error('EmdAPI Invoice not found ID#' . $r->Invoice_ID );
                continue;
            }

            //generate dimensions
            $dimensions = array();
            $dimension['location'] =$invoice_meta['location'];
            $dimension['service'] = $invoice_meta['service'];
            $dimension['provider'] = $invoice_meta['provider'];
            $dimension['insurance'] = $invoice_meta['insurance'];
            $dimension['status'] = $invoice_meta['status'];
/**
 *        AR bucket is changing depending on date range of calculation,
 *        therefore it s better to set it as difference between checkDate - invoiceDate
 */
            foreach($ar_buckets as $k=>$v )
            {
                //generate AR buckets by finding from invoice date the first bucket it fits into
                //as payment_date - invoice_date in days

                if($r->InvoiceToCheckDayCnt < $v)
                {
                    $dimension['ar'] = $v;
                }
            }


            //time bucket dimension if set
            $timebucket = false;

            if(!empty($merge))
            {
                $merge = strtoupper($merge);

                switch( $merge )
                {
                    case 'DAY': //leave it the day it is YYYYMMDD
                        $timebucket = $r->Payment_DateCheck_DAY;
                    break;
                    case 'WEEK': //leave it a week it is YYYYWW
                        $timebucket = $r->Payment_DateCheck_WEEK;
                    break;
                    case 'MONTH': //set YYYYMM
                        $timebucket = substr($r->Payment_DateCheck_DAY,0,6);
                    break;
                }
            }

           //first lets find total outstanding on that day
           self::add($meta, $data, 'posted::total', $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID, $timebucket);
           self::add($meta, $data, 'payment::total', $r->Payment_Payment, $r->Invoice_ID, $timebucket);
           self::add($meta, $data, 'adjustment::total', $r->Payment_Adjustment, $r->Invoice_ID, $timebucket);

           foreach($dimension as $k=>$v)
          {
               //get clean singular dimensions

               self::add($meta, $data, 'posted_' . $k .'::' . $v , $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID, $timebucket);
               self::add($meta, $data, 'payment_' . $k .'::' . $v, $r->Payment_Payment, $r->Invoice_ID, $timebucket);
               self::add($meta, $data, 'adjustment_' . $k .'::' . $v, $r->Payment_Adjustment, $r->Invoice_ID, $timebucket);

               //now permutations
               foreach($dimension as $kk=>$vv)
               {
                    if($k != $kk) //no need to self cross ref
                    {
                         self::add($meta, $data, 'posted_' . $k .'|' . $kk . '|::' . $v . '|' . $vv . '|' , $r->Payment_Payment + $r->Payment_Adjustment, $r->Invoice_ID, $timebucket);
                         self::add($meta, $data, 'payment_' . $k .'|' . $kk . '|::' . $v . '|' . $vv . '|', $r->Payment_Payment, $r->Invoice_ID, $timebucket);
                         self::add($meta, $data, 'adjustment_' . $k .'|' . $kk . '|::' . $v . '|' . $vv . '|', $r->Payment_Adjustment, $r->Invoice_ID, $timebucket);

                    }
               }
           }
        }

        self::store($cachekey, $data, $ttl);

        return $data;
    }


    //TODO

    /**
     * @param array $meta
     * @param string(YYYYMMDD) $day
     * @return multitype:
     */
    public static function get_ar(&$meta, $from, $to=false)
    {
        return;
    }

    static private function get_meta(&$meta, $invoice)
    {
        if(!empty($meta[$invoice]))
        {
            return $meta[$invoice];
        }
        return false;
    }

    public static function get_all_days()
    {

        $buckets = array();
        $pk = "Payment_DateCheck_DAY";
        $ik = "Invoice_DAY";

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

    public static function get_all_weeks()
    {

        $buckets = array();
        $pk = "Payment_DateCheck_WEEK";
        $ik = "Invoice_WEEK";

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

    /**
     * @param string(YYYYMMDD) $datestr
     * @throws Exception
     * @return boolean|string
     */
    public static function make_week($datestr)
    {
        if((strlen($datestr)) != 8)
        {
            throw new Exception("Invalid date format $datestr accepted YYYYMMDD");
            return false;
        }

        $offset = array(
                        1=>4 * 24 * 60 * 60 ,
                        2=>3 * 24 * 60 * 60 ,
                        3=>2 * 24 * 60 * 60 ,
                        4=>1 * 24 * 60 * 60 ,
                        5=>0 * 24 * 60 * 60 ,
                        6=>5 * 24 * 60 * 60 ,
                        0=>7 * 24 * 60 * 60
        );

        //generate week end that is friday
        $year = substr($datestr,0,4);
        $month = substr($datestr,4,2);
        $day = substr($datestr,6,2);

        $time  = mktime(0, 0, 0, $month, $day, $year);
        $weekday  = (int)date('w', $time);

        //now lets offset this day to next friday, to signify weekend
        $time += $offset[$weekday];
        return date('Ymd',$time);
    }

    /**
     * returns financials fro whole history
     * $param DAY,WEEK,MONTH $by
     */
    public static function get_financials($by=false)
    {
            $cachekey = 'get_financials' . $by;

            $ttl = Config::get('dailyemd.ttl');

            $cache = self::lookup($cachekey);

            if($cache !== false)
            {
                return $cache;
            }

            switch($by)
            {
                case 'DAY':
                    $merge = $days = self::get_all_days();
                    break;
                case 'WEEK':
                    $days = self::get_all_days();
                    $merge = self::get_all_weeks();
                    break;
                case 'MONTH':
                    $days = self::get_all_days();
                    $merge = array();

                    foreach($days as $k=>$v)
                    {
                        $merge[substr($k,0,6)] = 1;
                    }

                    break;
                default:
                    $days = self::get_all_days();
                    $by = false;
                    $merge = false;
                    break;
            }

            $meta = self::get_meta_invoices();

            $range = array_keys($days);
            $start = $range[0];
            $finish = $range[count($range)-1];


          //  $billed_totals = EmdApi::get_billing($meta, $start, $finish, 'both');
            //put whole thing to local storage
          //  $paid_totals = EmdApi::get_collections($meta, $start, $finish, 'both');


            $billed = EmdApi::get_billing($meta, $start, $finish, 'both', $by);
            //put whole thing to local storage
            $paid = EmdApi::get_collections($meta, $start, $finish, 'both', $by);

            $result = array();
            //merge data

            if(!$merge)
            {
                //it is just totals so safe to slam together.
                $result = array_merge($billed, $paid);
            }
              else
            {
                foreach($merge as $day=>$d)
                {
                    $b = $p = array();

                    if(!isset($result[$day]))
                    {
                        $result[$day] = array();
                    }

                    if(isset($billed[$day]))
                    {
                        //add billing
                        $b = $billed[$day];
                    }

                    if(isset($paid[$day]))
                    {
                        //add billing
                        $p =  $paid[$day];
                    }

                    $result[$day] = array_merge($b,$p);
               }
          }

          self::store($cachekey, $result, $ttl);

          return $result;
     }

     public static function get_filtered($filter = array(), $by=false)
     {
          $cachekey = 'get_filtered' . md5(implode('', $filter)) . $by;
          $ttl = Config::get('dailyemd.ttl');

          $cache = self::lookup($cachekey);

          if($cache !== false)
          {
              return $cache;
          }

          $r = self::get_financials($by);
          $result = array();

          if(!$by)
          {

          }
            else
          {
              foreach($r as $day=>$data)
              {
                  foreach($data as $k=>$v)
                  {
                       //see if filter matches from leftmost
                       foreach($filter as $i=>$itemfilter)
                       {
                           if(substr($k, 0, strlen($itemfilter)) == $itemfilter)
                           {
                               $result[$day][$k] = $v;
                           }
                       }
                  }
              }
          }

          self::store($cachekey, $result, $ttl);

          return $result;
     }
}

