<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DocsBuild extends Command
{
    protected $signature = 'docs:build';

    protected $description = 'Render the markdown in docs/ to static HTML in public/docs/';

    private string $sourcePath;

    private string $outputPath;

    public function handle(): int
    {
        $this->sourcePath = base_path('docs');
        $this->outputPath = public_path('docs');

        if (! File::isDirectory($this->sourcePath)) {
            $this->error("Source directory does not exist: {$this->sourcePath}");

            return CommandAlias::FAILURE;
        }

        File::deleteDirectory($this->outputPath);
        File::makeDirectory($this->outputPath, recursive: true);

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $files = collect(File::allFiles($this->sourcePath))
            ->filter(fn ($file) => $file->getExtension() === 'md')
            ->values();

        $nav = $this->buildNav($files);

        foreach ($files as $file) {
            $relative = Str::of($file->getRelativePathname())->replace('\\', '/');

            $markdown = File::get($file->getPathname());
            $html = (string) $converter->convert($markdown);
            $html = $this->rewriteLinks($html);

            $title = $this->extractTitle($markdown) ?? $relative->before('.md')->replace('/', ' - ')->toString();

            $view = view('docs.layout', [
                'title' => $title,
                'content' => $html,
                'nav' => $nav,
                'currentPath' => $relative->replace('.md', '.html')->toString(),
            ])->render();

            $isRootReadme = $relative->toString() === 'README.md';
            $destination = $isRootReadme
                ? $this->outputPath.'/index.html'
                : $this->outputPath.'/'.$relative->replace('.md', '.html');

            File::ensureDirectoryExists(dirname($destination));
            File::put($destination, $view);
        }

        $this->info("Built {$files->count()} pages into {$this->outputPath}");

        return CommandAlias::SUCCESS;
    }

    /**
     * Build a simple nav grouped by top-level folder. Root-level files
     * (Overview.md, Sections.md, GET.md, README.md) are listed separately.
     */
    private function buildNav($files): array
    {
        $groups = [];
        $root = [];

        foreach ($files as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());

            if ($relative === 'README.md') {
                continue;
            }

            if (! str_contains($relative, '/')) {
                $root[] = [
                    'label' => Str::before($relative, '.md'),
                    'path' => Str::replaceLast('.md', '.html', $relative),
                ];

                continue;
            }

            [$folder, $verbFile] = explode('/', $relative, 2);

            $groups[$folder][] = [
                'label' => Str::before($verbFile, '.md'),
                'path' => Str::replaceLast('.md', '.html', $relative),
            ];
        }

        ksort($groups);

        foreach ($groups as &$verbs) {
            usort($verbs, fn ($a, $b) => $a['label'] <=> $b['label']);
        }

        return [
            'root' => $root,
            'groups' => $groups,
        ];
    }

    private function extractTitle(string $markdown): ?string
    {
        if (preg_match('/^#\s+(.+)$/m', $markdown, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Rewrite markdown-relative .md links to the .html files this command
     * generates, including root-absolute links (e.g. "/resource/GET.md")
     * which need the /docs prefix since the site is mounted under /docs.
     */
    private function rewriteLinks(string $html): string
    {
        $html = preg_replace(
            '/href="\/([^"]+?)\.md(#[^"]*)?"/',
            'href="/docs/$1.html$2"',
            $html
        );

        $html = preg_replace(
            '/href="((?!https?:\/\/|\/docs\/)[^"]+?)\.md(#[^"]*)?"/',
            'href="$1.html$2"',
            $html
        );

        return $html;
    }
}
