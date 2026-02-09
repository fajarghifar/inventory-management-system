@props(['options' => [], 'placeholder' => 'Select option...', 'url' => null])

<div wire:ignore class="w-full">
    <select
        x-data="{
            tom: null,
            @if($attributes->has('wire:model'))
                value: @entangle($attributes->wire('model')),
            @elseif($attributes->has('x-model'))
                value: {{ $attributes->get('x-model') }},
            @else
                value: null,
            @endif

            init() {
                if (this.tom || this.$el.tomselect) return;

                this.$nextTick(() => {
                    if (this.tom || this.$el.tomselect) return;

                    // Handle Initial Search (Unresolved Items) - Prepare Data
                    const initialSearch = this.$el.getAttribute('data-initial-search');

                    let config = {
                        items: this.value ? [this.value] : [],
                        placeholder: (initialSearch && !this.value) ? initialSearch : '{{ $placeholder }}',
                        valueField: 'value',
                        labelField: 'text',
                        searchField: ['text'],
                        preload: 'focus',
                        plugins: ['clear_button'],
                        create: false,
                        sortField: {
                            field: 'text',
                            direction: 'asc'
                        },
                        onItemAdd: (value, item) => {
                            this.value = value;
                            if (this.tom.options[value]) {
                                this.$dispatch('option-selected', { name: '{{ $attributes->get("name") }}', value: value, item: this.tom.options[value] });
                            }
                        },
                        onItemRemove: (value) => {
                            this.value = null;
                            /* If removed, revert placeholder to the initial product name */
                        },
                        onClear: () => {
                            this.value = null;
                        }
                    };

                    /* Pre-load initial option if label is provided */
                    let initialLabel = this.$el.getAttribute('data-initial-label');
                    if (this.value && initialLabel) {
                        config.options = [{value: this.value, text: initialLabel, type: 'unknown'}];
                        config.items = [this.value];
                    }

                    if ('{{ $url }}') {
                        config.load = (query, callback) => {
                            let url = '{{ $url }}' + ( '{{ $url }}'.includes('?') ? '&' : '?' ) + 'q=' + encodeURIComponent(query);

                            /* Check for dynamic params */
                            const dataParams = this.$el.getAttribute('data-params');
                            if (dataParams) {
                                try {
                                    const params = JSON.parse(dataParams);
                                    const queryString = new URLSearchParams(params).toString();
                                    url += '&' + queryString;
                                } catch (e) {
                                    console.error('Invalid data-params JSON', e);
                                }
                            }

                            /* Get CSRF Token from Meta Tag */
                            const csrfToken = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');

                            fetch(url, {
                                credentials: 'include',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken || ''
                                }
                            })
                                .then(response => {
                                    if (!response.ok) throw new Error('Network response was not ok');
                                    return response.json();
                                })
                                .then(json => {
                                    if (Array.isArray(json)) {
                                        callback(json);
                                    } else {
                                        console.warn('TomSelect load: Expected array, got', json);
                                        callback();
                                    }
                                })
                                .catch((error) => {
                                    console.error('TomSelect load error:', error);
                                    callback();
                                });
                        };
                    }

                    this.tom = new TomSelect(this.$el, config);

                    /* Handle Initial Search - Search on Focus */
                    this.tom.on('focus', () => {
                        /* Only trigger if no value selected and search box is empty */
                        if (initialSearch && !this.value && this.tom.getValue() === '') {
                             /* Use setTimeout to ensure focus is fully handled */
                            setTimeout(() => {
                                if (!this.tom) return;
                                this.tom.setTextboxValue(initialSearch);
                                this.tom.search(initialSearch);
                            }, 50);
                        }
                    });

                    this.$watch('value', (newValue) => {
                        if (!this.tom) return;
                        const current = this.tom.getValue();
                        if (newValue !== current) {
                            if (!newValue) {
                                this.tom.clear(true);
                            } else {
                                this.tom.setValue(newValue, true);
                            }
                        }
                    });
                });
            }
        }"
        x-init="init"
        {{ $attributes->whereDoesntStartWith('wire:model') }}
        autocomplete="off"
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $option)
            <option value="{{ $option['value'] }}">{{ $option['text'] ?? $option['label'] }}</option>
        @endforeach
    </select>
</div>
