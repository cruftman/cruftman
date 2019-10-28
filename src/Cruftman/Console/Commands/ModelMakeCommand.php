<?php
/**
 * @file src/Cruftman/Models/Person.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as IlluminateModelMakeCommand;

class ModelMakeCommand extends IlluminateModelMakeCommand
{
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Models';
    }
}

// vim: syntax=php sw=4 ts=4 et:
