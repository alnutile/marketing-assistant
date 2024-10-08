<?php

return [
    'feedback_count' => env('ASSISTANT_FEEDBACK_COUNT', 3),
    "webhooks" => [
        "tweet" => env('ASSISTANT_TWEET_WEBHOOKS_URL'),
    ]
];
