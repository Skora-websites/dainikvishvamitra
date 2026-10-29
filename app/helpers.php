<?php

/**
 * Site-wide global presentation helpers.
 *
 * These back-compat wrappers around App\Support\Site are registered via the
 * composer "files" autoload so they exist for every request (including the CLI
 * test bootstrap), matching the legacy includes/functions.php contract that the
 * blade views rely on (e.g. excerpt()).
 */

use App\Support\Site;

if (! function_exists('excerpt')) {
    /**
     * Plain-text, multibyte-safe excerpt of an HTML string.
     * Delegates to Site::excerpt().
     */
    function excerpt(?string $html, int $len = 160): string
    {
        return Site::excerpt($html, $len);
    }
}
