<?php
/**
 * @file src/Cruftman/Support/Traits/ValidatesOptions.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support\Traits;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation.
 */
trait ValidatesOptions
{
    protected static $optionsResolversByClass = [];

    protected function getOptionsResolver() : OptionsResolver
    {
        if (!isset(self::$optionsResolversByClass[static::class])) {
            self::$optionsResolversByClass[static::class] = $this->createOptionsResolver();
        }
        return self::$optionsResolversByClass[static::class];
    }

    protected function createOptionsResolver() : OptionsResolver
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        return $resolver;
    }

    abstract protected function configureOptionsResolver(OptionsResolver $resolver);

    /**
     * Validates config template.
     *
     * @param  \Cruftman\Support\TemplateArray $options
     * @throws \Exception
     */
    protected function validateOptions(array $options)
    {
        return $this->getOptionsResolver()->resolve($options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
