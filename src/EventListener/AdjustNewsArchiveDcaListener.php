<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified News Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\EventListener;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\NewsArchiveModel;

/**
 * @Callback(table="tl_news_archive", target="config.onload", priority=-32)
 */
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
