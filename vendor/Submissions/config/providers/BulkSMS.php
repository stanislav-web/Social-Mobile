<?php
/**
 * Setting connection to the Provider. 
 * For each individual they are. So be careful when you try to connect a new supplier
 */
return [
    'Submissions\Provider\Config'  => [
        
        // Internet SMS Gateway
        
        'BulkSMS'  =>  [

            // Request params
            
            'api_url_pattern'   =>  'http://bulksms.vsms.net/eapi/submission/send_sms/2/2.0?username=:username&password=:password&message=:message&msisdn=:msisdn',                                                                                         // pattern of basic request uri 
            'username'          =>  'SWEB',                                                         // service username
            'password'          =>  'qwerty123',                                                    // service password
            'description'       =>  'Internet SMS Gateway',                                         // description
            'icon'              =>  '/images/providers/bulksms.jpg',                                // icon url
            
            // required Adapter request configurations
            
            'adapter'           =>  [
                CURLOPT_POST                =>  1,
                CURLOPT_RETURNTRANSFER      =>  true,
                CURLOPT_TIMEOUT             =>  30
            ],
        ]
    ]
];