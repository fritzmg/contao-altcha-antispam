<?php

declare(strict_types=1);

/*
 * This file is part of Contao Altcha Antispam.
 *
 * (c) Marko Cupic 2024 <m.cupic@gmx.ch>
 * @license GPL-3.0-or-later
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed with this source code.
 * @link https://github.com/markocupic/contao-altcha-antispam
 */

use Doctrine\DBAL\Types\Types;

$GLOBALS['TL_DCA']['tl_form_field']['palettes']['altcha_hidden'] = '
{type_legend},type,label;
{altcha_legend},altchaHideLogo,altchaHideFooter,altchaAuto,altchaMaxNumber,altchaSource;
{template_legend:hide},customTpl;
{invisible_legend:hide},invisible
';

// Fields
$GLOBALS['TL_DCA']['tl_form_field']['fields']['altchaHideLogo'] = [
    'exclude'   => true,
    'search'    => true,
    'sorting'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => [
        'type'    => Types::STRING,
        'length'  => '1',
        'notnull' => true,
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['altchaHideFooter'] = [
    'exclude'   => true,
    'search'    => true,
    'sorting'   => true,
    'inputType' => 'checkbox',
    'eval'      => ['tl_class' => 'w50'],
    'sql'       => [
        'type'    => Types::STRING,
        'length'  => '1',
        'notnull' => true,
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['altchaAuto'] = [
    'exclude'   => true,
    'search'    => true,
    'sorting'   => true,
    'inputType' => 'select',
    'options'   => ['onload', 'onsubmit'],
    'eval'      => ['mandatory' => false, 'includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql'       => [
        'type'    => Types::STRING,
        'length'  => '16',
        'notnull' => true,
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['altchaMaxNumber'] = [
    'exclude'   => true,
    'search'    => true,
    'sorting'   => true,
    'inputType' => 'text',
    'eval'      => ['mandatory' => true, 'rgxp' => 'integer', 'maxlength' => 10, 'tl_class' => 'w50'],
    'sql'       => [
        'type'     => Types::INTEGER,
        'length'   => '10',
        'unsigned' => true,
        'notnull'  => true,
        'default'  => 10000000,
    ],
];

$GLOBALS['TL_DCA']['tl_form_field']['fields']['altchaSource'] = [
    'exclude'   => true,
    'search'    => true,
    'sorting'   => true,
    'inputType' => 'select',
    'options'   => ['local', 'cdn'],
    'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql'       => [
        'type'    => Types::STRING,
        'length'  => '5',
        'notnull' => true,
        'default' => 'local',
    ],
];
