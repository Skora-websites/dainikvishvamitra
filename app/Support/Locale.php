<?php

namespace App\Support;

/**
 * Active display language for the public site. Defaults to Hindi — the site's
 * primary language; visitors opt into English via the header switcher, which
 * stores the choice in the session (route: language.switch).
 */
class Locale
{
    public const HI = 'hi';

    public const EN = 'en';

    /**
     * Current display locale ('hi' or 'en').
     */
    public static function current(): string
    {
        return session('locale') === self::EN ? self::EN : self::HI;
    }

    public static function isEnglish(): bool
    {
        return self::current() === self::EN;
    }
}
