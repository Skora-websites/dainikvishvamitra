<?php

namespace App\Support;

use App\Models\Setting;
use Carbon\Carbon;

/**
 * Site-wide presentation helpers ported from the legacy `includes/functions.php`:
 * Hindi numerals/dates, slugs, excerpts, settings cache.
 */
class Site
{
    /** Hindi (Devanagari) digits ०–९. */
    private const HI_DIGITS = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];

    /** Latin → Devanagari digit conversion. strtr() would corrupt multibyte targets byte-wise. */
    public static function hiDigits(string $s): string
    {
        return str_replace(range(0, 9), self::HI_DIGITS, $s);
    }

    public static function hiWeekday(int $w): string
    {
        return ['रविवार', 'सोमवार', 'मंगलवार', 'बुधवार', 'गुरुवार', 'शुक्रवार', 'शनिवार'][$w] ?? '';
    }

    public static function hiMonth(int $m): string
    {
        return ['', 'जनवरी', 'फ़रवरी', 'मार्च', 'अप्रैल', 'मई', 'जून', 'जुलाई', 'अगस्त', 'सितंबर', 'अक्टूबर', 'नवंबर', 'दिसंबर'][$m] ?? '';
    }

    /** e.g. "गुरुवार, ८ अक्टूबर २०२५" */
    public static function hiDate(mixed $ts = null): string
    {
        $c = $ts instanceof Carbon ? $ts : Carbon::parse($ts ?: 'now')->timezone('Asia/Kolkata');

        return self::hiDigits($c->format('j')).' '.self::hiMonth((int) $c->format('n')).' '.self::hiDigits($c->format('Y'));
    }

    /** "गुरुवार, ८ अक्टूबर" — weekday omitted; used where year is shown separately. */
    public static function hiDateShort(mixed $ts = null): string
    {
        $c = $ts instanceof Carbon ? $ts : Carbon::parse($ts ?: 'now')->timezone('Asia/Kolkata');

        return self::hiWeekday((int) $c->format('w')).', '.self::hiDigits($c->format('j')).' '.self::hiMonth((int) $c->format('n'));
    }

    /**
     * "४० मिनट पूर्व" style relative time — exact port of legacy hindi_time_ago():
     * Devanagari digits, fixed thresholds, "पूर्व" suffix, hindi date past a week.
     */
    public static function timeAgo(mixed $datetime): string
    {
        if (empty($datetime)) {
            return '';
        }

        $ts = $datetime instanceof Carbon
            ? $datetime->timezone('Asia/Kolkata')->getTimestamp()
            : (int) strtotime((string) $datetime);

        $diff = time() - $ts;

        if ($diff < 60) {
            return 'अभी-अभी';
        }
        if ($diff < 3600) {
            return self::hiDigits((string) (int) ($diff / 60)).' मिनट पूर्व';
        }
        if ($diff < 86400) {
            return self::hiDigits((string) (int) ($diff / 3600)).' घंटा पूर्व';
        }

        $days = (int) ($diff / 86400);
        if ($days === 1) {
            return 'कल';
        }
        if ($days < 7) {
            return self::hiDigits((string) $days).' दिन पूर्व';
        }

        return self::hiDateShort($ts);
    }

    /** URL slug from any string (keeps Devanagari + latin + digits). */
    public static function slugify(string $s): string
    {
        // Normalize accents, dashes, and quotes without destroying Devanagari.
        if (class_exists(\Normalizer::class)) {
            $s = \Normalizer::normalize($s, \Normalizer::FORM_C);
        }
        $s = strip_tags($s);
        $s = mb_strtolower($s);
        $s = preg_replace('/[^\p{L}\p{N}]+/u', '-', $s) ?? '';
        $s = preg_replace('/-+/', '-', $s) ?? '';

        return trim($s, '-');
    }

    /** Plain-text excerpt, multibyte safe. */
    public static function excerpt(?string $html, int $len = 160): string
    {
        $text = trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES, 'UTF-8'));

        if (mb_strlen($text) <= $len) {
            return $text;
        }

        return mb_substr($text, 0, $len).'…';
    }

    /** Settings key/value, cached per request. */
    public static function setting(string $key, string $default = ''): string
    {
        static $cache = null;
        if ($cache === null) {
            $cache = [];
            try {
                foreach (Setting::all() as $row) {
                    $cache[$row->key] = $row->value;
                }
            } catch (\Throwable $e) {
                // Table may not exist yet during installation.
            }
        }

        return $cache[$key] ?? $default;
    }

    /** Deterministic reading-time estimate (Hindi ≈ 180 wpm). */
    public static function readMinutes(?string $body): int
    {
        $words = str_word_count(strip_tags((string) $body), 0, '\x{0900}-\x{097F}') ?: 1;

        return max(1, (int) ceil($words / 180));
    }

    /** Pagination window (mirrors legacy paginate()). */
    public static function pagination(int $total, int $perPage, int $page): array
    {
        $last = max(1, (int) ceil($total / max(1, $perPage)));
        $page = max(1, min($page, $last));
        $start = max(1, $page - 2);
        $end = min($last, $page + 2);

        return [
            'last_page' => $last,
            'current' => $page,
            'start' => $start,
            'end' => $end,
            'has_prev' => $page > 1,
            'has_next' => $page < $last,
            'prev' => $page - 1,
            'next' => $page + 2,
        ];
    }

    /**
     * 11-char YouTube video ID from a watch/youtu.be/shorts/embed/live link or a
     * bare ID; null when the input is not a recognizable link or ID.
     */
    public static function youtubeId(string $input): ?string
    {
        $input = trim($input);
        if ($input === '') {
            return null;
        }
        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:live/|shorts/|embed/|v/|watch\?(?:.*&)?v=))([A-Za-z0-9_-]{11})~i', $input, $m)) {
            return $m[1];
        }

        return preg_match('/^[A-Za-z0-9_-]{11}$/', $input) ? $input : null;
    }
}
