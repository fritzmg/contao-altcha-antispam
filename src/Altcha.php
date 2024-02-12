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

namespace Markocupic\ContaoAltchaAntispam;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Markocupic\ContaoAltchaAntispam\Config\AltchaConfiguration;
use Markocupic\ContaoAltchaAntispam\Exception\HmacKeyNotSetException;
use Markocupic\ContaoAltchaAntispam\Exception\InvalidAlgorithmException;

class Altcha
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $altchaHmacKey,
        private readonly string $altchaAlgorithm,
        private readonly int $altchaRangeMin,
        private readonly int $altchaRangeMax,
    ) {
    }

    /**
     * @throws InvalidAlgorithmException
     */
    public function createChallenge(string $salt = null, int $number = null): array
    {
        if ('' === $this->altchaHmacKey) {
            throw new HmacKeyNotSetException('ALTCHA hmac key ist empty and should be set in config/config.yaml. Please visit https://github.com/markocupic/contao-altcha-antispam?tab=readme-ov-file#configuration-and-usage to learn more.');
        }

        $salt = $salt ?? bin2hex(random_bytes(12));
        $number = $number ?? random_int($this->altchaRangeMin, $this->altchaRangeMax);

        if (!\in_array($this->altchaAlgorithm, AltchaConfiguration::ALGORITHM_ALL, true)) {
            throw new InvalidAlgorithmException(sprintf('Algorithm must be set to %s.', implode(', ', AltchaConfiguration::ALGORITHM_ALL)));
        }

        $algorithm = str_replace('-', '', strtolower($this->altchaAlgorithm));

        $challenge = hash($algorithm, $salt.$number);
        $signature = hash_hmac($algorithm, $challenge, $this->altchaHmacKey);

        return [
            'algorithm' => $this->altchaAlgorithm,
            'challenge' => $challenge,
            'salt' => $salt,
            'signature' => $signature,
        ];
    }

    public function isValidPayload(string $payload): bool
    {
        $json = json_decode(base64_decode($payload, true), true);

        if (null === $json) {
            return false;
        }

        if ($this->isReplay($json)) {
            return false;
        }

        $set = [
            'tstamp' => time(),
            'challenge' => $json['challenge'],
        ];

        $this->connection->insert('tl_altcha_challenge', $set);

        $check = $this->createChallenge($json['salt'], $json['number']);

        return $json['algorithm'] === $check['algorithm']
                && $json['challenge'] === $check['challenge']
                && $json['signature'] === $check['signature'];
    }

    private function isReplay(array $json): bool
    {
        $challenge = $json['challenge'] ?? '';

        return false !== $this->connection->fetchOne(
            'SELECT id FROM tl_altcha_challenge WHERE challenge = :challenge',
            [
                'challenge' => $challenge,
            ],
            [
                'challenge' => Types::STRING,
            ],
        );
    }
}
