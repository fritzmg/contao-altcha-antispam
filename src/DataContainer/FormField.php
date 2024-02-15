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

namespace Markocupic\ContaoAltchaAntispam\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\Message;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormField
{
    public function __construct(
        private readonly Connection $connection,
        private readonly ContaoFramework $framework,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
        private readonly string $altchaHmacKey,
    ) {
    }

    #[AsCallback(table: 'tl_form_field', target: 'config.onload', priority: 100)]
    public function displayMessage(DataContainer $dc): void
    {
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $qb = $this->connection->createQueryBuilder();

        $id = $qb->select('t.id')
            ->from('tl_form_field', 't')
            ->where('id = :id AND type = :type')
            ->setParameters(
                [
                    'id' => $dc->id,
                    'type' => 'altcha_hidden',
                ],
                [
                    'id' => Types::INTEGER,
                    'type' => Types::STRING,
                ],
            )
            ->fetchOne()
        ;

        if (false !== $id && empty($this->altchaHmacKey)) {
            $message = $this->framework->getAdapter(Message::class);
            $errMsg = $this->translator->trans('ERR.altcha_hmac_key_not_found', [], 'contao_default');
            $message->addError($errMsg);
        }
    }

    #[AsCallback(table: 'tl_form_field', target: 'fields.name.load', priority: 100)]
    public function setDefaultValue(mixed $varValue, DataContainer $dc): mixed
    {
        $name = $this->connection->fetchOne(
            'SELECT name FROM tl_form_field WHERE id = :id AND type = :type AND name = :name',
            [
                'id' => $dc->id,
                'type' => 'altcha_hidden',
                'name' => '',
            ],
            [
                'id' => Types::INTEGER,
                'id' => Types::STRING,
                'name' => Types::STRING,
            ],
        );

        if ('' === $name) {
            $varValue = 'altcha_'.$dc->id;
        }

        return $varValue;
    }

}
