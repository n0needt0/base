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

        $result = array();
        $res = DB::connection('emds')->table("VIEW_API_Appointment")->where('startyyyymmdd', '>=', date('Ymd', time()))->where('patient_file','<>','')->where('patient_id','<>','0000000000')->whereIn('resource',array_keys($providers))->orderBy('startyyyymmdd', 'asc')->get();
        $i = 0;

        foreach($res as $r)
        {
            if(!empty($providers[$r->resource]))
            {
                //create hash
                $appthash = md5(serialize($r)) . "\n";
                //check if key exists for given "APPT:ID"
                $key = "APPT:" . $r->appointment_id;
                $lookup = self::lookup($key);

                if(empty($lookup) || trim((string)$lookup) != trim((string)$appthash))
                {
                    //save appontment key ttl to appointment + 1 day
                    if( self::set_appointment($r,$providers[$r->resource]) )
                    {
                        $i++;
                        self::store($key, $appthash, strtotime($r->startf) - time() + 60*60*24 );
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
        $message.="DTSTART:" . date('Ymd\THis', strtotime($r->startf)) . "\n";
        $message.="DTEND:" . date('Ymd\THis', strtotime($r->endf)) . "\n";
        $message.="DTSTAMP:" . date('Ymd\THis', time()) . "\n";
        $message.="ORGANIZER;CN=Emd:mailto:emd@helppain.net\n";
        $message.="UID:" . $r->appointment_id . "\n";
        $message.="ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP= FALSE;CN=Emd:mailto:emd@helppain.net\n";
        $message.="DESCRIPTION:" . $r->appointment_type . " | " . $r->patient_name . "\n";
        $message.="LOCATION: " . $r->facility . "\n";
        $message.="SEQUENCE:0\n";
        $message.="STATUS:CONFIRMED\n";
        $message.="SUMMARY:" . $r->appointment_type . " | " . $r->patient_name . " | " . $r->facility . "\n";
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
        $subject = $r->appointment_type . " with " . $r->patient_name;
        $subject = html_entity_decode($subject, ENT_QUOTES, 'UTF-8');

        if( $res = mail($email, $subject, $message, $headers) )
        {
            return true;
        }

        return false;
    }
}
