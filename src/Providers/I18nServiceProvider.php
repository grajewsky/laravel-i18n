<?php 

namespace Grajewsky\Laravel\I18n\Providers;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Grajewsky\Laravel\Interfaces\I18nPathProvider;
use Illuminate\Support\Collection;

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
        
        $translations = $this->loadFromPathProviders();



        Blade::directive('translations', function ($key) {
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

        $result =  collect($files)->flatMap(function ($file) {
            return [
                ($translation = $file->getBasename('.php')) => trans($translation),
            ];
        });
        return $result;
    }
    protected function loadFromPathProviders(): Collection {
        $translations = new Collection();
        foreach ($this->i18nPathProviders as $pathProvider) {
            $provider = new $pathProvider;
            if ($provider instanceof I18nPathProvider) {
                $translations = $translations->merge($this->loadFromPathProvider($provider));
            }
         }
         return $translations;
    }
    protected function loadFromPathProvider(I18nPathProvider $pathProvider): Collection {
        return $this->translations($pathProvider->getI18nPath() . sprintf("/%s%s", $pathProvider->getLocale(), "/"));
    }
}
