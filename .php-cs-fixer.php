<?php declare(strict_types=1);

require_once __DIR__.'/vendor/autoload.php';

use Zvive\Fixer\SharedConfig;
use Zvive\Fixer\Rulesets\ZviveRuleset;

$finder = LaravelPackageFinder::create(__DIR__)->notName('*.stub');
$rules  = [
    // '@PSR12'                 => true,
];

return SharedConfig::create($finder, new ZviveRuleset($rules));
