<?php

namespace App\Template;

use Charcoal\Cms\AbstractWebTemplate;

/**
 * Base Template Controller
 */
abstract class AbstractTemplate extends AbstractWebTemplate
{
    /**
     * Static assets versionning.
     *
     * @var string
     */
    public const ASSETS_VERSION = '1.0.0';

    const CORE_TEMPLATE_ACTIONS = [
        'debug',
        'siteName',
        'google',
        'typekit',
        'htmlAttrStructure',
        'htmlAttr',
        'assetsVersion',
        'copyright',
        'copyrightName',
        'copyrightYear',
        'criticalCss',
        'templateName',
        'title',
        'currentLocale',
        'currentUrl',
        'currentLanguage',
        'metaTitle',
        'metaDescription',
        // 'metaImage',
        'opengraphType',
        // 'opengraphImage',
        'seoMetadata',
        'baseUrl',
        'availableLanguages',
        'hasSeoMetadata',
        'documentTitle',
        'hasAlternateTranslations',
        'alternateTranslations',
    ];

    /**
     * Gets the data keys on this entity.
     * @see
     *
     * @return array
     */
    public function keys($keys = null)
    {
        $methods = $this->retrieveDynamicMethods();
        return array_merge(self::CORE_TEMPLATE_ACTIONS, $methods);
    }

    private function retrieveDynamicMethods()
    {
        $reflectionClass = new \ReflectionClass($this);
        $reflectionMethods = $reflectionClass->getMethods();
        $methodKeys = [];
        foreach ($reflectionMethods as $reflectionMethod) {
            if (substr($reflectionMethod->class, 0, 3) !== 'App') {
               continue;
            }

            if (substr($reflectionMethod->name, 0, 3) === 'get') {
               $methodKeys[] = lcfirst(substr($reflectionMethod->name, 3));
            }
        }
        return $methodKeys;
    }


    // Site Head/Metatags
    // ============================================================

    /**
     * @see Charcoal\Cms\Support\LocaleAwareTrait
     *
     * Format an alternate translation URL for the given translatable model.
     *
     * Note: The application's locale is already modified and will be reset
     * after processing all available languages.
     *
     * @param  mixed $context      The translated {@see \Charcoal\Model\ModelInterface model}
     *     or array-accessible structure.
     * @param  array $localeStruct The currently iterated language.
     * @return string Returns a link.
     */
    protected function formatAlternateTranslationUrl($context, array $localeStruct)
    {
        $isRoutable = ($context instanceof RoutableInterface && $context->isActiveRoute());
        $langCode   = $localeStruct['code'];
        $path       = ($isRoutable ? $context->url($langCode) : ($this->currentUrl() ? : $langCode));

        if ($path instanceof UriInterface) {
            $path = $path->getPath();
        }

        // Overrwrite LocaleAwareTrait to parse the url translation path with string for Twig
        return (string) $this->baseUrl()->withPath($path);
    }

    /**
     * Retrieve the site name.
     *
     * @return string|null
     */
    public function siteName()
    {
        return $this->appConfig('project_name');
    }



    // APIs
    // ============================================================

    /**
     * @return string
     */
    public function google()
    {
        return $this->appConfig('apis.google');
    }

    /**
     * @return string
     */
    public function typekit()
    {
        return $this->appConfig('apis.typekit');
    }



    // Presentation & Templating
    // =========================================================================

    /**
     * Retrieve the <html> element attribute structure.
     *
     * @return array
     */
    protected function htmlAttrStructure()
    {
        $classes = [ 'has-no-js' ];

        return [
            'class'         => $classes,
            'lang'          => $this->currentLanguage(),
            'data-template' => $this->templateName(),
            'data-debug'    => $this->debug() ? 'true' : false,
        ];
    }

    /**
     * Generate a string containing HTML attributes for the <html> element.
     *
     * @return string
     */
    public function htmlAttr()
    {
        return html_build_attributes($this->htmlAttrStructure());
    }

    /**
     * Retrieve the assets version for cache busting.
     *
     * @return string
     */
    public function assetsVersion()
    {
        return self::ASSETS_VERSION;
    }

    /**
     * @return string
     */
    public function copyright()
    {
        return sprintf('© %s %s', $this->copyrightYear(), $this->copyrightName());
    }

    /**
     * @return string
     */
    public function copyrightName()
    {
        return $this->appConfig('project_name');
    }

    /**
     * Retrieve current year (for copyright info).
     *
     * @return string
     */
    public function copyrightYear()
    {
        return date('Y');
    }



    // Front-end helpers
    // ============================================================

    /**
     * Loop X number of times.
     *
     * @return array
     */
    public function forLoop()
    {
        $i = 0;
        $max = 50;
        $out = [];
        for (; $i < $max; $i++) {
            $k = 1;
            $mini = [];
            for (; $k <= $i; $k++) {
                $mini[] = $k;
            }

            $out[(string)$i] = $mini;
        }

        return $out;
    }

    /**
     * Retrieve the critical stylesheet to inject in the markup.
     *
     * @return string
     */
    public function criticalCss()
    {
        $filePath = $this->appConfig()->publicPath() . 'assets/styles/critical.css';
        if (file_exists($filePath)) {
            ob_start();
            echo file_get_contents($filePath);
            return ob_get_clean();
        }

        return '';
    }
}
