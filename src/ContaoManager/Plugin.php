<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\ContaoManager;

use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\NewsBundle\ContaoNewsBundle;
use InspiredMinds\ContaoUnifiedNewsAliases\ContaoUnifiedNewsAliasesBundle;
use Terminal42\ChangeLanguage\Terminal42ChangeLanguageBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoUnifiedNewsAliasesBundle::class)
                ->setLoadAfter([
                    ContaoNewsBundle::class,
                    Terminal42ChangeLanguageBundle::class,
                ]),
        ];
    }
}
