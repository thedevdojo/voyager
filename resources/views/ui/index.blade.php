@extends('voyager::app')

@section('page-title', 'UI')

@section('content')
<card title="UI Elements">
    <div>
        <span class="mr-4">Jump to: </span>
        <div class="inline w-full">
        <button class="button blue" v-scroll-to="'#ui-tags'">
                Tag input
            </button>
            <button class="button blue" v-scroll-to="'#ui-headings'">
                Headings
            </button>
            <button class="button blue" v-scroll-to="'#ui-buttons'">
                Buttons
            </button>
            <button class="button blue" v-scroll-to="'#ui-inputs'">
                Inputs
            </button>
            <button class="button blue" v-scroll-to="'#ui-badges'">
                Badges
            </button>
            <button class="button blue" v-scroll-to="'#ui-alerts'">
                Alerts
            </button>
            <button class="button blue" v-scroll-to="'#ui-notifications'">
                Notifications
            </button>
        </div>
    </div>
</card>

<collapsible title="Tag input" id="ui-tags">
    <tag-input v-model="$store.ui.tags"></tag-input>
</collapsible>

<collapsible title="Heading" id="ui-headings">
    <h1>H1 Heading</h1>
    <h2>H2 Heading</h2>
    <h3>H3 Heading</h3>
    <h4>H4 Heading</h4>
    <h5>H5 Heading</h5>
    <h6>H6 Heading</h6>
</collapsible>

<collapsible title="Buttons" id="ui-buttons">
    <collapsible title="Default">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Small">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Large">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'large', color]">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="With Icon">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            <icon icon="info-circle" class="mr-1"></icon>
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </button>
    </collapsible>
    <collapsible title="Responsive">
        <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', 'small', color]">
            <icon icon="info-circle"></icon>
            <span>@{{ color[0].toUpperCase() + color.slice(1) }}</span>
        </button>
    </collapsible>
    <collapsible title="Button group">
        <div class="button-group">
            <button v-for="color in $store.ui.colors" :key="'button-'+color" :class="['button', color]">
                @{{ color[0].toUpperCase() + color.slice(1) }}
            </button>
        </div>
    </collapsible>
</collapsible>

<collapsible title="Inputs" id="ui-inputs">
    <collapsible title="Default">
        <input type="text" class="voyager-input w-full" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Disabled">
            <input type="text" class="voyager-input w-full" disabled placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Small">
        <input type="text" class="voyager-input w-full small" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="With label">
        <label class="label" for="labeled-input">Label</label>
        <input type="text" class="voyager-input w-full" id="labeled-input" placeholder="Placeholder" />
    </collapsible>
    <collapsible title="Colors" :opened="false">
        <input v-for="color in $store.ui.colors" type="text" class="voyager-input w-full mb-2" :class="color" :placeholder="ucfirst(color)" :key="'input-'+color">
    </collapsible>
</collapsible>

<collapsible title="Badges" id="ui-badges">
    <collapsible title="Default">
        <badge v-for="color in $store.ui.colors" :color="color" :key="'badge-'+color">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </badge>
    </collapsible>
    <collapsible title="Large">
        <badge v-for="color in $store.ui.colors" :color="color" :key="'badge-'+color" class="large">
            @{{ color[0].toUpperCase() + color.slice(1) }}
        </badge>
    </collapsible>
</collapsible>

<collapsible title="Alerts" id="ui-alerts">
    <alert v-for="color in $store.ui.colors" :color="color" :key="'alert-'+color" class="mb-3">
        <span slot="title">@{{ color[0].toUpperCase() + color.slice(1) }}</span>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid pariatur, ipsum similique veniam quo totam eius aperiam dolorum.</p>
    </alert>
</collapsible>

<collapsible title="Notifications" id="ui-notifications">
    <collapsible v-for="color in $store.ui.colors" :key="'notification_'+color" :title="ucfirst(color)">
        <div class="inline-flex">
            <button @click="$notify.notify($store.ui.lorem, ucfirst(color), color)" class="button" :class="color">Message and title</button>
            <button @click="$notify.notify($store.ui.lorem, null, color)" class="button" :class="color">Message only</button>
            <button @click="$notify.notify($store.ui.lorem, ucfirst(color), color, null, true)" class="button" :class="color">Indeterminate</button>
            <button @click="$notify.notify($store.ui.lorem, ucfirst(color), color, 5000, false)" class="button" :class="color">With timeout</button>
        </div>
    </collapsible>
    <collapsible title="Confirm">
        <div class="inline-flex">
            <button @click="$notify.confirm('Are you sure?', function (result) {})" class="button blue">Simple</button>
            <button @click="$notify.confirm('Are you sure?', function (result) {}, null, 'blue', 'Yes', 'No', null, true)" class="button blue">Indeterminate</button>
            <button @click="$notify.confirm('Are you sure?', function (result) {}, null, 'blue', 'Yes', 'No', 5000)" class="button blue">With timeout</button>
            <button @click="$notify.confirm('Are you sure?', function (result) {}, null, 'blue', 'Of course', 'Nah')" class="button blue">Custom buttons</button>
        </div>
    </collapsible>
    <collapsible title="Prompt">
        <div class="inline-flex">
            <button @click="$notify.prompt('Enter your name', '', function (result) {})" class="button blue">Simple</button>
            <button @click="$notify.prompt('Enter your name', '', function (result) {}, 'blue', 'Save', 'Abort')" class="button blue">Custom buttons</button>
            <button @click="$notify.prompt('Enter your name', $store.ui.name, function (result) { if (result) { $store.ui.name = result; } })" class="button blue">Value: @{{ $store.ui.name }}</button>
        </div>
    </collapsible>
</collapsible>

@endsection