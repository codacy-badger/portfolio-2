<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please Views the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Grammar_BodyTest extends \PHPUnit\Framework\TestCase
{
    public function testMagicToString()
    {
        $grammar = new Twig_Extensions_Grammar_Body('foo');
        $this->assertEquals('<foo:body>', (string) $grammar);
    }
}
