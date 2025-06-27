<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoUnifiedNewsAliasesBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
