<?php
return [
    'defaultConfigZalo' => [
        "auto" => false,
        "config_auto" => 'zalo',
        "session" => 'windows',
        "configurations" => [
            "join_group_config" => [
                "auto" => true,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "post_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "add_friend_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ]
        ]
    ],
    'defaultConfigFacebook' => [
        "auto" => true,
        "config_auto" => 'facebook',
        "session" => 'android',
        "configurations" => [
            "join_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "post_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "comment_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "add_friend_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "post_status_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "seeding_home_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "comment_home_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "share_post_to_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "share_post_to_profile_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
        ]
    ],
    'defaultConfigFanpage' => [
        "auto" => true,
        "config_auto" => 'fanpage',
        "session" => 'android',
        "configurations" => [
            "join_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "post_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "comment_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "add_friend_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "post_status_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "seeding_home_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "comment_home_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "share_post_to_group_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
            "share_post_to_profile_config" => [
                "auto" => false,
                "min_time" => 200,
                "action_limit" => 2,
                "already_done" => 0,
                "time_old" => date('d/m/Y H:i:s')
            ],
        ]
    ],
    'defaultConfigInteractLimit' => [
        "limit_use_like_post" => 15,
        "limit_use_like_page" => 15,
        "limit_use_sub_user" => 15,
        "limit_use_follow_page" => 15,
        "limit_use_share_post" => 15,
        "limit_use_comment" => 15,
    ],
];
