<?php 


namespace Grajewsky\Laravel\Interfaces;


interface I18nPathProvider {

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