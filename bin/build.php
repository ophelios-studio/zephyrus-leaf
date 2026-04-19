<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR . '/vendor/autoload.php';

use App\Models\Core\Application;
use Leaf\BuildCommand;
use Leaf\PageDiscovery;

$app = new Application();
$command = new BuildCommand($app);

// Register every `app/Views/pages/{name}.latte` as a build path so PagesController
// can render it. Projects add standalone pages by dropping files here (or via
// `templates/pages/` in Binary CLI tier, which the binary merges at build time).
$command->addPaths(PageDiscovery::discover(ROOT_DIR . '/app/Views'));

exit($command->run());
