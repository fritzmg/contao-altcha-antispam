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

namespace Markocupic\ContaoAltchaAntispam\DependencyInjection;

use Markocupic\ContaoAltchaAntispam\Config\AltchaAlgorithmConfig;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public const ROOT_KEY = 'markocupic_contao_altcha_antispam';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ROOT_KEY);

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('hmac_key')
                    ->cannotBeEmpty()
                    ->defaultValue('')
                ->end()
                ->enumNode('algorithm')
                    ->values([...AltchaAlgorithmConfig::ALGORITHM_ALL])
                    ->defaultValue(AltchaAlgorithmConfig::ALGORITHM_SHA_256)
                ->end()
                ->integerNode('range_min')
                    ->defaultValue(10000)
                ->end()
                ->integerNode('range_max')
                    ->defaultValue(100000)
                ->end()
                ->integerNode('challenge_expiry')
                    ->defaultValue(3600)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
