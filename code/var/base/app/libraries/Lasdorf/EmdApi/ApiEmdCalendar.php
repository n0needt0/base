<?php namespace Lasdorf\EmdApi;

use Lasdorf\EmdApi\EmdBase;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Redis as Redis;
use Illuminate\Support\Facades\Config as Config;
use Illuminate\Log;
use Lasdorf\MailHard\MailHard as MailHard;


Class ApiEmdCalendar extends EmdBase{

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
        return trim(unserialize($result));
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

    /**
     * Get calendar from today 0:0:0
     */
    static public function get_calendar()
    {
        $providers = Config::get('emdcalendar.providers');
        $providerf = Config::get('emdcalendar.providerf');
        $force = Config::get('emdcalendar.force');
        $force_proxy = Config::get('emdcalendar.force_proxy');
        $test = Config::get('emdcalendar.test');

        $result = array();
        $res = DB::connection('emds')->table("VIEW_API_Appointment")->where('startyyyymmdd', '>=', date('Ymd', time()))->where('patient_file','<>','')->where('patient_id','<>','0000000000')->whereIn('resource',array_keys($providers))->orderBy('startyyyymmdd', 'asc')->get();
        $i = 0;

        foreach($res as $r)
        {
            usleep(100);

            if($test && !empty($providerf))
            {
                if( $r->resource == $providerf)
                {
                    $filter = true;
                }
                else
                {
                    $filter = false;
                }
            }
            else
            {
                $filter = true;
            }

            if($filter && !empty($providers[$r->resource]))
            {

                if($test && $force_proxy)
                {
                    $proxies = array_keys($providers[$r->resource]['proxy']);
                    $randproxy = rand(0, count($proxies)-1);
                    \Log::info('force proxy to ' . $proxies[$randproxy]);
                    $r->appointment_type = $proxies[$randproxy];
                }

                //create hash
                $appthash = md5(serialize($r)) . "\n";
                //check if key exists for given "APPT:ID"
                $key = "APPT:" . $r->appointment_id;
                $lookup = self::lookup($key);

                //modify some time stamps nto unix types
                $r->startf = strtotime($r->startf);
                $r->endf = strtotime($r->endf);

                //run exceptions
                //check if status == 5 //rollback all dates to 3 years 365 * 24 * 60 * 60
                if((string)$r->status == '5')
                {
                    $r->startf = $r->startf - (3 * 365 * 24 *60 * 60);
                    $r->endf = $r->endf - (3 * 365 * 24 *60 * 60);
                    $r->startf = $r->startf - (3 * 365 * 24 *60 * 60);
                }

                if( $force || empty($lookup) || trim((string)$lookup) != trim((string)$appthash))
                {
                    //save appontment key ttl to appointment + 1 day
                    //SEE I IT IS PROXY

                    if(!empty($providers[$r->resource]['proxy']) && !empty($providers[$r->resource]['proxy'][$r->appointment_type]))
                    {
                        //this is proxy calendar
                        //this a small hack to make appointment d to be bit different
                        $r->appointment_id = $r->appointment_id . md5($providers[$r->resource]['email']);

                        $email = $providers[$providers[$r->resource]['proxy'][$r->appointment_type]]['email'];
                        \Log::info("PROXY " . $r->appointment_type . " proxy provider from " . $providers[$r->resource]['email'] . " to " . $providers[$providers[$r->resource]['proxy'][$r->appointment_type]]['email'] );
                    }
                    else
                    {
                        $email = $providers[$r->resource]['email'];
                    }


                    if( self::set_appointment($r,$email) )
                    {
                        $i++;
                        if((string)$r->status == '5')
                        {
                            \Log::info("CANCEL appointment on " . date('m-d', $r->startf) . ' ' .  $r->patient_name  . " (" . $r->dob . ") " . $r->appointment_type );
                        }
                        else
                        {
                            \Log::info("SET appointment on " . date('m-d', $r->startf) . ' ' .  $r->patient_name  . " (" . $r->dob . ") " . $r->appointment_type );
                        }
                        self::store($key, $appthash, 5*60*60*24 );
                    }
                }
                else
                {
                    echo "skip\n";
                }
            }
        }
        echo "Updated $i calendar events. Exiting..";
    }

    static private function set_appointment($r,$email )
    {

        $message="BEGIN:VCALENDAR\n";
        $message.="VERSION:2.0\n";
        $message.="CALSCALE:GREGORIAN\n";
        $message.="METHOD:REQUEST\n";
        $message.="BEGIN:VEVENT\n";

        //roll back on cancel 3 years
        if((string)$r->status == '5')
        {

            $message.="DTSTART:" . date('Ymd\THis', ($r->startf - 3*365*24*60*60)) . "\n";
        }
        else
        {
            $message.="DTSTART:" . date('Ymd\THis', $r->startf) . "\n";
        }
        if((string)$r->status == '5')
        {
            $message.="DTEND:" . date('Ymd\THis', ($r->endf - 3*365*24*60*60)) . "\n";
        }
        else
        {
            $message.="DTEND:" . date('Ymd\THis', $r->endf) . "\n";
        }

        $message.="DTSTAMP:" . date('Ymd\THis', time()) . "\n";
        $message.="ORGANIZER;CN=Emd:mailto:emd@helppain.net\n";
        $message.="UID:" . $r->appointment_id . "\n";
        $message.="ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP= FALSE;CN=Emd:mailto:emd@helppain.net\n";
        $message.="DESCRIPTION:" . $r->patient_name  . " (" . $r->dob . ") " . $r->appointment_type . "\n";
        $message.="LOCATION: " . $r->facility . "\n";
        $message.="SEQUENCE:0\n";
        $message.="STATUS:CONFIRMED\n";
        $message.="SUMMARY:" . $r->notes . "\n";
        $message.="TRANSP:OPAQUE\n";
        $message.="END:VEVENT\n";
        $message.="END:VCALENDAR";

        /*Setting the header part, this is important */
        $headers = "From: EMD <emd@helppain.net>\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/calendar; method=REQUEST;\n";
        $headers .= '        charset="UTF-8"';
        $headers .= "\n";
        $headers .= "Content-Transfer-Encoding: 7bit";

        /*mail content , attaching the ics detail in the mail as content*/
        if((string)$r->status == '5')
        {
            $subject = 'CANCEL';
        }
        else
        {
            $subject = $r->patient_name  . " ( " . $r->dob . " ) " . $r->appointment_type;
        }

        $subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');

        if(Config::get('emdcalendar.dryrun'))
        {
            return true;
        }

        if( $res = mail($email, $subject, $message, $headers) )
        {
            return true;
        }

        return false;
    }
}
