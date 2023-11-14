@props([
    'header',
    'content',
    'footer'
])

<div {{ $attributes->class(['card']) }}>

    @isset($header)
        <div {{ $header->attributes->class(['card-header']) }}>
            <h3 class="card-title">
                {{ $header }}
            </h3>
        </div>
    @endisset

    @isset($content)
        <div {{ $content->attributes->class(['card-body']) }}>
            {{ $content }}
        </div>
    @endisset

    @isset($footer)
        <div {{ $footer->attributes->class(['card-footer']) }}>
            {{ $footer }}
        </div>
   @endisset

</div>
