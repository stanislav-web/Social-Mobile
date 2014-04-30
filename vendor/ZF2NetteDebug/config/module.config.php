<?php
/**
 * Webino (http://www.webino.org/)
 *
 * Don't forget to remove, disable or set to production for public.
 *
 * @copyright   Copyright (c) 2012 Peter Bačinský (http://www.bacinsky.sk/)
 * @license     New BSD License
 * @package     ZF2NetteDebug
 */

// DO NOT EDIT DIRECTLY
return array(
    'nette_debug' => array(
        'enabled'      => true,
        'mode'         => false,        // true = production|false = development|null = autodetect|IP address(es) csv/array
        'bar'          => true,       // bool = enabled|Toggle nette diagnostics bar
        'strict'       => true,        // bool = cause immediate death|int = matched against error severity
        'log'          => "./logs/",          // bool = enabled|Path to directory eg. data/logs
        'email'        => "",          // in production mode notifies the recipient
        'max_depth'    => 3,           // nested levels of array/object
        'max_len'      => 150,         // max string display length
        'template_map' => array(       // merge templates if enabled
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
    ),
);
