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

namespace Markocupic\ContaoAltchaAntispam\Validator;

use Markocupic\ContaoAltchaAntispam\Altcha;

class AltchaValidator
{
    public function __construct(
        private readonly Altcha $altcha,
    ) {
    }

    public function validator(string $payload): bool
    {
        return $this->altcha->isValidPayload($payload);
    }
}
