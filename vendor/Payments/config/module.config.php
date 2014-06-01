<?php
namespace Payments;

/**
  * Configurator router current module (Payments)
  * Here are set settings aliases and URL template processing
  * Recorded all controllers in the process of creating an application
  * Set the path to the application by default
  */
return [
    
    /**
     * Namespace for all controllers
     */
    'controllers' => [
        'invokables' => [
            'Payments\Controller\PaymentsController'      => 'Payments\Controller\PaymentsController', // call controller connection management
        ],
    ],

    /**
     * Configure the router module
     */

    'router' => [
        'routes' => [

            'payments' => [
                'type'          => 'Segment',
                'options'       => [
                    'route'         => '/pay',
                'constraints'   => [
                    'select'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                ],
                'defaults' => [
		    'controller'    => 'Payments\Controller\PaymentsController',
		    'action'    => 'index',
                    ],
                ],
                'may_terminate' => true,
                
                'child_routes' => [
                    'id' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '[/][:id]',
                                'constraints' => [
                                    'id' => '[a-zA-Z0-9_-]*',
                                ],
                                'defaults' => [
                                    'controller'    => 'Payments\Controller\PaymentsController',
                                    'action'        => 'select',
                                ]
                        ],
                    ],
                    'process' => [
                        'type'      => 'Segment',
                        'options'   => [
                            'route' => '/process',
                                'defaults' => [
                                    'controller'    => 'Payments\Controller\PaymentsController',
                                    'action'        => 'process',
                                ]
                        ],
                    ],
                    
                ],                 
            ],
        ],
    ],

    // Require template_map

    'view_manager' => [
        
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],        
    ],
];
