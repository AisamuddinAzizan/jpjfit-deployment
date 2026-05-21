<?php

namespace App\Services;

use App\Models\LandingPageImage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class LandingPageImageService
{
    public const SECTION_HERO = 'hero';

    /**
     * Default hero slide images used when no DB images are available.
     *
     * @return list<string>
     */
    public function defaultHeroSlides(): array
    {
        return [
            'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1800&q=80&fm=webp',
            'https://images.unsplash.com/photo-1486218119243-13883505764c?auto=format&fit=crop&w=1800&q=80&fm=webp',
            'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&w=1800&q=80&fm=webp',
            'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1800&q=80&fm=webp',
        ];
    }

    /**
     * Return hero slide payload for landing page rendering with fallback defaults.
     *
     * @return list<array{url:string}>
     */
    public function heroSlides(): array
    {
        $images = $this->heroImages();

        if ($images->isEmpty()) {
            return array_map(static fn (string $url): array => ['url' => $url], $this->defaultHeroSlides());
        }

        return $images
            ->map(static fn (LandingPageImage $image): array => ['url' => $image->image_url])
            ->values()
            ->all();
    }

    /**
     * Return uploaded hero images for admin management page.
     */
    public function heroImages(): Collection
    {
        if (! Schema::hasTable('landing_page_images')) {
            return new Collection();
        }

        return LandingPageImage::query()
            ->where('section', self::SECTION_HERO)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function uploadHeroImage(UploadedFile $image, ?int $sortOrder = null): LandingPageImage
    {
        $path = $image->store('landing/hero', 'public');

        return LandingPageImage::query()->create([
            'section' => self::SECTION_HERO,
            'image_path' => $path,
            'sort_order' => max(0, (int) ($sortOrder ?? 0)),
        ]);
    }

    public function deleteImage(LandingPageImage $landingPageImage): void
    {
        if (! str_starts_with($landingPageImage->image_path, 'http')
            && Storage::disk('public')->exists($landingPageImage->image_path)) {
            Storage::disk('public')->delete($landingPageImage->image_path);
        }

        $landingPageImage->delete();
    }
}
