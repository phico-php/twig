<?php

declare(strict_types=1);

namespace Phico\View\Twig;

use Phico\View\{ViewException, ViewInterface};
use Twig\Loader\{ArrayLoader, FilesystemLoader, LoaderInterface};
use Twig\{Environment, TwigFilter, TwigFunction};


class Twig implements ViewInterface
{
    private Environment $twig;
    private LoaderInterface $loader;
    private array $options = [
        'cache_path' => '/storage/views',
        'file_paths' => [],
        'namespaces' => [],
        'extensions' => [],
        'filters' => [],
        'functions' => [],
        'globals' => [],
    ];


    public function __construct(array $overrides = [])
    {
        // apply default options overriding with known overrides
        foreach ($this->options as $k => $v) {
            $this->options[$k] = (isset($overrides[$k])) ? $overrides[$k] : $v;
        }

        $this->loader = new FilesystemLoader();
        foreach ($this->options['file_paths'] as $path) {
            $this->loader->addPath($path);
        }
        foreach ($this->options['namespaces'] as $name => $path) {
            $this->loader->addPath($path, ltrim($name, '@'));
        }

        $this->twig = new Environment($this->loader, [
            'cache' => path($this->options['cache_path'])
        ]);
        foreach ($this->options['extensions'] as $extension) {
            $this->twig->addExtension($extension);
        }
        foreach ($this->options['filters'] as $name => $callable) {
            if ($callable instanceof TwigFilter) {
                $this->twig->addFilter($callable);
            }
            if (is_string($name) && (is_callable($callable) or is_array($callable))) {
                $this->twig->addFilter(new TwigFilter($name, $callable));
            }
        }
        foreach ($this->options['functions'] as $name => $callable) {
            if ($callable instanceof TwigFunction) {
                $this->twig->addFunction($callable);
            }
            if (is_string($name) && (is_callable($callable) or is_array($callable))) {
                $this->twig->addFunction(new TwigFunction($name, $callable));
            }
        }
        foreach ($this->options['globals'] as $extension) {
            $this->twig->addGlobal($extension);
        }
    }
    public function render(string $template, array $data = [], bool $is_string = false): string
    {
        try {

            return $this->twig->render($template, $data);

        } catch (\Throwable $th) {

            throw new ViewException(sprintf('%s in file %s line %d', $th->getMessage(), $th->getFile(), $th->getLine()), 5050, $th);

        }
    }
    public function string(string $code, array $data = []): ?string
    {
        return $this->twig->createTemplate($code)->render($data);
    }
    public function template(string $template, array $data = []): string
    {
        return $this->render($template, $data);
    }

}
