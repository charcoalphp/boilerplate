<?php

namespace App\Template;

use Charcoal\Cms\AbstractWebTemplate;

use ReflectionClass;
use ReflectionMethod;

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

    /**
     * Gets the data keys on this entity.
     *
     * @return array
     */
    public function keys()
    {
        $methods = $this->getTemplateMethods();
        return array_merge([
            'alternateTranslations',
            'assetsVersion',
            'availableLanguages',
            'baseUrl',
            'copyright',
            'copyrightName',
            'copyrightYear',
            'criticalCss',
            'currentLanguage',
            'currentLocale',
            'currentUrl',
            'debug',
            'documentTitle',
            'google',
            'hasAlternateTranslations',
            'hasSeoMetadata',
            'htmlAttr',
            'htmlAttrStructure',
            'metaTitle',
            'metaDescription',
            'opengraphType',
            'seoMetadata',
            'siteName',
            'templateName',
            'title',
        ], $methods);
    }

    /**
     * Retrieve the template's public methods.
     *
     * @return string[]
     */
    private function getTemplateMethods(): array
    {
        $reflectionClass   = new ReflectionClass($this);
        $reflectionMethods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        $keys = [];
        foreach ($reflectionMethods as $reflectionMethod) {
            /** Include only methods in the {@see \App} namespace. */
            if (substr($reflectionMethod->class, 0, 3) !== 'App') {
                continue;
            }

            /** Include only methods with the following prefixes. */
            if (substr($reflectionMethod->name, 0, 3) === 'get') {
                $keys[] = lcfirst(substr($reflectionMethod->name, 3));
            } elseif (preg_match('/^(has|hide|is|show)/', $reflectionMethod->name)) {
                $keys[] = lcfirst(substr($reflectionMethod->name, 3));
            }
        }

        return $keys;
    }


    // Site Head/Metatags
    // ============================================================

    /**
     * Format an alternate translation for the given translatable model.
     *
     * Note: The application's locale is already modified and will be reset
     * after processing all available languages.
     *
     * @param  mixed $context      The translated {@see \Charcoal\Model\ModelInterface model}
     *     or array-accessible structure.
     * @param  array $localeStruct The currently iterated language.
     * @return array Returns a link structure.
     */
    protected function formatAlternateTranslation($context, array $localeStruct)
    {
        return array_replace(
            parent::formatAlternateTranslation($context, $localeStruct),
            [ 'url'      => (string)$this->formatAlternateTranslationUrl($context, $localeStruct), ]
        );
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
