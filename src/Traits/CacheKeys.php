<?php

namespace LaravelReady\LicenseConnector\Traits;

trait CacheKeys
{
    /**
     * Get access token cache key
     *
     * @return string
     */
    private static function getAccessTokenKey(string $licenseKey): string
    {
        return "license-connector:access-token-{$licenseKey}";
    }
}
