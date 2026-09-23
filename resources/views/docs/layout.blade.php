<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Costs to Expect API Docs</title>
    <link rel="icon" sizes="48x48" href="/images/theme/favicon.ico">
    <link href="/css/landing.css?v={{ filemtime(public_path('css/landing.css')) }}" rel="stylesheet"/>
    <link href="/docs/pagefind/pagefind-ui.css" rel="stylesheet"/>
</head>
<body class="bg-white">
    <div class="mx-auto max-w-7xl px-6 py-8 lg:flex lg:gap-x-8">
        <nav class="lg:w-64 shrink-0 mb-8 lg:mb-0">
            <div class="flex items-center justify-between lg:block">
                <a href="/docs/" class="block font-semibold text-gray-900 mb-4">Costs to Expect API Docs</a>

                <button type="button" id="docs-nav-toggle" class="lg:hidden mb-4 text-sm text-gray-600 hover:text-gray-900" aria-expanded="false" aria-controls="docs-nav-links">
                    Menu &#9662;
                </button>
            </div>

            <div id="docs-search" class="mb-6"></div>

            <div id="docs-nav-links" class="hidden lg:block">
                <ul class="text-sm space-y-1 mb-6">
                    @foreach ($nav['root'] as $item)
                        <li>
                            <a href="/docs/{{ $item['path'] }}"
                               class="{{ $currentPath === $item['path'] ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ $item['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                @foreach ($nav['groups'] as $folder => $items)
                    <div class="mb-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-1">{{ $folder }}</p>
                        <ul class="text-sm space-y-1">
                            @foreach ($items as $item)
                                <li>
                                    <a href="/docs/{{ $item['path'] }}"
                                       class="{{ $currentPath === $item['path'] ? 'text-indigo-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </nav>

        <main class="min-w-0 flex-1 prose" data-pagefind-body>
            {!! $content !!}
        </main>
    </div>

    <script>
        (function () {
            var toggle = document.getElementById('docs-nav-toggle');
            var links = document.getElementById('docs-nav-links');

            toggle.addEventListener('click', function () {
                var isHidden = links.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(!isHidden));
            });
        })();
    </script>
    <script src="/docs/pagefind/pagefind-ui.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            new PagefindUI({ element: '#docs-search', showSubResults: true, showImages: false });
        });
    </script>
</body>
</html>
