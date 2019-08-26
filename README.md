# Netgen stack training

## Part 1: homepage

### 1. define layout, blocks and block item view types based on wireframe

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

