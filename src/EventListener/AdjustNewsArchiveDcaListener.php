<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\EventListener;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\NewsArchiveModel;

#[AsCallback('tl_news_archive', 'config.onload', priority: -32)]
class AdjustNewsArchiveDcaListener
{
    public function __invoke(DataContainer $dc): void
    {
        $GLOBALS['TL_DCA']['tl_news_archive']['fields']['master']['eval']['submitOnChange'] = true;

        if (!$dc->id) {
            return;
        }

        $archive = NewsArchiveModel::findById($dc->id);

        if (null === $archive || (bool) $archive->master) {
            return;
        }

        PaletteManipulator::create()
            ->addField('use_unified_aliases', 'language_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_news_archive')
        ;
    }
}
