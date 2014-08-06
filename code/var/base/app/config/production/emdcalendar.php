<?php
return  array(
                "dryrun"=>false,  //run but not email
                "test"=>false,  //test setup
                "providerf"=>"", //filter to provider
                "force"=>true,  //disregard status and run anyway
                "force_proxy"=>false, //run through proxies of give provider
                "providers"=>array(
                                "Ahluwalia, Navneet S."=>array('email'=>"ahluwalia.navneet.calendar@helppain.net", 'proxy'=>array()),
                                "Azevedo, Michael"=>array('email'=>"azevedo.michael.calendar@helppain.net", 'proxy'=>array()),
                                "Brose, William"=>array('email'=>"brose.william.calendar@helppain.net", 'proxy'=>array(
                                                'Gaeta/Brose Consult'=>"Gaeta, Raymond R.",
                                                'Gaeta/Brose f/u'=>"Gaeta, Raymond R.",
                                                'Schonwald/Brose Consult'=>"Schonwald, Gabriel",
                                                'Schonwald/Brose f/u'=>"Schonwald, Gabriel",
                                                'Azevedo/Brose Consult'=>"Azevedo, Michael",
                                                'Azevedo/Brose f/u'=>"Azevedo, Michael",
                                                'Ahluwalia/Brose Consult'=>"Ahluwalia, Navneet S.",
                                                'Ahluwalia/Brose f/u'=>"Ahluwalia, Navneet S."
                                ) ),
                                "Gaeta, Raymond R."=>array('email'=>"gaeta.raymond.calendar@helppain.net", 'proxy'=>array(
                                                'Azevedo/Gaeta Consult'=> "Azevedo, Michael",
                                                'Azevedo/Gaeta f/u'=>"Azevedo, Michael"
                                )),
                                "Schonwald, Gabriel"=>array('email'=>"schonwald.gabriel.calendar@helppain.net", 'proxy'=>array()),
                ),
);