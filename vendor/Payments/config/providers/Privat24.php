<?php
/**
 * Setting connection to the Provider. 
 * For each individual they are. So be careful when you try to connect a new supplier
 */
return [
    'Payments\Provider\Config'  => [
        
        // Privat24 Online pay
        
        'Privat24'  =>  [

            // Request params
            
            'merchant'          =>  101091,                                    // online store ID (you must to register your store in Privat24 system)
            'merchant_password' =>  '6PcmncrrIyhdF901kpWg9GjPDVkaZIt7',        // password verify from response (get it from account Privat24 > Merchant)
            'request_uri'       =>  'https://api.privatbank.ua/p24api/ishop',  // host for API requests

            // Form params for builder
            
            'form'    =>  [
                'form'  =>  [
                    'method'    =>  'post'
                ],
                
                // required public fields
                
                'fields'    =>  [
                    'order'     =>  ['type' => 'text', 'placeholder'    =>  'order ID here',  'label'    =>  'Entering Order ID', 'required' => true],
                    'amt'       =>  ['type' => 'text',  'placeholder'    =>  'input amunt for refill', 'pattern' =>  '^\d+(.\d{0,3})?$', 'label' => 'Enter amount (50.2)', 'required' => true],
                    'details'   =>  ['type' => 'text',  'placeholder'    =>  'comment here...', 'label'    =>  'Comment if you wish'],   
                ],
                
                // system hidden fields
                
                'system'    =>  [
                    'pay_way'           =>  'privat24',                                // transaction type
                    'return_url'        =>  'http://zf.local/pay/result',          // page hosting customer after payment
                    'server_url'        =>  'http://zf.local/pay/result',          // page hosting API response to a result of payment
                    'ccy'               =>  'UAH',
                ],
            ],            
            
            // This is Requred option "adapter" (inside parameters can be configured of necessity)
            
            'adapter'           => [
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