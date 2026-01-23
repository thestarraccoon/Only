<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class DetectLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $this->detectLocale($request);
        App::setLocale($locale);

        return $next($request);
    }

    private function detectLocale(Request $request): string
    {
        $locale = $request->getPreferredLanguage($this->getSupportedLocales());
        if ($locale) {
            return $locale;
        }

        return Config::get('app.fallback_locale');
    }

    private function getSupportedLocales(): array
    {
        return array_keys(Config::get('app.supported_locales', []));
    }
}
