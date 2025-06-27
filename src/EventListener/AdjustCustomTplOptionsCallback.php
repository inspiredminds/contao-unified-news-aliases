<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedNewsAliases\EventListener;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\ModuleModel;
use InspiredMinds\ContaoUnifiedNewsAliases\Controller\FrontendModule\NewsReaderModuleController;

#[AsCallback('tl_module', 'config.onload')]
class AdjustCustomTplOptionsCallback
{
    public function __invoke(DataContainer $dc): void
    {
        $module = ModuleModel::findById($dc->id);

        if (null === $module || NewsReaderModuleController::TYPE !== $module->type) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_module']['fields']['customTpl']['options_callback'] = (static fn () => Controller::getTemplateGroup('mod_newsreader_', [], 'mod_newsreader'));
    }
}
