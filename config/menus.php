<?php

return [
    'Dashboard' => [
        'url' => '/admin/dashboard',
        'icon' => 'mdi-view-dashboard',
    ],
    'Social Account' => [
        'icon' => 'mdi-account-group',
        'children' => [
            'Tài khoản Facebook' => [
                'url' => '/facebook_page',
                'icon' => 'mdi-facebook',
            ],
            'Tài khoản Google' => [
                'url' => '/google_page',
                'icon' => 'mdi-google',
            ],
            'Tài khoản Zalo' => [
                'url' => '/zalo_page',
                'icon' => 'mdi-chat-processing',
            ],
            'County' => [
                'url' => '/contry_page',
                'icon' => 'mdi-earth',
            ],
            'Fanpage Manager' => [
                'url' => '/fanpage_manager_page',
                'icon' => 'mdi-facebook',
            ],
            'Ads Manager' => [
                'url' => '/ads_manager_page',
                'icon' => 'mdi-adobe',
            ],
            'Email scan' => [
                'url' => '/email-scan-page',
                'icon' => 'mdi-email-search',
            ],
        ],
    ],
    'Link' => [
        'url' => '/link-page',
        'icon' => 'mdi-link-variant',
    ],

    'Quản lý nội dung' => [
        'url' => '/content-manager-page',
        'icon' => 'mdi-file-document-edit',
    ],
    'Video & Image' => [
        'icon' => 'mdi-folder',
        'children' => [
            'File Manager' => [
                'url' => '/file-manager-page',
                'icon' => 'mdi-folder',
            ],
            'Video Creator' => [
                'url' => '/video-creator-page',
                'icon' => 'mdi-video',
            ],
        ],
    ],
    'Đặt lịch chạy' => [
        'icon' => 'mdi-rocket',
        'children' => [
            'Chạy Sub' => [
                'url' => '/sub_page',
                'icon' => 'mdi-rocket',
            ],
            'Like' => [
                'url' => '/like_page',
                'icon' => 'mdi-thumb-up',
            ],
            'Share' => [
                'url' => '/share_page',
                'icon' => 'mdi-share-variant',
            ],
        ],
    ],
    'Sử dụng ngay' => [
        'icon' => 'mdi-flash',
        'children' => [
            'Chạy Sub Ngay' => [
                'url' => '/sub_page_now',
                'icon' => 'mdi-rocket',
            ],
            'Like Ngay' => [
                'url' => '/like_page_now',
                'icon' => 'mdi-thumb-up',
            ],
            'Share Ngay' => [
                'url' => '/share_page_now',
                'icon' => 'mdi-share-variant',
            ],
        ],
    ],
    'CronTab' => [
        'url' => '/crontab-page',
        'icon' => 'mdi-clock-outline',
    ],
    'Network Controler' => [
        'icon' => 'mdi-server',
        'children' => [
            'Cấu Hình' => [
                'url' => '/network_config',
                'icon' => 'mdi-server',
            ],
            'Proxy V6' => [
                'url' => '/proxy_v6_page',
                'icon' => 'mdi-server',
            ],
            'Proxy V4' => [
                'url' => '/proxy_v4_page',
                'icon' => 'mdi-server',
            ],
        ],
    ],
    'Config Auto' => [
        'url' => '/auto_config_page',
        'icon' => ' mdi-bike',
    ],

    'Action Limit' => [
        'url' => '/facebook/action-limit-page',
        'icon' => 'mdi-timer-sand',
    ],
    'History' => [
        'icon' => 'mdi-history',
        'children' => [
            'Facebook History' => [
                'url' => '/facebook_history_page',
                'icon' => 'mdi-facebook',
            ],
            'Zalo History' => [
                'url' => '/zalo_history_page',
                'icon' => 'mdi-chat-processing',
            ],
            'Network history' => [
                'url' => '/network_history_page',
                'icon' => 'mdi-history',
            ],
            'Request history' => [
                'url' => '/request_history_page',
                'icon' => 'mdi-history',
            ],
        ]
    ],
    'Service Controller' => [
        'url' => '/service_manager_page',
        'icon' => 'mdi-server',

    ],
    'Quản lý hệ thống' => [
        'icon' => 'mdi-cogs',
        'children' => [
            'Quản lý tài khoản' => [
                'url' => '/user_page',
                'icon' => 'mdi-account-multiple',
            ],
            'Quản lý Site' => [
                'url' => '/site-manager',
                'icon' => 'mdi-web',
            ],
            'Quản lý quyền' => [
                'url' => '/role_page',
                'icon' => 'mdi-shield-key',
            ],
        ]
    ],
    'Sao lưu dữ liệu' => [
        'url' => '/backup-data',
        'icon' => 'mdi-database-export',
    ],
    'Thông tin hệ thống' => [
        'url' => '/system_info',
        'icon' => 'mdi-information-outline',
    ],

];
