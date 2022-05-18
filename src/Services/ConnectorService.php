<?php

namespace LaravelReady\LicenseConnector\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

use LaravelReady\LicenseConnector\Traits\CacheKeys;

class ConnectorService
{
    use CacheKeys;

    public function __construct()
    {
    }

    /**
     * Get access token for the given domain
     *
     * @param string $licenseKey
     *
     * @return string
     */
    public static function getAccessToken(string $licenseKey): null | string
    {
        $accessTokenCacheKey = self::getAccessTokenKey($licenseKey);

        $accessToken = Cache::get($accessTokenCacheKey, null);

        if ($accessToken) {
            return $accessToken;
        }

        $url = Config::get('license-connector.license_server_url') . '/api/license-server/auth/login';

        $response = Http::withHeaders([
            'x-host' => Config::get('app.url'),
            'x-host-name' => Config::get('app.name'),
        ])->post($url, [
            'license_key' => $licenseKey
        ]);

        if ($response->ok()) {
            $data = $response->json();

            if (isset($data['status'])) {
                if ($data['status'] === true) {
                    if (isset($data['access_token'])) {
                        $accessToken = $data['access_token'];

                        Cache::put($accessTokenCacheKey, $accessToken, now()->addMinutes(60));

                        return $accessToken;
                    }
                } else if ($data['status'] === true) {
                    if (isset($data['message'])) {
                        return $data['message'];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check license status
     *
     * @param string $licenseKey
     *
     * @return boolean
     */
    public static function validateLicense(string $licenseKey)
    {
        $accessToken = self::getAccessToken($licenseKey);

        $url = Config::get('license-connector.license_server_url') . '/api/license-server/license';

        $response = Http::withHeaders([
            'x-host' => Config::get('app.url'),
            'x-host-name' => Config::get('app.name'),
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get($url);

        if ($response->ok()) {
            $data = $response->json();

            return $data['status'] == 'active';
        }

        return false;
    }
}
