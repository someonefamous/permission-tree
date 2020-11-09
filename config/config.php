<?php

return [
    'route_prefix' => 'sf-permissions',
    'middleware' => [
        'web'
    ],
    'available_permissions' => [
        'do_anything' => [
            'name'     => 'do anything',
            'children' => [
                'access_admin_functions' => [
                    'name'     => 'access admin functions',
                    'children' => [
                        'manage_users' => [
                            'name'     => 'manage users',
                            'children' => [
                                'edit_user_details'     => 'edit user details',
                                'edit_user_permissions' => 'edit user permissions',
                                'add_new_users'         => 'add new users'
                            ]
                        ],
                    ]
                ],
            ]
        ],
    ]
];
