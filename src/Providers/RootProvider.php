<?php
namespace TiagoAlves\LicenSysConectorPackpage\Providers;
use Illuminate\Support\ServiceProvider;

class RootProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(dirname(__DIR__, 1).'/routes/web.php');
        $this->publishes([
            dirname(__DIR__).'/config/licensys_api.php' => config_path('licensys_api.php'),
        ], 'licensys_conector_packpage');

    }
    public function register()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/config/licensys_api.php',
            'licensys_api'
        );
    }
}
