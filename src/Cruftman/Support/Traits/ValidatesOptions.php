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
 * Adds options validation functionality to an object.
 *
 * Works with <a href="HasOptions.html">HasOptions</a> trait. Once added to
 * a class, the validation is triggered automatically from ``HasOptions`` each
 * time the <a href="HasOptions.html#method_setOptions">HasOptions::setOptions()</a>
 * is called. The receiving class must implement
 * <a href="#method_configureOptionsResolver">configureOptionsResolver()</a> method.
 */
trait ValidatesOptions
{
    /**
     * An array of options resolvers, one item per class in a hierarchy.
     *
     * @var array
     */
    protected static $optionsResolversByClass = [];

    /**
     * Returns options resolver for the current class.
     *
     * @return OptionsResolver
     */
    protected function getOptionsResolver() : OptionsResolver
    {
        if (!isset(self::$optionsResolversByClass[static::class])) {
            self::$optionsResolversByClass[static::class] = $this->createOptionsResolver();
        }
        return self::$optionsResolversByClass[static::class];
    }

    /**
     * Creates new options resolver.
     *
     * @return OptionsResolver
     */
    protected function createOptionsResolver() : OptionsResolver
    {
        $resolver = new OptionsResolver();
        $this->configureOptionsResolver($resolver);
        return $resolver;
    }

    /**
     * Configure options resolver to validate and resolve options (the
     * receiving class must implement this method).
     *
     * @param  OptionsResolver $resolver
     */
    abstract protected function configureOptionsResolver(OptionsResolver $resolver);

    /**
     * Validates and resolves options using the options resolver
     * (see <a href="#method_getOptionsResolver">getOptionsResolver()</a>).
     *
     * @param  \Cruftman\Support\TemplateArray $options
     * @return array the resolved options
     */
    protected function validateOptions(array $options)
    {
        return $this->getOptionsResolver()->resolve($options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
