<?php

namespace App\ServiceProvider;

use Charcoal\Email\ServiceProvider\EmailServiceProvider;
use Charcoal\Model\ServiceProvider\ModelServiceProvider;
use DI\Container;
use Twig\Extension\DebugExtension;
use Charcoal\App\ServiceProvider\AppServiceProvider as CharcoalAppServiceProvider;

/**
 * App Service Provider
 */
class AppServiceProvider extends CharcoalAppServiceProvider
{
    /**
     * @param  Container $container A service container.
     * @return void
     */
    public function register(Container $container)
    {
        (new EmailServiceProvider())->register($container);
        (new ModelServiceProvider())->register($container);

        $container->set('view/mustache/helpers', function (Container $container): array {
            $helpers = [];

            if ($container->has('view/mustache/helpers')) {
                $helpers = $container->get('view/mustache/helpers');
            }

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
        $container->set('view/twig/helpers', function (Container $container): array {
            $helpers = [];

            if ($container->has('view/twig/helpers')) {
                $helpers = $container->get('view/twig/helpers');
            }

            return array_merge(
                $helpers,
                [ new DebugExtension() ],
            );
        });
    }
}
