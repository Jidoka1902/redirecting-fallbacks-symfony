<?php

namespace Jidoka1902\RedirectingFallbacksSymfony\UrlGenerator;


use Jidoka1902\RedirectingFallbacks\UrlGenerator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class FrameworkUrlGeneratorAdapter
 * @package Jidoka1902\RedirectingFallbacks\Resolver
 */
class FrameworkUrlGeneratorAdapter implements UrlGenerator
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * takes the name of a configured symfony router route
     * @param string $route
     * @return string
     */
    public function generate(string $route): string
    {
        return $this->router->generate($route, array(),UrlGeneratorInterface::ABSOLUTE_PATH);
    }


}