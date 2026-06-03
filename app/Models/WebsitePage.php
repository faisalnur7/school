<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsitePage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'page_type',
        'status',
        'excerpt',
        'cover_image',
        'content',
        'published_at',
    ];

    public const SYSTEM_PAGE_DEFINITIONS = [
        'home' => [
            'label' => 'Home',
            'icon' => 'fa-home',
            'color' => 'primary',
            'route' => 'website.home',
        ],
        'about' => [
            'label' => 'About Us',
            'icon' => 'fa-info-circle',
            'color' => 'purple',
            'route' => 'website.about',
        ],
        'academics' => [
            'label' => 'Academics',
            'icon' => 'fa-book-open',
            'color' => 'indigo',
            'route' => 'website.page.show',
        ],
        'admission' => [
            'label' => 'Admission',
            'icon' => 'fa-user-plus',
            'color' => 'success',
            'route' => 'website.page.show',
        ],
        'notices' => [
            'label' => 'Notice Board',
            'icon' => 'fa-bell',
            'color' => 'warning',
            'route' => 'website.notices',
        ],
        'events' => [
            'label' => 'Events',
            'icon' => 'fa-calendar',
            'color' => 'danger',
            'route' => 'website.events',
        ],
        'news-events' => [
            'label' => 'News / Events',
            'icon' => 'fa-calendar-day',
            'color' => 'danger',
            'route' => 'website.page.show',
        ],
        'calendar' => [
            'label' => 'Academic Calendar',
            'icon' => 'fa-calendar-alt',
            'color' => 'info',
            'route' => 'website.academic-calendar',
        ],
        'results' => [
            'label' => 'Results / Exam Information',
            'icon' => 'fa-clipboard-check',
            'color' => 'teal',
            'route' => 'website.page.show',
        ],
        'gallery' => [
            'label' => 'Gallery',
            'icon' => 'fa-images',
            'color' => 'primary',
            'route' => 'website.gallery',
        ],
        'teachers-staff' => [
            'label' => 'Teachers / Staff',
            'icon' => 'fa-chalkboard-teacher',
            'color' => 'secondary',
            'route' => 'website.page.show',
        ],
        'contact' => [
            'label' => 'Contact Us',
            'icon' => 'fa-envelope',
            'color' => 'success',
            'route' => 'website.contact',
        ],
        'downloads' => [
            'label' => 'Downloads / Forms',
            'icon' => 'fa-file-download',
            'color' => 'dark',
            'route' => 'website.page.show',
        ],
        'facilities' => [
            'label' => 'Facilities',
            'icon' => 'fa-building',
            'color' => 'info',
            'route' => 'website.page.show',
        ],
        'policies' => [
            'label' => 'Policies / Rules',
            'icon' => 'fa-gavel',
            'color' => 'warning',
            'route' => 'website.page.show',
        ],
    ];

    public const SYSTEM_PAGES = [
        'home',
        'about',
        'academics',
        'admission',
        'notices',
        'events',
        'news-events',
        'calendar',
        'results',
        'gallery',
        'teachers-staff',
        'contact',
        'downloads',
        'facilities',
        'policies',
    ];

    public function isSystemPage(): bool
    {
        return in_array($this->page_type, self::SYSTEM_PAGES);
    }

    public static function systemPageDefinitions(): array
    {
        return self::SYSTEM_PAGE_DEFINITIONS;
    }

    public static function systemPageMeta(string $type): array
    {
        return self::SYSTEM_PAGE_DEFINITIONS[$type] ?? [
            'label' => ucfirst(str_replace('-', ' ', $type)),
            'icon' => 'fa-file',
            'color' => 'secondary',
            'route' => 'website.page.show',
        ];
    }

    public static function publicUrlFor(self $page): string
    {
        return route(self::publicRouteNameFor($page), self::publicRouteParamsFor($page));
    }

    public static function publicRouteNameFor(self $page): string
    {
        return match ($page->page_type) {
            'home' => 'website.home',
            'about' => 'website.about',
            'notices' => 'website.notices',
            'events' => 'website.events',
            'calendar' => 'website.academic-calendar',
            'gallery' => 'website.gallery',
            'contact' => 'website.contact',
            default => 'website.page.show',
        };
    }

    public static function publicRouteParamsFor(self $page): array
    {
        return match ($page->page_type) {
            'home', 'about', 'notices', 'events', 'calendar', 'gallery', 'contact' => [],
            default => ['slug' => $page->slug],
        };
    }

    public static function publicUrlFromType(string $type, ?string $slug = null): string
    {
        $meta = self::systemPageMeta($type);

        return match ($meta['route']) {
            'website.home' => route('website.home'),
            'website.about' => route('website.about'),
            'website.notices' => route('website.notices'),
            'website.events' => route('website.events'),
            'website.academic-calendar' => route('website.academic-calendar'),
            'website.gallery' => route('website.gallery'),
            'website.contact' => route('website.contact'),
            default => route('website.page.show', $slug ?? $type),
        };
    }

    public static function systemNavigationTypes(): array
    {
        return self::SYSTEM_PAGES;
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function sections()
    {
        return $this->hasMany(WebsiteSection::class)->orderBy('sort_order');
    }
}
