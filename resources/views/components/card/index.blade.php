@props([
    'header',
    'content',
    'footer',
    'title',
    'actions'
])

<div {{ $attributes->class(['card']) }}>

    @isset($header)
        <div {{ $header->attributes->class(['card-header']) }}>
            @isset($title)
                <div>
                    <h3 class="card-title">
                        {{ $title }}
                    </h3>
                </div>
            @endisset

            {{ $header }}

            @isset($actions)
                <div class="card-actions">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endisset

    @isset($content)
        <div {{ $content->attributes->class(['card-body']) }}>
            {{ $content }}
        </div>
    @endisset

    @isset($slot)
        {{ $slot }}
    @endisset

    @isset($footer)
        <div {{ $footer->attributes->class(['card-footer']) }}>
            {{ $footer }}
        </div>
   @endisset
</div>
