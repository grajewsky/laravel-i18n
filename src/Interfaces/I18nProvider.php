<?php 


namespace Grajewsky\Laravel\I18n\Interfaces;


interface I18nProvider {

    /**
     * Return path to lang assets
     * 
     * @return string
     */
    public function getI18nPath(): string;
    /**
     * Get specific locale
     */
    public function getLocale(): string;
}