<?php

namespace App\Support\DashboardContent;

class ContentModule
{
    /**
     * @return array<string, mixed>
     */
    public static function categories(): array
    {
        return self::make('categories', 'Categories', 'Category', 'Category management', [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
        ], false);
    }

    /**
     * @return array<string, mixed>
     */
    public static function languages(): array
    {
        return self::make('languages', 'Languages', 'Language', 'Language management', [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
        ], false);
    }

    /**
     * @return array<string, mixed>
     */
    public static function termsAndConditions(): array
    {
        return self::make('terms-and-conditions', 'Terms and Conditions', 'Terms and Conditions', 'Terms and Conditions management', [
            ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
            ['name' => 'content', 'label' => 'Content', 'type' => 'textarea', 'wide' => true, 'rows' => 16],
        ], false);
    }

    /**
     * @return array<string, mixed>
     */
    public static function faqs(): array
    {
        return self::make('faqs', 'FAQ', 'FAQ', 'FAQ management', [
            ['name' => 'question', 'label' => 'Question', 'type' => 'textarea', 'wide' => true, 'rows' => 3],
            ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'wide' => true, 'rows' => 8],
        ], false);
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @return array<string, mixed>
     */
    private static function make(string $key, string $title, string $singular, string $eyebrow, array $fields, bool $showStatusField = true): array
    {
        return [
            'key' => $key,
            'title' => $title,
            'singular' => $singular,
            'eyebrow' => $eyebrow,
            'fields' => $fields,
            'showStatusField' => $showStatusField,
            'displayField' => $fields[0]['name'],
            'detailField' => $fields[1]['name'] ?? null,
            'indexRoute' => $key.'.index',
            'createRoute' => $key.'.create',
            'storeRoute' => $key.'.store',
            'editRoute' => $key.'.edit',
            'updateRoute' => $key.'.update',
            'toggleRoute' => $key.'.toggle-status',
            'deleteRoute' => $key.'.destroy',
        ];
    }
}
