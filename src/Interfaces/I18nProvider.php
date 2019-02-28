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
     * 
     * @return string
     */
    public function getLocale(): string;
    /** 
     * return namespace for package langs
     * 
     * @return string|null
     */
    public function getNamespace(): ?string;
}