<?php

use Phico\View\Twig\Twig;

test('can instantiate Twig', function () {

    $view = new Twig([

    ]);

    expect($view)->toBeInstanceOf(Twig::class);

});
test('can render a Twig template from file', function ($expect, $data) {

    $view = new Twig([
        'file_paths' => [
            'tests/views'
        ]
    ]);

    $out = $view->render('hello.twig', $data);

    expect(compactHtml($out))->toBe(compactHtml($expect));

})->with([

            ['<h1>Hello World</h1>' . "\n", []],
            ['<h1>Hello Bob</h1>' . "\n", ['name' => 'Bob']],
            ['<h1>Hello Humpty Dumpty</h1>' . "\n", ['name' => 'Humpty Dumpty']],

        ]);

test('can render a Twig template from string', function ($expect, $data) {

    $view = new Twig([
        'use_cache' => false,
        'cache_path' => '/tmp',
        'view_path' => 'tests/views',
    ]);

    $out = $view->string('<h1>Hello {{ name ?? \'World\' }}</h1>', $data);

    expect(compactHtml($out))->toBe(compactHtml($expect));

})->with([

            ['<h1>Hello World</h1>', []],
            ['<h1>Hello Bob</h1>', ['name' => 'Bob']],
            ['<h1>Hello Humpty Dumpty</h1>', ['name' => 'Humpty Dumpty']],

        ]);

