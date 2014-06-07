<?php
/**
 * Setting connection to the Provider. 
 * For each individual they are. So be careful when you try to connect a new supplier
 */
return [
    'Submissions\Provider\Config'  => [
        
        // Unisender mass mail Service
        
        'Unisender'  =>  [

            // Request params
            
            'api_key'           =>  '599p7nzp9i1yst7ihru1kz4676iekkhy8p5dyfee', // API secret key
            'lang'              =>  'ru',                                       // default language
            'api_url_pattern'   =>  'https://api.unisender.com/:lang/api/:uri', // pattern of basic request uri 
            'default_list'      =>  'SocialMobile',                             // using default mail list name
            'track_read'        =>  false,                                      // track mailing read
            'track_links'       =>  false,                                      // track mailing links
            'format'            =>  'json',                                     // response data format
            'description'       =>  'Unisender Russian mass mail provider',     // description
            'icon'              =>  '/images/providers/unisender.png',          // icon url
            
            // required Adapter request configurations
            
            'adapter'           =>  [
                CURLOPT_POST                =>  1,
                CURLOPT_SSL_VERIFYPEER      =>  false,
                CURLOPT_SSL_VERIFYHOST      =>  false,
                CURLOPT_RETURNTRANSFER      =>  true,
                CURLOPT_TIMEOUT             =>  30
            ],
        ]
    ]
];