<?php namespace Lasdorf\MailHard;

use Illuminate\Support\Facades\Mail as Mail;  //to get configs
use Illuminate\Log; //to log screw ups

Class FbgUtils {

    public function __construct(){

    }

    public static function notify($data, $view, $status_msg="")
    {
        $data['random'] = self::get_random_text(); //add random text to fool junk mail filters
        if(empty($data['status_message']))
        {
            $data['status_message'] = $status_msg;
        }

        Mail::send($view, $data, function($message) use ($data)
        {
            $message->to($data['email'])->subject($data['status_message']);
        });
    }

    private static function get_random_text()
    {
        $file = __DIR__ . '/random.txt';
        $returnlines = 20;
        $i=0;
        $buffer="\n";
        $rand = rand(1, filesize($file));

        $handle = @fopen($file, "r");
        fseek($handle, $rand);

        if ($handle)
        {
            while (!feof($handle) && $i<=$returnlines)
            {
                $buffer .= fgets($handle, 4096);
                $i++;
            }

            fclose($handle);
        }

        return "\n</br>\n</br>\n</br>*************RANDOM TEXT TO ESCAPE SPAM FILTER****************************\n</br>\n</br>\n</br>" . $buffer;
    }
}