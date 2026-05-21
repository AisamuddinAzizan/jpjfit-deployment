<x-app-layout>
    <style>
        .locale-tab-btn {
            background-color: transparent;
            color: #475569;
        }

        .locale-tab-btn:hover {
            background-color: #f1f5f9;
        }

        .locale-tab-btn.is-active {
            background-color: #0f766e !important;
            color: #ffffff !important;
        }

        .locale-tab-btn.is-active:hover {
            background-color: #115e59 !important;
        }
    </style>

    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800">{{ __('Landing Page Content') }}</h1>
            <p class="text-sm text-slate-500">{{ __('Edit each landing-page section here. Saved values override defaults.') }}</p>
        </div>
    </x-slot>

    <section class="panel-card p-5">
        <h2 class="text-lg font-bold text-slate-800">{{ __('Hero Slider Images') }}</h2>
        <p class="mt-1 text-sm text-slate-500">
            {{ __('Upload hero slider images and remove any image you no longer need. If all uploaded images are removed, default slider images will be used automatically.') }}
        </p>

        <form method="POST" action="{{ route('landing-content.hero-images.store') }}" enctype="multipart/form-data" class="mt-4 grid gap-4 md:grid-cols-3">
            @csrf

            <div class="md:col-span-2">
                <label class="text-sm font-medium text-slate-700" for="hero_image">{{ __('Hero Image') }}</label>
                <input id="hero_image" name="image" type="file" accept="image/*" class="form-input" required>
                @error('image')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700" for="hero_sort_order">{{ __('Sort Order') }}</label>
                <input id="hero_sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="form-input">
                @error('sort_order')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-3">
                <button type="submit" class="btn-primary">{{ __('Upload Hero Image') }}</button>
            </div>
        </form>

        @if($heroImages->isNotEmpty())
            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($heroImages as $heroImage)
                    <article class="rounded-xl border border-slate-200 bg-white p-3">
                        <img src="{{ $heroImage->image_url }}" alt="Hero slider image" class="h-36 w-full rounded-lg object-cover">

                        <div class="mt-3 flex items-center justify-between gap-2 text-xs text-slate-500">
                            <span>{{ __('Order') }}: {{ $heroImage->sort_order }}</span>
                            <span>#{{ $heroImage->id }}</span>
                        </div>

                        <form method="POST" action="{{ route('landing-content.hero-images.destroy', $heroImage) }}" class="mt-3" onsubmit="return confirm('Remove this hero image?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary w-full justify-center">{{ __('Remove Image') }}</button>
                        </form>
                    </article>
                @endforeach
            </div>
        @else
            <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                {{ __('No uploaded hero images yet. The landing page is using default slider images now.') }}
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($defaultHeroSlides as $defaultSlide)
                    <img src="{{ $defaultSlide }}" alt="Default hero slide" class="h-28 w-full rounded-lg object-cover">
                @endforeach
            </div>
        @endif
    </section>

    @php
        $localeLabels = [
            'en' => 'English',
            'ms' => 'Malay',
        ];
    @endphp

    <form method="POST" action="{{ route('landing-content.update') }}" class="space-y-6">
        @csrf
        @method('PUT')
        @foreach($fieldGroups as $group)
            @php
                $sectionIndex = $loop->index;
                $errorInMalay = false;

                foreach ($group['fields'] as $field) {
                    if ($errors->has('content.ms.'.$field['key'])) {
                        $errorInMalay = true;
                        break;
                    }
                }

                $activeLocale = old('section_locale.'.$sectionIndex, $errorInMalay ? 'ms' : 'en');
            @endphp

            <section class="panel-card p-5" data-section-tabs="{{ $sectionIndex }}">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-bold text-slate-800">{{ $group['title'] }}</h2>

                    <div class="inline-flex overflow-hidden rounded-lg border border-slate-200 bg-white" role="tablist" aria-label="{{ $group['title'] }} language tabs">
                        @foreach($locales as $locale)
                            <button
                                type="button"
                                role="tab"
                                data-locale-tab="{{ $locale }}"
                                aria-selected="{{ $activeLocale === $locale ? 'true' : 'false' }}"
                                class="locale-tab-btn px-4 py-2 text-sm font-semibold transition {{ $activeLocale === $locale ? 'is-active' : '' }}"
                            >
                                {{ __($localeLabels[$locale] ?? strtoupper($locale)) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="section_locale[{{ $sectionIndex }}]" value="{{ $activeLocale }}" data-active-locale>

                @foreach($locales as $locale)
                    <div data-locale-pane="{{ $locale }}" class="mt-4 {{ $activeLocale === $locale ? '' : 'hidden' }}">
                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($group['fields'] as $field)
                                @php
                                    $inputName = 'content.'.$locale.'.'.$field['key'];
                                    $inputId = 'content_'.$sectionIndex.'_'.$locale.'_'.$field['key'];
                                    $value = old($inputName, $contentByLocale[$locale][$field['key']] ?? '');
                                    $isTextarea = ($field['type'] ?? 'text') === 'textarea';
                                @endphp

                                <div class="{{ $isTextarea ? 'md:col-span-2' : '' }}">
                                    <label class="text-sm font-medium text-slate-700" for="{{ $inputId }}">{{ $field['label'] }}</label>

                                    @if($isTextarea)
                                        <textarea
                                            id="{{ $inputId }}"
                                            name="content[{{ $locale }}][{{ $field['key'] }}]"
                                            rows="3"
                                            class="form-input"
                                        >{{ $value }}</textarea>
                                    @else
                                        <input
                                            id="{{ $inputId }}"
                                            name="content[{{ $locale }}][{{ $field['key'] }}]"
                                            type="text"
                                            value="{{ $value }}"
                                            class="form-input"
                                        >
                                    @endif

                                    @error($inputName)
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>
        @endforeach

        <div class="panel-card p-5">
            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="btn-primary">{{ __('Save Landing Content') }}</button>
                <a href="{{ route('home') }}" target="_blank" class="btn-secondary">{{ __('Preview Landing Page') }}</a>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-section-tabs]').forEach((section) => {
                const tabButtons = section.querySelectorAll('[data-locale-tab]');
                const panes = section.querySelectorAll('[data-locale-pane]');
                const activeLocaleInput = section.querySelector('[data-active-locale]');

                const setActiveLocale = (locale) => {
                    tabButtons.forEach((button) => {
                        const isActive = button.getAttribute('data-locale-tab') === locale;
                        button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                        button.classList.toggle('is-active', isActive);
                    });

                    panes.forEach((pane) => {
                        const isActive = pane.getAttribute('data-locale-pane') === locale;
                        pane.classList.toggle('hidden', !isActive);
                    });

                    if (activeLocaleInput) {
                        activeLocaleInput.value = locale;
                    }
                };

                tabButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        setActiveLocale(button.getAttribute('data-locale-tab'));
                    });
                });
            });
        });
    </script>
</x-app-layout>
