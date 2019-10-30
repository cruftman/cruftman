<?php
/**
 * @file src/Cruftman/Config/TemplateArray.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support;

use Illuminate\Support\Arr;
use Cruftman\Support\Exceptions\TemplateArrayException;

/**
 * Template array.
 *
 * An array with string values containing placeholders such as ``${foo}``.
 * The TemplateArray object provides a way to substitute all placeholders with
 * user-defined values.
 */
class TemplateArray extends \ArrayObject
{
    const UNDEF_NONE = 'none';
    const UNDEF_SKIP = 'skip';
    const UNDEF_FAIL = 'fail';

    /**
     * @var array
     */
    protected $defaults = [];

    /**
     * @var bool
     */
    protected $undefAction = self::UNDEF_FAIL;

    /**
     * @var bool
     */
    protected $recursive = true;

    /**
     * Regular expression used to recognize placeholders in a template strings.
     *
     * @var string
     */
    protected $pattern = '/\${([[:alpha:]_][[:alnum:]_]*)}/u';

    /**
     * Formatting string used to form placeholders using ``sprintf($format, $key)``.
     *
     * @var string
     */
    protected $format = '${%s}';

    /**
     * Initializes the object.
     *
     * @param  array $template
     * @param  array $defaults
     */
    public function __construct(array $template, array $defaults = [])
    {
        parent::__construct($template);
        $this->setDefaults($defaults);
    }

    /**
     * Returns the default values for placeholders.
     *
     * @return array
     */
    public function getDefaults() : array
    {
        return $this->defaults;
    }

    /**
     * Returns a string determining how undefined placeholders are handled.
     *
     * @return string
     */
    public function getUndefAction() : string
    {
        return $this->undefAction;
    }

    /**
     * Returns the pattern used to match placeholders in configuration values.
     *
     * @return string
     */
    public function getPattern() : string
    {
        return $this->pattern;
    }

    /**
     * Returns formatting string that forms placeholder.
     *
     * @return string
     */
    public function getFormat() : string
    {
        return $this->format;
    }

    /**
     * Returns the "recursive" flag.
     *
     * If the recursive flag is set, the ``substitute()`` walks recursively
     * through the array template. Otherwise, only first-level array values are
     * visited.
     *
     * @return bool
     */
    public function isRecursive() : bool
    {
        return $this->recursive;
    }

    /**
     * Sets the default values for placeholders.
     *
     * @return $this
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     * Sets the undefAction.
     *
     * @return $this
     */
    public function setUndefAction(string $action)
    {
        $this->undefAction= $action;
        return $this;
    }

    /**
     * Sets the recursive flag.
     *
     * @return $this
     */
    public function setRecursive(bool $recursive = true)
    {
        $this->recursive = $recursive;
        return $this;
    }

    /**
     * Set the placeholder pattern.
     *
     * @param  string $pattern
     * @return $this
     */
    public function setPattern(string $pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

    /**
     * Set the placeholder formatting string.
     *
     * @param  string $format
     * @return $this
     */
    public function setFormat(string $format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * Returns a placeholder for given $key.
     *
     * @param string $key
     */
    public function placeholder(string $key) : string
    {
        return sprintf($this->getFormat(), $key);
    }

    /**
     * Get array item using "dot" notation.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Arr::get($this, $key, $default);
    }

    /**
     * Get the configuration array or its item (using "dot" notation) with all placeholders substituted.
     *
     * @param  array $dict
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     * @throws TemplateArrayException
     */
    public function substitute(array $dict = [], string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->substInArray($this->getArrayCopy(), $dict);
        } else {
            return $this->substItem($key, $dict, $default);
        }
    }

    /**
     * Get substituted array item using "dot" notation.
     *
     * @param  string $key
     * @param  array $dict
     * @param  mixed $default
     * @return mixed
     * @throws TemplateArrayException
     */
    public function substItem(string $key, array $dict = [], $default = null)
    {
        $item = $this->get($key, $default);
        return $this->substValue($item, $dict);
    }

    /**
     * Substitute placeholders in a value (string, array).
     *
     * @param  mixed $value
     * @param  array $dict
     * @return mixed
     * @throws TemplateArrayException
     */
    public function substValue($value, array $dict = [])
    {
        if (is_array($value)) {
            $value = $this->substInArray($value, $dict);
        } elseif (is_string($value)) {
            $value = $this->substInString($value, $dict);
        }
        return $value;
    }

    protected function substInString(string $value, array $dict) : string
    {
        $callback = $this->getPregReplaceCallback($dict);
        return preg_replace_callback($this->getPattern(), $callback, $value);
    }

    protected function substInArray(array $array, array $dict) : array
    {
        if ($this->isRecursive()) {
            array_walk_recursive($array, $this->getArrayWalkCallback($dict));
        } else {
            array_walk($array, $this->getArrayWalkCallback($dict));
        }
        return $array;
    }

    protected function getArrayWalkCallback(array $dict)
    {
        $callback = $this->getPregReplaceCallback($dict);
        return function (&$value) use ($callback) {
            if (is_string($value)) {
                $value = preg_replace_callback($this->getPattern(), $callback, $value);
            }
        };
    }

    protected function getPregReplaceCallback(array $dict)
    {
        switch ($this->getUndefAction()) {
            case self::UNDEF_FAIL:
                return function ($matches) use ($dict) {
                    return $this->getReplacementOrFail($dict, $matches[1]);
                };
                break;
            case self::UNDEF_SKIP:
                return function ($matches) use ($dict) {
                    return $this->getReplacement($dict, $matches[1], $matches[0]);
                };
                break;
            default:
                return function ($matches) use ($dict) {
                    return $this->getReplacement($dict, $matches[1]);
                };
                break;
        }
    }

    /**
     * Returns a replacement string for given placeholder identified by $key.
     *
     * If there is no replacement value for given $key, then an exception
     * is thrown.
     *
     * @param  array $dict
     * @param  array $key
     * @return string
     * @throws TemplateArrayException
     */
    protected function getReplacementOrFail(array $dict, string $key) : string
    {
        $defaults = $this->getDefaults();
        if (($repl = $dict[$key] ?? ($defaults[$key] ?? null)) === null) {
            throw new TemplateArrayException('undefined value for '.$this->placeholder($key).' placeholder');
        }
        return $repl;
    }

    /**
     * Returns a replacement string for given placeholder identified by $key.
     *
     * If there is no replacement value for given $key, then empty string is
     * returned.
     *
     * @param  array $dict
     * @param  string $key
     * @param  string $default
     * @return string
     */
    protected function getReplacement(array $dict, string $key, string $default = '') : string
    {
        $defaults = $this->getDefaults();
        return $dict[$key] ?? ($defaults[$key] ?? $default);
    }
}

// vim: syntax=php sw=4 ts=4 et:
