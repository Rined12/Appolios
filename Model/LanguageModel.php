<?php

/**
 * Loads UI translation arrays from /lang — views use $lang only (no direct file/session access).
 */
class LanguageModel
{
    /** @var list<string> */
    private array $allowed = ['fr', 'en', 'ar'];

    private function defaultLang(): string
    {
        $d = defined('DEFAULT_LOCALE') ? strtolower(trim((string) DEFAULT_LOCALE)) : 'fr';

        return $this->isValid($d) ? $d : 'fr';
    }

    public function getAvailableLanguages(): array
    {
        return $this->allowed;
    }

    public function isValid(string $lang): bool
    {
        return in_array(strtolower($lang), $this->allowed, true);
    }

    public function getTranslations(string $lang): array
    {
        $lang = strtolower($lang);
        if (!$this->isValid($lang)) {
            $lang = $this->defaultLang();
        }
        $file = dirname(__DIR__) . '/lang/' . $lang . '.php';
        if (is_file($file)) {
            /** @var array<string, string> $data */
            $data = require $file;

            return is_array($data) ? $data : [];
        }

        $fallback = dirname(__DIR__) . '/lang/' . $this->defaultLang() . '.php';

        return is_file($fallback) ? (require $fallback) : [];
    }

    public function getCurrentLang(): string
    {
        $code = $_SESSION['lang'] ?? $this->defaultLang();
        $code = is_string($code) ? strtolower($code) : $this->defaultLang();

        return $this->isValid($code) ? $code : $this->defaultLang();
    }

    public function setLang(string $lang): void
    {
        if ($this->isValid($lang)) {
            $_SESSION['lang'] = strtolower($lang);
        }
    }

    /**
     * Safe internal route for redirect after language switch (?url=...).
     */
    public function sanitizeReturnRoute(string $route): string
    {
        $route = trim($route, '/');
        if ($route === '') {
            return 'home/index';
        }
        if (str_contains($route, '..')) {
            return 'home/index';
        }
        if (!preg_match('#^[a-zA-Z0-9][a-zA-Z0-9\-_/]*$#', $route)) {
            return 'home/index';
        }

        return $route;
    }
}
