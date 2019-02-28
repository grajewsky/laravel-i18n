<?php 

namespace Grajewsky\Laravel\I18n\Providers;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Grajewsky\Laravel\I18n\Interfaces\I18nProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class I18nServiceProvider extends ServiceProvider
{
    /**
     * @var Array<stdClass::class>
     */
    private $i18nPathProviders;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/assets/js' => resource_path('assets/js/vendor'),
            __DIR__.'/../config/i18n.php' => config_path('i18n.php'),
        ]);

        $this->i18nPathProviders = Config::get('i18n.providers');
        if ($this->i18nPathProviders == null) {
            $config = include __DIR__.'/../config/i18n.php';
            $this->i18nPathProviders = $config['providers'];
        }
        
        $translations = $this->loadFromPathProviders();
        Blade::directive('translations', function ($key) use ($translations) {
            return sprintf('<script>window[%s] = %s</script>', $key ?: "'translations'", $translations);
        });
    }

    /**
     * Get the translations.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function translations(string $langPath) {
        $files = File::files($langPath);

        $result = collect($files)->flatMap(function ($file) {
            return [
                ($translation = $file->getBasename('.php')) => include $file->getPath() . "/". $file->getFilename() /*trans($translation)*/,
            ];
        });
        return $result;
    }
    protected function loadFromPathProviders(): Collection {
        $translations = new Collection();
        foreach ($this->i18nPathProviders as $pathProvider) {
            /** @var I18nProvider */
            $provider = new $pathProvider;
            if ($provider instanceof I18nProvider) {
                $result = array();
                if (!is_null($provider->getNamespace())) {
                    $result[$provider->getNamespace()] = $this->loadFromPathProvider($provider);
                } else {
                    $result = $this->loadFromPathProvider($provider);
                }
                $translations = $translations->merge($result);
            }
         }
         return $translations;
    }
    protected function loadFromPathProvider(I18nProvider $pathProvider): Collection {
        return $this->translations($pathProvider->getI18nPath() . sprintf("/%s%s", $pathProvider->getLocale(), "/"));
    }
}
