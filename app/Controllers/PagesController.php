<?php

declare(strict_types=1);

namespace App\Controllers;

use Leaf\Config\LeafConfig;
use Zephyrus\Controller\Controller;
use Zephyrus\Http\Response;
use Zephyrus\Rendering\RenderResponses;
use Zephyrus\Routing\Attribute\Get;

/**
 * Handles standalone Latte pages: templates dropped into
 * `app/Views/pages/{name}.latte` become `/{name}` at build time.
 *
 * Rendering resolves the page name back to its Latte file, using the shared
 * template context (layouts, partials, `$leafName` and friends).
 */
final class PagesController extends Controller
{
    use RenderResponses;

    public function __construct(
        private readonly LeafConfig $leafConfig,
    ) {
    }

    #[Get('/{page}', constraints: ['page' => '[a-z0-9][a-z0-9-]*'])]
    public function show(string $page): Response
    {
        $viewPath = ROOT_DIR . '/app/Views/pages/' . $page . '.latte';
        if (!is_file($viewPath)) {
            return $this->render('404', ['title' => 'Page Not Found'], 404);
        }

        $title = ucwords(str_replace('-', ' ', $page));
        return $this->render('pages/' . $page, [
            'title' => $title . ' · ' . $this->leafConfig->name,
            'pageTitle' => $title,
            'pagePath' => '/' . $page,
        ]);
    }
}
