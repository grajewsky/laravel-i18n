<?php

namespace Grajewsky\Laravel\I18n\Providers;

use Grajewsky\Laravel\I18n\Interfaces\I18nPathProvider;
use Illuminate\Support\Facades\App;

class LaravelI18nProvider implements I18nPathProvider {

    public function getLocale(): string {
        
        return App::getLocale();
    }
    public function getI18nPath(): string {
        return resource_path('lang');
    }
    
}