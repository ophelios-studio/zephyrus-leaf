<?php

declare(strict_types=1);

namespace Leaf\Localization;

use Latte\Extension;
use Latte\Runtime\Template;
use Zephyrus\Localization\Translator;

/**
 * Latte extension that provides translation support in templates.
 *
 * Injects the following variables into every template:
 *
 *   {$trans('key')}          — Translate a key using the current locale
 *   {$trans('key', [...])}   — Translate with parameter interpolation
 *   {$currentLocale}         — The active locale code (e.g. "en", "fr")
 *   {$supportedLocales}      — Array of all supported locale codes
 *
 * The current locale can be switched at runtime via setCurrentLocale(),
 * which is used by the StaticSiteBuilder to generate pages for each locale.
 */
final class TranslationLatteExtension extends Extension
{
    private string $currentLocale;

    /**
     * @param list<string> $supportedLocales
     */
    public function __construct(
        private readonly Translator $translator,
        string $defaultLocale,
        private readonly array $supportedLocales,
    ) {
        $this->currentLocale = $defaultLocale;
    }

    public function getCurrentLocale(): string
    {
        return $this->currentLocale;
    }

    public function setCurrentLocale(string $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function beforeRender(Template $template): void
    {
        $translator = $this->translator;
        $locale = $this->currentLocale;
        $supportedLocales = $this->supportedLocales;

        $trans = static fn (string $key, array $parameters = []): string
            => $translator->trans($key, $parameters, $locale);

        $existing = $template->getParameters();

        $setter = \Closure::bind(function () use ($trans, $locale, $supportedLocales, $existing): void {
            if (!array_key_exists('trans', $existing)) {
                $this->params['trans'] = $trans;
            }
            if (!array_key_exists('currentLocale', $existing)) {
                $this->params['currentLocale'] = $locale;
            }
            if (!array_key_exists('supportedLocales', $existing)) {
                $this->params['supportedLocales'] = $supportedLocales;
            }
        }, $template, Template::class);

        $setter();
    }
}
