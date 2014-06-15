<?php
/**
 * Параметры навигации для текущего модуля
 */
return [
    'navigation'    =>  [
        'default'     =>  [
            [
                'label' => 'Control Panel',
                'route' => 'admin',
                'pages' => [
                    [
                        'label'     =>  'Users control',
                        'route'     =>  'users',
                        'action'    =>  'index',
                        'pages'     =>  [
                            [
                                'label'     =>  'Edit - ',
                                'route'     =>  'users/edit',
                                'action'    =>  'edit',
                            ],    
                            [
                                'label'     =>  '',
                                'route'     =>  'users/view',
                                'action'    =>  'view',
                            ],                            
                        ]                        
                    ],
                    [
                        'label'     =>  'Plugins control',
                        'route'     =>  'plugins',
                        'action'    =>  'index',
                        'pages'     =>  [
                            [
                                'label'     =>  '',
                                'route'     =>  'plugins/edit',
                                'action'    =>  'edit',
                            ],    
                            [
                                'label'     =>  'Register plugin',
                                'route'     =>  'plugins/add',
                                'action'    =>  'add',
                            ],                            
                        ]
                    ],    
                    [
                        'label'     =>  'Submissions control',
                        'route'     =>  'distributions',
                        'action'    =>  'index',
                        'pages'     =>  [
                            [
                                'label'     =>  '',
                                'route'     =>  'distributions/provider',
                                'action'    =>  'view',
                            ],    
                        ]
                    ],                     
                ]
            ],
        ],
    ],    
];
