<?php

return array(
    'billing' => array(
                'dimensions'=>array(
                                'location'=>'Organization_Name',
                                'service'=>'Invoice_Comment'
                                )
  ),

  'revenueiq'=>array(
                  'include_tables'=>array('VIEW_EXTERNAL_LUCID_ENTITY_CPT',
                                                       'VIEW_EXTERNAL_LUCID_InvoiceCPT',
                                                       'VIEW_EXTERNAL_LUCID_InvoiceIndex',
                                                       'VIEW_EXTERNAL_LUCID_PaymentDistribution',
                                                       'VIEW_EXTERNAL_LUCID_PaymentIndex',
                                                       'VIEW_EXTERNAL_LUCID_REF_Case',
                                                       'VIEW_EXTERNAL_LUCID_REF_Entity_Location',
                                                       'VIEW_EXTERNAL_LUCID_REF_Guarantor',
                                                       'VIEW_EXTERNAL_LUCID_REF_HCPCS',
                                                       'VIEW_EXTERNAL_LUCID_REF_InsuranceCompany',
                                                       'VIEW_EXTERNAL_LUCID_REF_InsuranceGroup',
                                                       'VIEW_EXTERNAL_LUCID_REF_Providers',
                                                       'VIEW_EXTERNAL_LUCID_REF_ReasonCode'
                               ),
                  'pemfile'=>'/etc/ftpuser.pem',
                  'destination'=>'ftpuser@54.68.229.123:/home/ftpuser/ -i /etc/ftpuser.pem',
                  /*
                   scp -i ~/.ssh/ftpuser.pem HELP20151020.zip ftpuser@54.68.229.123:/home/ftpuser/
                   $connection = ssh2_connect('shell.example.com', 22);
ssh2_auth_password($connection, 'username', 'password');

ssh2_scp_send($connection, '/local/filename', '/remote/filename', 0644);
                    */
)

);