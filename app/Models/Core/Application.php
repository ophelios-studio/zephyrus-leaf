<?php

declare(strict_types=1);

namespace App\Models\Core;

use App\Controllers\DocsController;
use Leaf\Kernel;

final class Application extends Kernel
{
    protected function createController(string $class): object
    {
        if ($class === DocsController::class) {
            return new DocsController(
                $this->contentLoader,
                $this->searchIndexBuilder,
                $this->leafConfig,
            );
        }
        return new $class();
    }
}
