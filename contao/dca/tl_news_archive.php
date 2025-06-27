<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

$GLOBALS['TL_DCA']['tl_news_archive']['fields']['use_unified_aliases'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'w50 clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];
