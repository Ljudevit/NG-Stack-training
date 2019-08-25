<?php

declare(strict_types=1);

namespace AppBundle\Block\BlockDefinition\Handler;

use Exception;
use Netgen\Layouts\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface;
use Netgen\Layouts\Block\BlockDefinition\Handler\Plugin;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use Netgen\Layouts\Ez\Parameters\ParameterType as EzParameterType;
use Netgen\Layouts\Parameters\ParameterType;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\DynamicParameters;

class OffsetPlugin extends Plugin
{

    /**
     * Returns the fully qualified class name of the handler which this
     * plugin extends.
     *
     * @return string|string[]
     */
    public static function getExtendedHandlers(): array
    {
        return [ContainerDefinitionHandlerInterface::class];
    }

    /**
     * Builds the parameters by using provided parameter builder.
     *
     * @param \Netgen\Layouts\Parameters\ParameterBuilderInterface $builder
     */
    public function buildParameters(ParameterBuilderInterface $builder): void
    {
        $designGroup = array(self::GROUP_DESIGN);

        $builder->add('container_offset:enabled', ParameterType\Compound\BooleanType::class,
                array('default_value' => false,
                'label' => 'block.plugin.container_offset.enabled',
                'groups' => $designGroup,
            )
        );

        $builder->get('container_offset:enabled')->add('container_offset:position', ParameterType\ChoiceType::class,
            array('default_value' => 'left',
                'label' => 'block.plugin.container_offset.position',
                'options' =>
                    ['block.plugin.container_offset.left' => 'left', 'block.plugin.container_offset.right' => 'right', 'block.plugin.container_offset.both' => 'both'],
                'groups' => $designGroup,
            )
        );

    }

    

}

