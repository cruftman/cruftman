<?php
/**
 * @file src/Cruftman/Models/ModelUserHelpers.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * The receiver is supposed to set ``$modelName`` property, and then all the
 * methods provided by the trait should work.
 */
trait ModelUserHelpers
{
    use NsUserHelpers;

    /**
     * Model name, must be defined in a subclass.
     *
     * @var string
     */
    protected $modelName = null;

    /**
     * Returns the name of the model supported by this controller.
     *
     * @return string
     * @throws UnexpectedValueException
     */
    public function getModelName() : string
    {
        if (!is_string($this->modelName)) {
            throw new \UnexpectedValueException(static::class . '::$modelName is not a string');
        }
        return $this->modelName;
    }

    /**
     * Returns the model class supported by this controller.
     *
     * @return string
     */
    public function getModelClass() : string
    {
        if (isset($this->modelClass)) {
            if (!is_string($this->modelClass)) {
                throw new \UnexpectedValueException(static::class . '::$modelClass is not a string');
            }
            return $this->modelClass;
        } else {
            return $this->getModelNamespace() . '\\' . $this->getModelName();
        }
    }

    /**
     * Returns an empty instance of the model supported by this controller.
     *
     * @return Model
     */
    public function getModel(...$args) : Model
    {
        $class= $this->getModelClass();
        return new $class(...$args);
    }

    /**
     * Returns the table name of the model.
     *
     * @return string
     */
    public function getModelTable() : string
    {
        return $this->getModel()->getTable();
    }

    /**
     * Returns resource key for model's API resource.
     *
     * By default, it's just a singular form of model's table name.
     *
     * @return string
     */
    public function getModelResourceKey() : string
    {
        return $this->getModelTable();
        //return Str::singular($this->getModelTable());
    }
}

// vim: syntax=php sw=4 ts=4 et:
