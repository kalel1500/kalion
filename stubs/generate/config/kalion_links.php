<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | In the following options you can configure the application links.
    |
    */

    'navbar' => [
        'search' => [
            'show' => false,
            'route' => null,
        ],
        'items' => [
            [
                'is_theme_toggle'   => true,
            ],
            [
                'code'              => 'user',
                'icon'              => null,
                'text'              => 'Open user menu',
                'tooltip'           => null,
                'route_name'        => null,
                'is_theme_toggle'   => false,
                'is_user'           => true,
                'dropdown'          => [
                    'is_list'           => false,
                    'is_square'         => false,
                    'get_data_action'   => 'getUserInfo',
                    'header'            => null,
                    'footer'            => null,
                    'items'             => [
                        [
                            'icon'          => 'kal::icon.arrow-left-end-on-rectangle',
                            'text'          => 'Log Out',
                            'tooltip'       => null,
                            'route_name'    => 'logout',
                            'is_post'       => true,
                            'is_separator'  => false,
                        ],
                    ],
                ],
            ],
        ],
    ],

    'sidebar' => [
        'search' => [
            'show' => false,
            'route' => null,
        ],
        'items' => [
            [
                'code'              => null,
                'icon'              => 'kal::icon.home',
                'text'              => 'Home',
                'tooltip'           => null,
                'route_name'        => 'home',
                'counter_action'    => null,
                'collapsed'         => false,
                'is_separator'      => false,
                'dropdown'          => null,
            ],
            [
                'code'              => null,
                'icon'              => 'kal::icon.document-text',
                'text'              => 'Posts',
                'tooltip'           => null,
                'route_name'        => 'post.list',
                'counter_action'    => null,
                'collapsed'         => false,
                'is_separator'      => false,
                'dropdown'          => null,
            ],
            [
                'code'              => null,
                'icon'              => 'kal::icon.tag',
                'text'              => 'Tags',
                'tooltip'           => null,
                'route_name'        => 'tags',
                'counter_action'    => null,
                'collapsed'         => false,
                'is_separator'      => false,
                'dropdown'          => null,
            ],
            [
                'is_separator'      => true,
            ],
            [
                'code'              => null,
                'icon'              => 'kal::icon.computer-desktop',
                'text'              => 'Laravel welcome',
                'tooltip'           => null,
                'route_name'        => 'welcome',
                'counter_action'    => null,
                'collapsed'         => false,
                'is_separator'      => false,
                'dropdown'          => null,
            ],
        ],
    ],
];
