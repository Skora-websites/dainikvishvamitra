<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

/**
 * Port of legacy take_slot(): pulls articles assigned to a home/category slot,
 * never repeats an already-used id, and backfills from the newest matching
 * articles so a section always has content.
 */
class SlotFiller
{
    /** @var array<int,bool> */
    private array $used = [];

    public function __construct()
    {
        // Seed with already-shown ids if the caller pre-registered any.
    }

    /** Reserve an id so later slots never reuse it. */
    public function reserve(int $id): void
    {
        $this->used[$id] = true;
    }

    /**
     * @return Collection<int, Article>
     */
    public function take(string $col, string $slot, int $limit, ?string $catSlug = null, bool $withImage = false)
    {
        $orderCol = $col === 'home_slot' ? 'home_order' : 'cat_order';

        $base = Article::published()
            ->where($col, $slot)
            ->where('published_at', '<=', now())
            ->when($catSlug, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $catSlug)))
            ->when($this->used, fn ($q) => $q->whereNotIn('id', array_keys($this->used)));

        $out = (clone $base)->orderBy($orderCol)->orderByDesc('published_at')->limit($limit)->get();

        foreach ($out as $a) {
            $this->reserve($a->id);
        }

        $need = $limit - $out->count();
        if ($need > 0) {
            $fill = Article::published()
                ->where('published_at', '<=', now())
                ->where('is_opinion', false)
                ->when($catSlug, fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $catSlug)))
                ->when($withImage, fn ($q) => $q->whereNotNull('image')->where('image', '!=', ''))
                ->when($this->used, fn ($q) => $q->whereNotIn('id', array_keys($this->used)))
                ->orderByDesc('published_at')
                ->limit($need)
                ->get();

            foreach ($fill as $a) {
                $this->reserve($a->id);
            }
            $out = $out->concat($fill);
        }

        return $out;
    }
}
