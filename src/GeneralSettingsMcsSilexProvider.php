<?php

namespace GeneralSettingsMcs;

use Silex\Application;
use Silex\ServiceProviderInterface;

class GeneralSettingsMcsSilexProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     *
     * @return Processor
     */
    public function register(Application $app)
    {
        $app['hatch-is.general-settings-mcs.processor'] = $app->share(
            function () use ($app) {
                return new Processor(
                    $app['hatch-is.general-settings-mcs.endpoint']
                );
            }
        );
    }

    public function boot(Application $app) {}
}