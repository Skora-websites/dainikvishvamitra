<?php

namespace Tests\Unit;

use App\Support\Html;
use PHPUnit\Framework\TestCase;

/**
 * The rich-text body sanitizer.
 *
 * Regression guard for the table/strikethrough/code data loss: the previous
 * strip_tags()-based allowlist removed those tags but kept their text, so a
 * table saved from the editor came back as loose prose and the formatting
 * was lost for good.
 */
class HtmlSanitizerTest extends TestCase
{
    public function test_it_preserves_tables(): void
    {
        $html = '<table><tbody><tr><th><p>Head</p></th></tr><tr><td><p>Cell</p></td></tr></tbody></table>';

        $clean = Html::clean($html);

        $this->assertStringContainsString('<table>', $clean);
        $this->assertStringContainsString('<th>', $clean);
        $this->assertStringContainsString('<td>', $clean);
        $this->assertStringContainsString('Cell', $clean);
    }

    public function test_it_preserves_strikethrough_and_code(): void
    {
        $clean = Html::clean('<p><s>struck</s> and <code>x = 1</code></p>');

        $this->assertStringContainsString('<s>struck</s>', $clean);
        $this->assertStringContainsString('<code>x = 1</code>', $clean);
    }

    public function test_it_drops_disallowed_elements_with_their_subtree(): void
    {
        // A script's *text* must not survive as visible prose.
        $clean = Html::clean('<p>Safe</p><script>alert("xss")</script>');

        $this->assertStringContainsString('<p>Safe</p>', $clean);
        $this->assertStringNotContainsString('alert', $clean);
        $this->assertStringNotContainsString('<script', $clean);
    }

    public function test_it_strips_event_handler_attributes(): void
    {
        $clean = Html::clean('<p onclick="steal()">Text</p><img src="/a.jpg" onerror="steal()">');

        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringContainsString('<img src="/a.jpg">', $clean);
    }

    public function test_it_neutralizes_javascript_urls(): void
    {
        $clean = Html::clean('<a href="javascript:alert(1)">click</a>');

        $this->assertStringNotContainsString('javascript:', $clean);
    }

    public function test_it_keeps_safe_links_and_adds_noopener_to_blank(): void
    {
        $clean = Html::clean('<a href="https://example.com" target="_blank">link</a>');

        $this->assertStringContainsString('href="https://example.com"', $clean);
        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_it_preserves_devanagari_text(): void
    {
        $clean = Html::clean('<p>मानसून सत्र की शुरुआत, विपक्ष ने उठाए मुद्दे</p>');

        $this->assertStringContainsString('मानसून सत्र की शुरुआत', $clean);
    }

    public function test_it_preserves_image_alt_and_dimensions(): void
    {
        $clean = Html::clean('<img src="/uploads/a.jpg" alt="चित्र" width="800" height="600">');

        $this->assertStringContainsString('alt="चित्र"', $clean);
        $this->assertStringContainsString('width="800"', $clean);
    }

    public function test_it_preserves_image_alignment_classes_and_styles(): void
    {
        $leftFig = '<figure class="align-left" style="float: left; margin: 0 1.5rem 1rem 0; max-width: 50%"><img src="/uploads/news/pic.jpg" alt="test"><figcaption>विवरण</figcaption></figure>';
        $cleanLeft = Html::clean($leftFig);
        $this->assertStringContainsString('class="align-left"', $cleanLeft);
        $this->assertStringContainsString('float: left', $cleanLeft);
        $this->assertStringContainsString('max-width: 50%', $cleanLeft);
        $this->assertStringContainsString('<figcaption>विवरण</figcaption>', $cleanLeft);

        $rightFig = '<figure class="align-right" style="float: right; margin: 0 0 1rem 1.5rem"><img src="/uploads/news/pic.jpg" alt="test"></figure>';
        $cleanRight = Html::clean($rightFig);
        $this->assertStringContainsString('class="align-right"', $cleanRight);
        $this->assertStringContainsString('float: right', $cleanRight);

        $centerFig = '<figure class="align-center" style="text-align: center; margin: 1.5rem auto"><img src="/uploads/news/pic.jpg" alt="test"></figure>';
        $cleanCenter = Html::clean($centerFig);
        $this->assertStringContainsString('class="align-center"', $cleanCenter);
        $this->assertStringContainsString('text-align: center', $cleanCenter);
    }

    public function test_it_strips_unsafe_styles_while_preserving_safe_alignments(): void
    {
        $dirty = '<figure style="float: left; position: fixed; background: url(javascript:alert(1)); max-width: 50%"><img src="/pic.jpg"></figure>';
        $clean = Html::clean($dirty);
        $this->assertStringContainsString('float: left', $clean);
        $this->assertStringContainsString('max-width: 50%', $clean);
        $this->assertStringNotContainsString('position', $clean);
        $this->assertStringNotContainsString('background', $clean);
        $this->assertStringNotContainsString('javascript', $clean);
    }

    public function test_it_returns_empty_string_for_empty_input(): void
    {
        $this->assertSame('', Html::clean(''));
        $this->assertSame('', Html::clean(null));
    }
}
