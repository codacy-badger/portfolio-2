<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please View the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class SubBinary extends AbstractBinary
{
    public function operator(Compiler $compiler)
    {
        return $compiler->raw('-');
    }
}

class_alias('Twig\Node\Expression\Binary\SubBinary', 'Twig_Node_Expression_Binary_Sub');
