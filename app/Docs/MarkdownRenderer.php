<?php

namespace App\Docs;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalink;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\NodeIterator;
use League\CommonMark\Node\StringContainerHelper;
use Phiki\Adapters\CommonMark\PhikiExtension;
use Phiki\Theme\Theme;

/**
 * Turns one docs page into HTML on the server, so the article and its highlighted
 * code are in the first response.
 */
final class MarkdownRenderer
{
    private readonly MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'heading_permalink' => [
                'apply_id_to_heading' => true,
                'html_class' => 'docs-anchor',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'min_heading_level' => 2,
                'max_heading_level' => 3,
                'symbol' => '#',
                'aria_hidden' => true,
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addExtension(new FrontMatterExtension);
        $environment->addExtension(new HeadingPermalinkExtension);
        $environment->addExtension(new PhikiExtension(['light' => Theme::GithubLight, 'dark' => Theme::GithubDarkDimmed]));

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * @return array{title: string, description: string, blocks: list<array{type: 'html', html: string}|array{type: 'example'|'flow', name: string}>, headings: list<array{id: string, text: string, level: int}>, text: string}
     */
    public function render(string $markdown): array
    {
        $rendered = $this->converter->convert($markdown);
        $frontMatter = $rendered instanceof RenderedContentWithFrontMatter ? (array) $rendered->getFrontMatter() : [];
        $headings = [];

        foreach ($rendered->getDocument()->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if ($node instanceof Heading && $node->getLevel() >= 2 && $node->getLevel() <= 3) {
                $permalink = $node->firstChild();

                if ($permalink instanceof HeadingPermalink) {
                    $headings[] = ['id' => $permalink->getSlug(), 'text' => StringContainerHelper::getChildText($node), 'level' => $node->getLevel()];
                }
            }
        }

        $html = preg_replace_callback(
            '/<blockquote>\s*<p>\[!(NOTE|TIP|WARNING)\]\s*/',
            fn (array $match): string => '<blockquote class="docs-callout" data-kind="'.strtolower($match[1]).'"><p>',
            $rendered->getContent(),
        ) ?? '';

        $html = preg_replace_callback(
            '/<!--\s*screenshot:\s*([\w-]+)\s*\|\s*(.+?)\s*-->/',
            fn (array $match): string => $this->screenshot($match[1], $match[2]),
            $html,
        ) ?? '';

        // A `demo:/path` link opens that screen of the demo, on whichever host it runs.
        $html = str_replace('href="demo:', 'href="https://'.config('xelqun.demo_domain'), $html);

        return [
            'title' => (string) ($frontMatter['title'] ?? ''),
            'description' => (string) ($frontMatter['description'] ?? ''),
            'blocks' => $this->blocks($html),
            'headings' => $headings,
            'text' => trim((string) preg_replace('/\s+/', ' ', strip_tags($html))),
        ];
    }

    /**
     * The same screen in both themes, and the page shows the one that matches. Each
     * is captured at twice its size, so half of that is the largest it stays sharp.
     */
    private function screenshot(string $name, string $alt): string
    {
        $image = function (string $theme) use ($name, $alt): string {
            [$width, $height] = getimagesize(public_path("images/docs/panels/{$name}-{$theme}.jpg")) ?: [0, 0];

            return sprintf(
                '<img src="/images/docs/panels/%1$s-%2$s.jpg" alt="%3$s" data-theme="%2$s" loading="lazy" width="%4$d" height="%5$d">',
                $name,
                $theme,
                e($alt),
                intdiv($width, 2),
                intdiv($height, 2),
            );
        };

        return '<figure class="docs-shot">'.$image('light').$image('dark').'</figure>';
    }

    /**
     * A page is HTML with live examples between it, marked `<!-- example: name -->`,
     * and animated diagrams, marked `<!-- flow: name -->`.
     *
     * @return list<array{type: 'html', html: string}|array{type: 'example'|'flow', name: string}>
     */
    private function blocks(string $html): array
    {
        $blocks = [];
        $parts = preg_split('/<!--\s*(example|flow):\s*([\w\/-]+)\s*-->/', $html, -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];

        // The split gives the HTML, then the kind and name of each marker, then the HTML after it.
        foreach (array_chunk($parts, 3) as $chunk) {
            if (trim($chunk[0]) !== '') {
                $blocks[] = ['type' => 'html', 'html' => $chunk[0]];
            }

            if (isset($chunk[2])) {
                $blocks[] = ['type' => $chunk[1] === 'flow' ? 'flow' : 'example', 'name' => $chunk[2]];
            }
        }

        return $blocks;
    }
}
