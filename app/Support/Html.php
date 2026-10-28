<?php

namespace App\Support;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allowlist sanitizer for rich-text article/page bodies.
 *
 * The legacy implementation used strip_tags() with an allowlist, which has two
 * problems this class fixes:
 *
 *  1. strip_tags() drops every tag outside the allowlist but keeps their
 *     *text* content. A <table> therefore collapsed to a bare run of cell
 *     text, and <s>/<code>/<sub> lost their markup — the editor offered
 *     formatting the database silently threw away.
 *  2. strip_tags() cannot filter attributes at all, so it had to be paired
 *     with regexes to strip event handlers and javascript: URLs. Those
 *     regexes are bypassable (e.g. unquoted or oddly-quoted attributes).
 *
 * This walks the DOM instead: disallowed elements are removed *along with
 * their subtree*, and every surviving element gets its attributes filtered
 * against a per-tag allowlist.
 */
final class Html
{
    /**
     * Tags kept in a body, mapped to the attributes each may carry.
     * Attribute names are lowercase.
     */
    private const ALLOWED = [
        'p' => ['style' => true],
        'br' => [],
        'b' => [],
        'strong' => [],
        'i' => [],
        'em' => [],
        'u' => [],
        's' => [],
        'strike' => [],
        'mark' => [],
        'sub' => [],
        'sup' => [],
        'code' => [],
        'pre' => [],
        'h2' => ['style' => true],
        'h3' => ['style' => true],
        'h4' => ['style' => true],
        'h5' => ['style' => true],
        'h6' => ['style' => true],
        'ul' => [],
        'ol' => ['start' => true],
        'li' => [],
        'blockquote' => [],
        'a' => ['href' => true, 'title' => true, 'target' => true, 'rel' => true, 'class' => true],
        'img' => ['src' => true, 'alt' => true, 'title' => true, 'width' => true, 'height' => true, 'loading' => true, 'class' => true, 'style' => true],
        'figure' => ['class' => true, 'style' => true],
        'figcaption' => ['class' => true, 'style' => true],
        'hr' => [],
        'span' => ['style' => true],
        'div' => ['style' => true],
        'table' => [],
        'thead' => [],
        'tbody' => [],
        'tfoot' => [],
        'tr' => [],
        'th' => ['colspan' => true, 'rowspan' => true],
        'td' => ['colspan' => true, 'rowspan' => true],
    ];

    /**
     * CSS properties permitted in an inline `style`, with their allowed values.
     * `*` means any safe value (lengths/percentages/auto/none) the property accepts.
     *
     * Alignment (text-align, float, margin, max-width) must survive the round trip.
     * Everything else is dropped: an allowlist of properties rather than a regex
     * over the whole string.
     */
    private const SAFE_STYLES = [
        'text-align' => ['left', 'center', 'right', 'justify'],
        'float' => ['left', 'right', 'none'],
        'clear' => ['left', 'right', 'both', 'none'],
        'display' => ['block', 'inline-block', 'inline', 'flex'],
        'margin' => '*',
        'margin-left' => '*',
        'margin-right' => '*',
        'margin-top' => '*',
        'margin-bottom' => '*',
        'max-width' => '*',
        'width' => '*',
        'height' => '*',
    ];

    /** URL schemes permitted in href/src. */
    private const SAFE_SCHEMES = ['http', 'https', 'mailto', 'tel', 'ftp'];

    /**
     * Sanitize a rich-text body.
     */
    public static function clean(?string $html): string
    {
        return self::sanitize((string) $html, self::ALLOWED);
    }

    /**
     * Sanitize an HTML fragment, returning only the sanitized body markup.
     *
     * @param  array<string, array<string, true>>  $allowed  tag => attribute allowlist
     */
    public static function sanitize(string $html, array $allowed): string
    {
        $html = trim($html);
        if ($html === '') {
            return '';
        }

        $doc = self::parse($html);
        if ($doc === null) {
            return '';
        }

        $root = $doc->getElementById('__html_root__');
        if (! $root instanceof DOMElement) {
            return '';
        }

        self::cleanChildren($root, $allowed);

        $out = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    /**
     * Parse an HTML fragment into a UTF-8 DOMDocument with a wrapper element.
     */
    private static function parse(string $html): ?DOMDocument
    {
        $doc = new DOMDocument;

        // Force UTF-8 interpretation — without this hint DOMDocument reads
        // bytes as ISO-8859-1 and mangles every Devanagari character.
        $prefix = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
        $wrapped = $prefix.'<div id="__html_root__">'.$html.'</div>';

        $previous = libxml_use_internal_errors(true);
        $loaded = $doc->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return null;
        }

        return $doc;
    }

    /**
     * Recursively filter a node's children: disallowed elements are dropped
     * with their subtree, allowed elements have their attributes filtered.
     *
     * @param  array<string, array<string, true>>  $allowed
     */
    private static function cleanChildren(DOMNode $node, array $allowed): void
    {
        // Snapshot: the live NodeList shifts as we remove nodes.
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->nodeName);

                if (! isset($allowed[$tag])) {
                    // Drop the element *and its contents*. Keeping the text
                    // (what strip_tags did) leaks table cells and script
                    // bodies into the article as loose prose.
                    $node->removeChild($child);

                    continue;
                }

                self::cleanAttributes($child, $allowed[$tag]);
                self::cleanChildren($child, $allowed);
            } elseif ($child instanceof \DOMComment || $child instanceof \DOMProcessingInstruction) {
                $node->removeChild($child);
            } elseif ($child instanceof \DOMText) {
                // Keep text as-is; entities are re-escaped on save.
                continue;
            } else {
                // CDATA and other exotic node types have no place in a body.
                $node->removeChild($child);
            }
        }
    }

    /**
     * Strip every attribute not explicitly allowed, and neutralize unsafe URLs.
     *
     * @param  array<string, true>  $allowedAttributes
     */
    private static function cleanAttributes(DOMElement $el, array $allowedAttributes): void
    {
        // Collect first: removing while iterating a live NamedNodeMap skips entries.
        $attrs = [];
        foreach (iterator_to_array($el->attributes) as $attr) {
            $attrs[] = $attr;
        }

        foreach ($attrs as $attr) {
            /** @var DOMAttr $attr */
            $name = strtolower($attr->nodeName);

            // Event handlers (onclick, onerror, ...) are never in the allowlist.
            if (! isset($allowedAttributes[$name])) {
                $el->removeAttribute($attr->nodeName);

                continue;
            }

            if ($name === 'href' || $name === 'src') {
                $value = trim($attr->nodeValue);
                if (! self::isSafeUrl($value)) {
                    $el->removeAttribute($attr->nodeName);
                }
            }

            if ($name === 'target') {
                // Only _blank is useful, and it must carry rel="noopener".
                if (strtolower(trim($attr->nodeValue)) !== '_blank') {
                    $el->removeAttribute($attr->nodeName);
                } else {
                    $el->setAttribute('rel', 'noopener noreferrer');
                }
            }

            if ($name === 'style') {
                $safe = self::cleanStyle($attr->nodeValue);
                if ($safe === '') {
                    $el->removeAttribute($attr->nodeName);
                } else {
                    $el->setAttribute('style', $safe);
                }
            }
        }
    }

    /**
     * Filter an inline style down to the allowed properties and values.
     */
    private static function cleanStyle(?string $style): string
    {
        $style = (string) $style;
        if (trim($style) === '') {
            return '';
        }

        $kept = [];

        foreach (explode(';', $style) as $declaration) {
            if (! str_contains($declaration, ':')) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $declaration, 2));
            $property = strtolower($property);
            $value = strtolower($value);

            if (! isset(self::SAFE_STYLES[$property])) {
                continue;
            }

            $allowedValues = self::SAFE_STYLES[$property];
            if (is_array($allowedValues)) {
                if (! in_array($value, $allowedValues, true)) {
                    continue;
                }
            } elseif ($allowedValues === '*') {
                // Ensure value is safe: only numbers, units, auto, none, %, spaces.
                // Disallow urls, javascript, expressions, parentheses, quotes, or semi-colons.
                if (! preg_match('/^[0-9a-z%.\s\-]+$/i', $value) || preg_match('/(url|expression|javascript)/i', $value)) {
                    continue;
                }
            } else {
                continue;
            }

            $kept[] = $property.': '.$value;
        }

        return implode('; ', $kept);
    }

    /**
     * True when a URL is safe to emit: no scheme (relative), or a known-safe
     * scheme. Blocks javascript:, data:, vbscript: and their obfuscations.
     */
    private static function isSafeUrl(string $url): bool
    {
        if ($url === '') {
            return false;
        }

        // Strip control characters and whitespace that can hide a scheme,
        // e.g. "java\tscript:alert(1)" or a leading "\n".
        $probe = preg_replace('/[\s\x00-\x1F\x7F]+/', '', $url) ?? '';
        $probe = strtolower($probe);

        // Relative URLs, fragments, and protocol-relative URLs have no scheme.
        if ($probe === '' || $probe[0] === '/' || $probe[0] === '#' || $probe[0] === '?') {
            return true;
        }

        // A scheme must be followed by ':'. Anything before it is the scheme.
        if (! preg_match('/^([a-z][a-z0-9+.-]*):/', $probe, $m)) {
            // No scheme at all — a relative path such as "uploads/x.jpg".
            return true;
        }

        return in_array($m[1], self::SAFE_SCHEMES, true);
    }
}
