<?php
namespace {$namespace};

use Program\Table\{$upper}Table;
use icePHP\Frame\TableLog\TableLog;

use function icePHP\Frame\MVC\display;
use function icePHP\Frame\Functions\csvHeader;
use function icePHP\Frame\Functions\iconvRecursion;

/**
 * {$name} 相关控制逻辑
 * Crud自动生成
 */
class {$module}{$upper}Controller extends {$module}BaseController
{
{$index}
{$input}
{$add}
{$edit}
{$delete}

{$multiDelete}
{$tableOperations}
{$rowOperations}
{$multiOperations}

{$detail}
{$export}

{$print}
}