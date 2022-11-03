<?php

namespace App\ServiceProvider;

use Charcoal\Email\ServiceProvider\EmailServiceProvider;
use Charcoal\Model\ServiceProvider\ModelServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Twig\Extension\DebugExtension;

/**
 * App Service Provider
 */
class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * @param  Container $container A service container.
     * @return void
     */
    public function register(Container $container)
    {
        $container->register(new EmailServiceProvider());
        $container->register(new ModelServiceProvider());

        $container->extend('view/mustache/helpers', function (array $helpers): array {
            $helper = [
                /**
                 * Retrieve the current date/time.
                 *
                 * @return array
                 */
                'now' => [
                    'year' => date('Y'),
                ],
            ];

            return array_merge($helpers, $helper);
        });

        /**
         * Extend global helpers for the Twig Engine.
         *
         * @param  array     $helpers   The Mustache helper collection.
         * @param  Container $container A container instance.
         * @return array
         */
        $container->extend('view/twig/helpers', function (array $helpers, Container $container) {
            return array_merge(
                $helpers,
                [ new DebugExtension() ],
            );
        });
    }
}
