<?php
namespace TiagoAlves\LicenSysConectorPackpage\Providers;
use Illuminate\Support\ServiceProvider;

class RootProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(dirname(__DIR__, 1).'/routes/web.php');
    }
    public function register()
    {

    }
}
