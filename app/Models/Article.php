<?php

namespace App\Models;

use App\Support\Locale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    /** Column names for the legacy `created_at`/`updated_at` (DB DEFAULT CURRENT_TIMESTAMP). */
    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /** Explicitly managed in SQL — no client-side maintenance. */
    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [
        'category_id', 'title', 'title_en', 'language', 'slug', 'excerpt', 'excerpt_en', 'body', 'body_en',
        'image', 'image_alt', 'image_caption',
        'author_name', 'author_role', 'author_image', 'location', 'badge_label', 'badge_color', 'stat_text',
        'is_breaking', 'is_featured', 'is_opinion', 'is_visual', 'home_slot', 'home_order',
        'cat_slot', 'cat_order', 'time_label', 'read_minutes', 'views', 'comment_count',
        'status', 'published_at', 'meta_title', 'meta_desc',
    ];

    protected $casts = [
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'is_opinion' => 'boolean',
        'is_visual' => 'boolean',
        'read_minutes' => 'integer',
        'views' => 'integer',
        'comment_count' => 'integer',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'author_name' => 'विशेष संवाददाता ब्यूरो',
        'badge_color' => 'bg-primary',
        'status' => 'draft',
        'language' => 'hi',
    ];

    // ---- Bilingual content accessors ----
    /**
     * Title in the visitor's display language, falling back to the other
     * language when that translation is missing. Raw values via getRawOriginal().
     */
    public function getTitleAttribute(mixed $value): mixed
    {
        $hi = $value === null || $value === '' ? null : $value;
        $en = $this->title_en === null || $this->title_en === '' ? null : $this->title_en;

        return Locale::isEnglish() ? ($en ?? $hi) : ($hi ?? $en);
    }

    public function getExcerptAttribute(mixed $value): mixed
    {
        $hi = $value === null || $value === '' ? null : $value;
        $en = $this->excerpt_en === null || $this->excerpt_en === '' ? null : $this->excerpt_en;

        return Locale::isEnglish() ? ($en ?? $hi) : ($hi ?? $en);
    }

    public function getBodyAttribute(mixed $value): mixed
    {
        $hi = $value === null || $value === '' ? null : $value;
        $en = $this->body_en === null || $this->body_en === '' ? null : $this->body_en;

        return Locale::isEnglish() ? ($en ?? $hi) : ($hi ?? $en);
    }

    // ---- Relationships ----
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function keyPoints()
    {
        return $this->hasMany(KeyPoint::class, 'article_id')->orderBy('sort_order')->orderBy('id');
    }

    // ---- Query scopes ----
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published');
    }

    public function scopeBreaking(Builder $q): Builder
    {
        return $q->where('is_breaking', true);
    }

    public function scopeOpinion(Builder $q): Builder
    {
        return $q->where('is_opinion', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('is_featured', true);
    }

    public function scopeNewest(Builder $q): Builder
    {
        return $q->orderByRaw('COALESCE(published_at, created_at) DESC')->orderByDesc('id');
    }

    /** Articles curated into a specific home page slot, ordered. */
    public function scopeHomeSlot(Builder $q, string $slot): Builder
    {
        return $q->where('home_slot', $slot)->orderBy('home_order')->orderByDesc('id');
    }

    /** Articles curated into a category page slot (lead|grid|dossier), ordered. */
    public function scopeCatSlot(Builder $q, string $slot): Builder
    {
        return $q->where('cat_slot', $slot)->orderBy('cat_order')->orderByDesc('id');
    }

    // ---- Accessors ----
    /** Category label with a safe fallback (works even if the relation is missing). */
    public function getCatNameAttribute(): string
    {
        return (string) (optional($this->category)->name_hi ?? '');
    }

    /** Category slug with a safe fallback. */
    public function getCatSlugAttribute(): string
    {
        return (string) (optional($this->category)->slug ?? '');
    }

    /** Full URL for the article page. */
    public function getUrlAttribute(): string
    {
        return url('article/'.$this->slug);
    }

    /** Full URL for the article image, falling back to the shared placeholder. */
    public function getImageUrlAttribute(): string
    {
        $p = trim((string) $this->image);
        if ($p === '') {
            return asset('assets/img/hero-cec.jpg');
        }
        if (preg_match('#^https?://#i', $p) || str_starts_with($p, 'data:')) {
            return $p;
        }

        return asset(ltrim($p, '/'));
    }

    /** Same resolution rules for the author photo. */
    public function getAuthorImageUrlAttribute(): ?string
    {
        $p = trim((string) $this->author_image);
        if ($p === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $p) || str_starts_with($p, 'data:')) {
            return $p;
        }

        return asset(ltrim($p, '/'));
    }

    /** Meta `<title>` with fallback to the article title. */
    public function getMetaTitleResolvedAttribute(): string
    {
        return (string) ($this->meta_title ?: $this->title);
    }

    /** Meta description with fallback to the excerpt. */
    public function getMetaDescResolvedAttribute(): string
    {
        return (string) ($this->meta_desc ?: $this->excerpt);
    }

    /** Fixed relative-time label; falls back to a computed relative time from published_at. */
    public function getTimeDisplayAttribute(): string
    {
        $label = trim((string) $this->time_label);
        if ($label !== '') {
            return $label;
        }
        if ($this->published_at) {
            return $this->published_at->diffForHumans(['parts' => 1, 'locale' => 'hi']);
        }

        return '';
    }
}
