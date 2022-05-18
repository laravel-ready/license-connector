<?php

namespace LaravelReady\LicenseConnector\Facades;

use Illuminate\Support\Facades\Facade;

class LicenseConnector extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'license-connector';
    }
}
