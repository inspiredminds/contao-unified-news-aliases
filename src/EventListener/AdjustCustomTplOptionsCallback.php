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

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\ModuleModel;
use InspiredMinds\ContaoUnifiedNewsAliases\Controller\FrontendModule\NewsReaderModuleController;

/**
 * @Callback(table="tl_module", target="config.onload")
 */
class AdjustCustomTplOptionsCallback
{
    public function __invoke(DataContainer $dc): void
    {
        $module = ModuleModel::findById($dc->id);

        if (null === $module || NewsReaderModuleController::TYPE !== $module->type) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_module']['fields']['customTpl']['options_callback'] = function () {
            return Controller::getTemplateGroup('mod_newsreader_', [], 'mod_newsreader');
        };
    }
}
