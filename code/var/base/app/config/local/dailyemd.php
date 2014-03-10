<?php

return array(
    'ttl'=>300,
    'billing' => array(

                'valid_codes' => array('FILED'=>1,'DAISY'=>1,'PAR'=>1,'PAID'=>1),
                'ar_buckets' => array(
                                    30=>'<30',
                                    60=>'30-60',
                                    90=>'60-120',
                                    120=>'120-180',
                                    180=>'6m-12m',
                                    365=>'12m-18m',
                                    730=>'18m-24m',
                                    1460=>'2yr+'
                                )
  )
);
