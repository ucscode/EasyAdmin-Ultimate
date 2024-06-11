<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@hotwired/turbo' => [
        'version' => '8.0.4',
    ],
    'chart.js/auto' => [
        'version' => '4.4.2',
    ],
    '@kurkle/color' => [
        'version' => '0.3.2',
    ],
    'bootbox' => [
        'version' => '6.0.0',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'animate.css' => [
        'version' => '4.1.1',
    ],
    'animate.css/animate.min.css' => [
        'version' => '4.1.1',
        'type' => 'css',
    ],
    'clipboard' => [
        'version' => '2.0.11',
    ],
    'bs5-toast' => [
        'version' => '1.0.0',
    ],
];
