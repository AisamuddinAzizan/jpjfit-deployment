<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLandingPageImageRequest;
use App\Http\Requests\UpdateLandingPageContentRequest;
use App\Models\LandingPageImage;
use App\Services\LandingPageContentService;
use App\Services\LandingPageImageService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LandingPageContentController extends Controller
{
    public function edit(
        LandingPageContentService $landingPageContentService,
        LandingPageImageService $landingPageImageService,
    ): View
    {
        return view('landing-page-content.edit', [
            'fieldGroups' => $landingPageContentService->fieldGroups(),
            'locales' => $landingPageContentService->supportedLocales(),
            'contentByLocale' => $landingPageContentService->allLocales(),
            'heroImages' => $landingPageImageService->heroImages(),
            'defaultHeroSlides' => $landingPageImageService->defaultHeroSlides(),
        ]);
    }

    public function update(UpdateLandingPageContentRequest $request, LandingPageContentService $landingPageContentService): RedirectResponse
    {
        $validated = $request->validated();

        $landingPageContentService->save($validated['content'] ?? []);

        return redirect()
            ->route('landing-content.edit')
            ->with('success', 'Landing page content updated successfully.');
    }

    public function storeHeroImage(
        StoreLandingPageImageRequest $request,
        LandingPageImageService $landingPageImageService,
    ): RedirectResponse {
        $validated = $request->validated();

        $landingPageImageService->uploadHeroImage(
            $validated['image'],
            isset($validated['sort_order']) ? (int) $validated['sort_order'] : null,
        );

        return redirect()
            ->route('landing-content.edit')
            ->with('success', 'Hero slider image uploaded successfully.');
    }

    public function destroyHeroImage(
        LandingPageImage $landingPageImage,
        LandingPageImageService $landingPageImageService,
    ): RedirectResponse {
        if ($landingPageImage->section !== LandingPageImageService::SECTION_HERO) {
            return redirect()
                ->route('landing-content.edit')
                ->with('error', 'Only hero images can be removed from this section.');
        }

        $landingPageImageService->deleteImage($landingPageImage);

        return redirect()
            ->route('landing-content.edit')
            ->with('success', 'Hero slider image removed successfully.');
    }
}
