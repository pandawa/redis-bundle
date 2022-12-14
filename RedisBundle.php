<?php

declare(strict_types=1);

namespace Pandawa\Bundle\RedisBundle;

use Illuminate\Contracts\Redis\Factory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Arr;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Contracts\Foundation\HasPluginInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisBundle extends Bundle implements HasPluginInterface, DeferrableProvider
{
    protected array $deferred = [
        'redis',
        'redis.connection',
        Factory::class,
    ];

    public function register(): void
    {
        $this->app->singleton('redis', function ($app) {
            $config = $app->make('config');

            $options = [
                ...Arr::except($config->get('redis', []), ['connections']),
                ...$config->get('redis.connections', [])
            ];

            return new RedisManager($app, $options['client'], $options);
        });

        $this->app->alias('redis', Factory::class);
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }
}
