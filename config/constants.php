<?php

return [
    'flashcards' => [
        'version' => '1.0.0-beta',
        'autoupdate' => env('AUTOUPDATE'),
        'registry_url' => env('REGISTRY_URL', 'ghcr.io'),
        'is_windows_docker_desktop' => env('IS_WINDOWS_DOCKER_DESKTOP', false),
    ],
    'terminal' => [
        'protocol' => env('TERMINAL_PROTOCOL'),
        'host' => env('TERMINAL_HOST'),
        'port' => env('TERMINAL_PORT'),
    ],

    'pusher' => [
        'host' => env('PUSHER_HOST'),
        'port' => env('PUSHER_PORT'),
        'app_key' => env('PUSHER_APP_KEY'),
    ],

    'migration' => [
        'is_migration_enabled' => env('MIGRATION_ENABLED', true),
    ],

    'seeder' => [
        'is_seeder_enabled' => env('SEEDER_ENABLED', true),
    ],

    'horizon' => [
        'is_horizon_enabled' => env('HORIZON_ENABLED', true),
        'is_scheduler_enabled' => env('SCHEDULER_ENABLED', true),
    ],

    'docker' => [
        'minimum_required_version' => '24.0',
    ],

    'ssh' => [
        'mux_enabled' => env('MUX_ENABLED', env('SSH_MUX_ENABLED', true)),
        'mux_persist_time' => env('SSH_MUX_PERSIST_TIME', 3600),
        'connection_timeout' => 10,
        'server_interval' => 20,
        'command_timeout' => 7200,
    ],

    'invitation' => [
        'link' => [
            'base_url' => '/invitations/',
            'expiration_days' => 3,
        ],
    ],

    'sentry' => [
        'sentry_dsn' => env('SENTRY_DSN'),
    ],
];