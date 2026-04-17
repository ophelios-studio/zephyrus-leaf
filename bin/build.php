<?php

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR . '/vendor/autoload.php';

use App\Models\Core\Application;
use Leaf\BuildCommand;

$app = new Application();
$command = new BuildCommand($app);
exit($command->run());
