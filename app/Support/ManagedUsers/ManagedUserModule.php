<?php

namespace App\Support\ManagedUsers;

class ManagedUserModule
{
    /**
     * @return array<string, string>
     */
    public static function listeners(): array
    {
        return self::make('listeners', 'Listeners', 'Listener', 'Listener management');
    }

    /**
     * @return array<string, string>
     */
    public static function gurujis(): array
    {
        return self::make('gurujis', 'Gurujis', 'Guruji', 'Guruji management');
    }

    /**
     * @return array<string, string>
     */
    private static function make(string $key, string $title, string $singular, string $eyebrow): array
    {
        return [
            'key' => $key,
            'title' => $title,
            'singular' => $singular,
            'eyebrow' => $eyebrow,
            'indexRoute' => $key.'.index',
            'editRoute' => $key.'.edit',
            'updateRoute' => $key.'.update',
            'toggleRoute' => $key.'.toggle-status',
            'deleteRoute' => $key.'.destroy',
        ];
    }
}
