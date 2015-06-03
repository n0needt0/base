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

        $result = trim($result);
        if(empty($result))
        {
            \Log::info("miss $key");
            return false;
        }
        \Log::info("hit $key");
        $r = unserialize($result);

        return $r;
    }

    protected static function store($key, $value, $ttlsec)
    {

        $r = Redis::set($key, serialize($value));

        if( !$r )
        {
            \Log::info("failed store $key for $ttlsec sec");
            return;
        }
        Redis::expire($key, $ttlsec);

        \Log::info("stored $key for $ttlsec sec");
    }



    /**
     * Get calendar from today 0:0:0
     */
    static public function get_calendar()
    {
        $config = Config::get('emdcalendar');

        $result = array();
        $res = DB::connection('emds')->table("VIEW_API_Appointment")->where('startyyyymmdd', '>=', date('Ymd', time()))->orderBy('startyyyymmdd', 'asc')->get();
        $insert=$block=$move=$cancel=$update=0;
        $dot = 0;
        $humpster = array("wants water"=>"drink", "hungry"=>"eat", "money"=>"negative");

        foreach($res as $r)
        {
            echo '.';
            $dot++;
            if($dot%80 == 0)
            {
                $dot =0;
                echo "\n";
            }

            usleep(100); //unload a little

            //set unix timestamps in addtion to formattes
            $r->start_ts = strtotime($r->startf);
            $r->end_ts = strtotime($r->endf);

            if(!empty($config['providers'][$r->resource])) //only work with configured providers
            {
                //cancels and deletes
                if((string)$r->status == '5' || (string)$r->isdeleted == '1')
                {
                    //get current email
                    $email = $config['providers'][$r->resource]['email'];

                    //cancel appointment by setting timestamps 3 years back
                    $r->start_ts = strtotime($r->startf) - 3*365*24*60*60;
                    $r->end_ts = strtotime($r->endf) - 3*365*24*60*60;
                    $r->action = "CANCEL";
                    $r->subject = "CANCEL: " . $r->patient_name  . " (" . $r->dob . ") " . $r->appointment_type;

                    $r->email = $email;

                    if(!self::is_modified($r))
                    {
                        continue;
                    }

                    $r->md5 = md5(serialize($r));
                    self::debug(print_r($r, true));
                    $cancel++;
                    self::set_appointment($r, $email);
                    if ($r->appointment_type == 'P&S Exam'){
                        self::set_appointment($r, 'kyawary@helppain.net');
                    }

                    continue;
                }

                // block
                if( (string)$r->status == '1' && (string)$r->isdeleted == '0' )
                {
                    //get current email
                    $email = $config['providers'][$r->resource]['email'];

                    //cancel appointment by setting timestamps 3 years back
                    $r->action = "BLOCK";
                    $r->subject = "BLOCK: " . $r->appointment_type;

                    $r->email = $email;

                    if(!self::is_modified($r))
                    {
                        continue;
                    }

                    $r->md5 = md5(serialize($r));
                    $block++;
                    self::debug(print_r($r, true));
                    self::set_appointment($r, $email);
                    if ($r->appointment_type == 'P&S Exam'){
                        self::set_appointment($r, 'kyawary@helppain.net');
                    }
                    continue;
                }


                if((string)$r->isdeleted == '0' && ( (string)$r->status == '0' || (string)$r->status == '2' ) ) //we move everything
                {
                    //see if it is new or update
                    $key = self::make_key($r);
                    $cache = self::lookup($key);
                    $email = $config['providers'][$r->resource]['email'];

                    if(empty($cache) || empty($cache->email))
                    {

                        echo "do new
                                        \n";
                        //new appontment set it
                        $r->action = "NEW";
                        $r->subject = $r->patient_name  . " (" . $r->dob . ") " . $r->appointment_type;
                        $r->md5 = md5(serialize($r));
                        $insert++;
                        self::debug(print_r($r, true));
                        self::set_appointment($r, $email);
                        if ($r->appointment_type == 'P&S Exam'){
                            self::set_appointment($r, 'kyawary@helppain.net');
                        }
                        continue;
                    }
                    else
                    {
                        //see if modified and if moved from one account to other delete the other
                        if($cache->email != $email )
                        {
                            //delete the old one
                            //cancel appointment by setting timestamps 3 years back
                            $r->start_ts = strtotime($r->startf) - 3*365*24*60*60;
                            $r->end_ts = strtotime($r->endf) - 3*365*24*60*60;
                            $r->action = "MOVE";
                            $r->subject = $r->patient_name  . " (" . $r->dob . ") " . $r->appointment_type;
                            $r->md5 = "RESET"; //needs to be set like this so next will pick up update
                            self::debug(print_r($r, true));
                            $move++;
                            self::set_appointment($r, $cache->email);
                            if ($r->appointment_type == 'P&S Exam'){
                                self::set_appointment($r, 'kyawary@helppain.net');
                            }
                        }

                        //now modify accordingly
                        $r->start_ts = strtotime($r->startf);
                        $r->end_ts = strtotime($r->endf);
                        $r->action = "UPDATE";
                        $r->subject = $r->patient_name  . " (" . $r->dob . ") " . $r->appointment_type;

                        $r->email = $email;

                        if(!self::is_modified($r))
                        {
                            continue;
                        }

                        //***so it matches md5 on next run******
                        unset($r->md5);
                        $r->action = "UPDATE";
                        $md5 = md5(serialize($r));
                        $r->md5 = $md5;
                        //*****************************************

                        $update++;
                        self::debug(print_r($r, true));
                        self::set_appointment($r, $email);
                        if ($r->appointment_type == 'P&S Exam'){
                          self::set_appointment($r, 'kyawary@helppain.net');
                       }
                        continue;

                    }
                }
                else
                {
                    \Log::info("INVALID PROVIDER: " . $r->resource);
                }
            }
        }

        echo "\nProcessed...NEW: $insert, BLOCK:$block, CANCEL:$cancel, UPDATE:$update, MOVE:$move \n";
    }

    static private function make_key($appointment)
    {
        return "APT:" . $appointment->appointment_id;
    }

    static private function set_appointment($r,$email )
    {
        $r->email = $email;
        $message="BEGIN:VCALENDAR\n";
        $message.="VERSION:2.0\n";
        $message.="CALSCALE:GREGORIAN\n";
        $message.="METHOD:REQUEST\n";
        $message.="BEGIN:VEVENT\n";
        $message.="DTSTART:" . date('Ymd\THis', $r->start_ts) . "\n";
        $message.="DTEND:" . date('Ymd\THis', $r->end_ts) . "\n";
        $message.="DTSTAMP:" . date('Ymd\THis', time()) . "\n";
        $message.="ORGANIZER;CN=Emd:mailto:emd@helppain.net\n";
        $message.="UID:" . $r->appointment_id . "\n";
        $message.="ATTENDEE;PARTSTAT=NEEDS-ACTION;RSVP= FALSE;CN=Emd:mailto:emd@helppain.net\n";
        $message.="DESCRIPTION:" . $r->notes . "\n";
        $message.="LOCATION: " . $r->facility . "\n";
        $message.="SEQUENCE:0\n";
        $message.="STATUS:CONFIRMED\n";
        $message.="SUMMARY:" . $r->subject . "\n";
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
        $subject = html_entity_decode($r->subject, ENT_QUOTES, 'UTF-8');
        $key = self::make_key($r);

        if(Config::get('emdcalendar.dryrun'))
        {
            \Log::info("Dry run " . print_r(array("email"=>$email, "subject"=>$subject, "message"=>$message, "headers"=>$headers), true));
            self::store($key, $r, 60*60*24*7);
            return;
        }

        if( $res = mail($email, $subject, $message, $headers) )
        {
            //set cache only if sent
            \Log::info("eMailed " . print_r(array("email"=>$email, "subject"=>$subject, "message"=>$message, "headers"=>$headers), true));
            self::store($key, $r, 60*60*24*7);
        }

        return;
    }

    static function is_modified($r)
    {
        //see if needs processing
        $key = self::make_key($r);
        $cache = self::lookup($key);

        $md5 = trim(md5(serialize($r)));
        $force = Config::get('emdcalendar.force');

        if( !empty($cache) && (trim($cache->md5) == $md5) && empty($force))
        {
            //already done
            $r->action = "SKIP";
            self::debug(print_r($r, true));
            return false;
        }
        return true;
    }
}
