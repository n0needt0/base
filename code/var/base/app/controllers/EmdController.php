<?php


class EmdController extends BaseController {

    public function __construct()
    {
        //        $this->beforeFilter('auth');
    }

    public function getIndex()
    {
        return 'emds';
    }

    public function getPdf()
    {
        try{
            $image_url = Input::get('imgurl',false);
            if(!$image_url)
            {
                echo "Invalid Image";
                die;
            }
            $image = new Imagick();
            $f = fopen($image_url, 'rb');
            $image->readImageFile($f);
            $image->setImageFormat("pdf");
            $fname = md5($image_url);
            $image->writeImages( '/tmp/' . $fname, true );
            $image->clear();

            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $fname .'"');
            echo file_get_contents('/tmp/' . $fname);
            unlink('/tmp/' . $fname);

            die;

        }catch(Exception $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     *
     */

    public function getInvoice($invoice_id,$format=false)
    {
        $invoice = array('header'=>array(), 'charges'=>array(), 'payments'=>array());
        $header = $charges = $payments = array();

        $res = DB::connection('emds')->table("VIEW_API_InvoiceIndex")->where('InvoiceNumber_EMD', $invoice_id)->get();
        foreach($res as $r)
        {
            $header = (array)$r;
            //unset secure data here
        }

        if(empty($header))
        {
            return 'invalid emd id';
        }

        $emd_invoice_id = $header['Invoice_ID'];

        $invoice['header'] = $header;

        $res = DB::connection('emds')->table("VIEW_API_InvoiceCPT")->where('Invoice_ID', $emd_invoice_id)->get();
        foreach($res as $r)
        {
            $charges[] = (array)$r;
            //unset secure data here
        }

        $invoice['charges'] = $charges;

        $res = DB::connection('emds')->table("VIEW_API_PaymentIndex")->where('Invoice_ID', $emd_invoice_id)->get();
        foreach($res as $r)
        {
            $payments[] = (array)$r;
            //unset secure data here
        }

        $invoice['payments'] = $payments;

        if('json' == $format)
        {
            return $this->json_out($invoice);
        }
        elseif('htmljson' == $format)
        {
            $content = View::make('emd.billtrac')->with($invoice)->render();
            return $this->json_out(array('contents'=>$content));
        }
        else
        {
            //just output regular div
            return View::make('emd.billtrac')->with($invoice);
        }

    }

    public function getAppointments($patient_file,$format=false)
    {

        $patient_file = strtoupper(trim($patient_file));
        $appointments = $schedule = array();

        $res = DB::connection('emds')->table("VIEW_API_Appointment")->where('patient_file', strtoupper($patient_file))->where('isdeleted',0)->orderBy('start', 'desc')->get();
        foreach($res as $r)
        {
            if( strtotime($r->startf) >= strtotime(date('Y-m-d 00:00:00',time())))
            {
                $r->display = 'schnormal';
            }
              else
            {
                $r->display='schhistory';
            }

            if($r->status == 3)
            {
                $r->display = 'schnoshow';
            }

            if($r->status == 5 || $r->status == 2)
            {
                $r->display = 'schcancel';
            }
get
            $appointments[] = (array)$r;

            //unset secure data here
        }

        if(!count($appointments))
        {
            return 'invalid patient file';
        }

        $schedule['appointments'] = $appointments;

        if('json' == $format)
        {
            return $this->json_out($schedule);
        }
        elseif('htmljson' == $format)
        {
            $content = View::make('emd.ptrac')->with($schedule)->render();
            return $this->json_out(array('contents'=>$content));
        }
        else
        {
            //just output regular div
            return View::make('emd.ptrac')->with($schedule);
        }

    }
}