<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified News Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use InspiredMinds\ContaoUnifiedNewsAliases\Controller\FrontendModule\NewsReaderModuleController;

$GLOBALS['TL_LANG']['FMD'][NewsReaderModuleController::TYPE] = [
    $GLOBALS['TL_LANG']['FMD']['newsreader'][0].' für vereinheitlichte Aliase',
    'Dieser Nachrichtenleser erlaubt die Darstellung von Nachrichten über den vereinheitlichten Nachrichtenalias (also den Nachrichten-Alias aus dem Hauptarchiv).',
];
