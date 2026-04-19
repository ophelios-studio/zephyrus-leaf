<?php

declare(strict_types=1);

namespace App\Controllers;

use Leaf\Config\LeafConfig;
use Leaf\Content\ContentLoader;
use Leaf\Content\SearchIndexBuilder;
use Zephyrus\Controller\Controller;
use Zephyrus\Http\Response;
use Zephyrus\Rendering\RenderResponses;
use Zephyrus\Routing\Attribute\Get;

final class DocsController extends Controller
{
    use RenderResponses;

    public function __construct(
        private readonly ContentLoader $contentLoader,
        private readonly SearchIndexBuilder $searchIndexBuilder,
        private readonly LeafConfig $leafConfig,
    ) {
    }

    #[Get('/')]
    public function index(): Response
    {
        // Render the landing template. Projects customize it via
        // templates/landing.latte (binary tier) or app/Views/landing.latte
        // (Composer tier). The template ships a generic welcome landing
        // as a fallback.
        return $this->render('landing', [
            'title' => $this->leafConfig->name,
        ]);
    }

    #[Get('/{section}/{slug}')]
    public function show(string $section, string $slug): Response
    {
        $page = $this->contentLoader->getPage($section, $slug);

        if ($page === null) {
            return $this->render('404', [
                'title' => 'Page Not Found',
            ], 404);
        }

        $description = $page->meta('description', '');
        if ($description === '') {
            $plaintext = strip_tags($page->html);
            $plaintext = preg_replace('/\s+/', ' ', trim($plaintext));
            $description = mb_strlen($plaintext) > 160
                ? mb_substr($plaintext, 0, 157) . '...'
                : $plaintext;
        }

        return $this->render('docs/page', [
            'title' => $page->meta('title', ucfirst(str_replace('-', ' ', $slug))),
            'pageDescription' => $description,
            'section' => $section,
            'slug' => $slug,
            'content' => $this->prefixInternalLinks($page->html),
            'sidebar' => $this->contentLoader->getSidebar(),
            'toc' => $page->toc,
            'prevPage' => $this->contentLoader->getPreviousPage($section, $slug),
            'nextPage' => $this->contentLoader->getNextPage($section, $slug),
        ]);
    }

    #[Get('/search.json')]
    public function searchIndex(): Response
    {
        $index = $this->searchIndexBuilder->build();
        return Response::json($index);
    }

    /**
     * Prefix internal links and asset sources in rendered HTML with the configured base URL.
     */
    private function prefixInternalLinks(string $html): string
    {
        $baseUrl = rtrim($this->leafConfig->baseUrl, '/');
        if ($baseUrl === '') {
            return $html;
        }
        return preg_replace(
            '#(href|src)="(/(?!/)[^"]*)"#',
            '$1="' . $baseUrl . '$2"',
            $html,
        );
    }

    #[Get('/404')]
    public function notFound(): Response
    {
        return $this->render('404', [
            'title' => 'Page Not Found',
        ]);
    }
}
