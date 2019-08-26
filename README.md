# Netgen stack training

## Part 1: homepage

### 1. Define layout, blocks and block item view types based on wireframe

### 2. Creating a new layout

- add `resources/config/layout_types.yml`

```
layout_types:
        layout_nt:
            name: 'Layout Netgen training'
            icon: '/bundles/netgenlayoutsstandard/images/layout_types/layout_2.svg'
            zones:
                main_menu:
                    name: 'Main Menu'
                header:
                    name: 'Header'
                post_header:
                    name: 'Post header'
                main:
                    name: 'Main'
                pre_footer:
                    name: 'Pre footer'
                footer:
                    name: 'Footer'

```
- add `resources/config/layout_view.yml`

```
view:
	layout_view:
        default:
            layout_nt:
                template: "@App/nglayouts/themes/app/layout/layout_nt.html.twig"
                match:
                    layout\type: layout_nt
            app:
                layout_nt:
                    template: "@App/nglayouts/app/layout/layout_nt.html.twig"
                    match:
                        layout\type: layout_nt

```

- in `DependencyInjection/AppExtension.php` add two new yml files:

```
$prependConfigs = [
            'layouts/blocks.yml' => 'netgen_layouts',
            'layouts/block_view.yml' => 'netgen_layouts',
            'layouts/item_view.yml' => 'netgen_layouts',
            'layouts/layout_types.yml' => 'netgen_layouts',
            'layouts/layout_view.yml' => 'netgen_layouts',
        ];

```

- create new layout templates for frontend and backend, paths:

`/Resources/views/nglayouts/themes/app/layout/layout_nt.html.twig`

`/Resources/views/nglayouts/app/layout/layout_nt.html.twig`

### 3. Creating new block item view type

- create folder `hero` in `/Resources/views/themes/app/content/` and inside create file `ng_banner.html.twig`

- create file `hero.html.twig` in `/Resources/views/themes/app/content` as fallback template

- in `resources/config/content_view.yml` add: 

```
hero:
    ng_banner:
        template: "@ezdesign/content/hero/ng_banner.html.twig"
        match:
            Identifier\ContentType: ng_banner
    hero:
        template: "@ezdesign/content/hero.html.twig"
        match: ~

```

- create `_hero.scss` in `Resources/sass/content/item_view_types`, include it in `_view_types.scss`

- enable choice of selecting hero block item view type in list block; in `resources/config/layouts/blocks.yml`

```
Block_definitions:
	List:
		item_view_types: &list_item_view_types
			hero:
          		name: 'Hero (banner)'

```

### 4. Add background color block plugin value

- in `Resources/config/layouts/services.yml` add:

```
parameters:
    ngsite.layouts.block.plugin.background_color.colors:
        nt_blue: 'block.plugin.background_color.nt_blue’

```
- add translation for label in `Resources/translations/nglayouts.en.yml`

```
block.plugin.background_color.nt_blue: 'Netgen training blue'
```

- add CSS style in `Resources/sass/blocks/_block.scss`

```
&.bg-color-nt_blue {
    background-color: #3895D3;
}
```

### 5. Create block plugin for Container offseting

- create a file `Block/BlockDefinition/Handler/Plugin/OffsetPlugin.php`

```
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

```

- Add plugin to `Resources/config/layouts/services.yml`

```
services:
    app.block.block_definition.handler.plugin.offset_plugin:
        class: AppBundle\Block\BlockDefinition\Handler\Plugin\OffsetPlugin
        tags:
            - { name: netgen_layouts.block_definition_handler.plugin }

```

- Add translations labels in `Resources/translations/nglayouts.en.yml`

```
block.plugin.container_offset.enabled: 'Container Offset'
block.plugin.container_offset.position: 'Offset position'
block.plugin.container_offset.left: 'Left'
block.plugin.container_offset.right: 'Right'
block.plugin.container_offset.both: 'Both'
```

- Add block parameter in `Resources/views/nglayouts/themes/app/block/block.html.twig`

```
{% set use_offset = block.hasParameter('container_offset:enabled') and block.parameter('container_offset:enabled').value is same as(true) %}
{% set offset_position = block.hasParameter('container_offset:enabled') and block.parameter('container_offset:enabled').value and block.hasParameter('container_offset:position') ? block.parameter('container_offset:position').value : null %}
```

- expand template logic for 2_columns 50:50 container (`Resources/views/nglayouts/themes/app/block/two_columns/two_columns_50_50.html.twig`):

```
{% extends '@nglayouts/block/block.html.twig' %}

{% block content %}
    <div class="row">
        {% if offset_position is not null %}
            {% if offset_position == 'left' or offset_position == 'both' %}
                <div class="col-md-6 col-lg-5 offset-lg-1">
            {% else %}
                <div class="col-md-6">
            {% endif %}
        {% else %}
        <div class="col-md-6">
        {% endif %}
            {{ nglayouts_render_placeholder(block, 'left') }}
        </div>
        {% if offset_position is not null %}
            {% if offset_position == 'right' or offset_position == 'both' %}
                <div class="col-md-6 col-lg-5">
            {% else %}
                <div class="col-md-6">
            {% endif %}
        {% else %}
        <div class="col-md-6">
        {% endif %}
            {{ nglayouts_render_placeholder(block, 'right') }}
        </div>
    </div>
{% endblock %}
```

## Part 2: Full views

### 1. import/create content type and content for Event and Speakers

### 2. create full view templates for Event and Speaker

create files:

`Resources/views/themes/app/content/full/nt_event.html.twig`

`Resources/views/themes/app/content/full/nt_speaker.html.twig`

registrate them in `Resources/config/content_view.yml`:

```
nt_event:
    template: '@ezdesign/content/full/nt_event.html.twig'
        match:
            Identifier\ContentType: nt_event
                       
nt_speaker: 
    template: '@ezdesign/content/full/nt_speaker.html.twig'
        match:
            Identifier\ContentType: nt_speaker

```

### 3. create circled and schedule block item view types:

`Resources/config/content_view.yml`:

```
schedule:
    nt_event:
        template: "@ezdesign/content/schedule/nt_event.html.twig"
        match:
            Identifier\ContentType: nt_event
    schedule:
        template: "@ezdesign/content/schedule.html.twig"
        match: ~
circled:
    nt_speaker:
        template: "@ezdesign/content/circled/nt_speaker.html.twig"
        match:
            Identifier\ContentType: nt_speaker
    circled:
        template: "@ezdesign/content/circled.html.twig"
        match: ~
```

- create templates:

### 4. add items to homepage layout

### 5. create display of relations between events and speakers

`Resources/views/themes/app/content/schedule/nt_event.html.twig`

```
{% set related_speakers = content.fieldRelations('related_speakers', 10) %}
<ul>
{% for speaker in related_speakers %}
    <li><a href="{{ path(speaker.mainLocation) }}" >{{ speaker.name }}</a></li>
{% endfor %}
</ul>
```
- add queries:




in `Resources/config/content_view.yml`

for nt_event:

```
queries:
    speakers:
        query_type: SiteAPI:Content/Relations/ForwardFields
        max_per_page: 10
        page: 1
        parameters:
            relation_field: related_speakers
            content_type: nt_speaker
            sort: name
```

for nt_speaker:

```
queries:
    events:
        query_type: SiteAPI:Content/Relations/ReverseFields
        max_per_page: 10
        page: 1
        parameters:
            relation_field: related_speakers
            content_type: nt_event
            sort: field/nt_event/start_date asc
```

in `Resources/views/themes/app/content/full/nt_speaker.html.twig`

```
{% block related_events %}

{% set events = ng_query('events') %}

{% if events|length > 0 %}
    <div class="row">
        {% for event in events %}
        <div class="col-md-4">
            {{ render(
                controller(
                    'ng_content:viewAction', {
                        'content': event,
                        'location': event.mainLocation,
                        'viewType': 'schedule',
                        'layout': false
                    }
                )
            ) }}
        </div>
        {% endfor %}
    </div>
{% endif %}

{% endblock %}
```

in `Resources/views/themes/app/content/full/nt_event.html.twig`

```
{% set speakers = ng_query('speakers') %}

{% if speakers|length > 0 %}
    <div class="row">
        {% for speaker in speakers %}
        <div class="col-md-4">
            {{ render(
                controller(
                    'ng_content:viewAction', {
                        'content': speaker,
                        'location': speaker.mainLocation,
                        'viewType': 'circled',
                        'layout': false
                    }
                )
            ) }}
        </div>
        {% endfor %}
    </div>
{% endif %}
```