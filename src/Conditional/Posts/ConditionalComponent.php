<?php

declare(strict_types=1);

namespace PoP\Comments\Conditional\Posts;

use PoP\Comments\Component;
use PoP\Root\Component\YAMLServicesTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;

/**
 * Initialize component
 */
class ConditionalComponent
{
    use YAMLServicesTrait;

    public static function initialize(
        array $configuration = [],
        bool $skipSchema = false,
        array $skipSchemaComponentClasses = []
    ): void {
        if (class_exists('\PoP\RESTAPI\Component')
            && !in_array(\PoP\RESTAPI\Component::class, $skipSchemaComponentClasses)
        ) {
            \PoP\Comments\Conditional\Posts\Conditional\RESTAPI\ConditionalComponent::initialize(
                $configuration,
                $skipSchema
            );
        }
    }

    /**
     * Boot component
     *
     * @return void
     */
    public static function beforeBoot(): void
    {
        if (class_exists('\PoP\RESTAPI\Component')) {
            \PoP\Comments\Conditional\Posts\Conditional\RESTAPI\ConditionalComponent::beforeBoot();
        }
    }
}
