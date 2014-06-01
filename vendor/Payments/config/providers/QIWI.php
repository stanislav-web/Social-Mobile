<?php
/**
 * Setting connection to the Provider. 
 * For each individual they are. So be careful when you try to connect a new supplier
 */
return [
    'Payments\Provider\Config'  => [
        
        // Visa QIWI Wallet
        
        'QIWI'  =>  [

            // Request params
            
            'request_uri'       =>  'https://w.qiwi.com/',  // host for API requests
            'prv_id'            =>  12345,                  // store Identitficator
            'password'          =>  'qwerty123',            // password for authorize
            'bill_id'           =>  '001',                  // Billing identifier
            'key'               =>  '1234567890ABCDEFG',    // secret key from API Visa QIWI Wallet 
            'ccy'               =>  'RUB',                  // currency
            'lifetime'          =>  0,                      // payment lifetime
            'successURL'        =>  '?pay=QIWI&success',    // callback success transaction url
            'failURL'           =>  '?pay=QIWI&fail',       // callback fail transaction url
            
            // Form params for builder
            
            'form'    =>  [
                'form'  =>  [
                    'method'    =>  'post'
                ],
                
                // required public fields
                
                'fields'    =>  [
                    'user'          =>  ['type' => 'phone', 'placeholder'    =>  'input phone number',  'pattern'    =>  '^\+\d{1,15}$', 'label'    =>  'Phone number (+0123456789)', 'required' => true],
                    'amount'        =>  ['type' => 'text',  'placeholder'    =>  'input amunt for refill', 'pattern' =>  '^\d+(.\d{0,3})?$', 'label' => 'Enter amount (50.2)', 'required' => true],
                    'comment'       =>  ['type' => 'text',  'placeholder'    =>  'comment here...', 'label'    =>  'Comment if you wish'],   
                ],
                
                // system hidden fields
                
                'system'    =>  [
                    'ccy'       =>  'RUB',
                    'lifetime'  =>  '',
                    'pay_source'=>  'qw', // ("qw", "mobile")
                    'prv_name'  =>  'QIWI'
                ],
            ],            
            
            // This is Requred option "adapter" (inside parameters can be configured of necessity)
            
            'adapter'           => [
                'CURLOPT_HTTPAUTH'          =>  CURLAUTH_BASIC,                     // auth type
                'CURLOPT_RETURNTRANSFER'    =>  true,
                'CURLOPT_FOLLOWLOCATION'    =>  false,
                'CURLOPT_SSL_VERIFYHOST'    =>  false,
                'CURLOPT_SSL_VERIFYPEER'    =>  false,
                'CURLOPT_HTTPHEADER'        =>  [ // receive content type
                    'Content-Type:  application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept:        text/json',
                ],        
            ]
        ]
    ]
];