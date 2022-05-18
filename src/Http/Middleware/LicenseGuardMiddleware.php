<?php

namespace LaravelReady\LicenseConnector\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

use LaravelReady\LicenseConnector\Support\DomainSupport;

class LicenseGuardMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->header('_Host');
        $hostName = $request->header('_Host_Name');

        if ($host && $hostName) {
            $domain = DomainSupport::validateDomain($host);
            $subDomain = $domain->subDomain()->toString();

            if (Config::get('license-server.allow_subdomains') && !empty($subDomain)) {
                $request->merge([
                    'domain' => $subDomain,
                ]);

                return $next($request);
            }

            $registrableDomain = $domain->registrableDomain()->toString();

            if (!empty($registrableDomain)) {
                $request->merge([
                    'domain' => $registrableDomain,
                ]);

                return $next($request);
            }
        }

        return abort(403);
    }
}
