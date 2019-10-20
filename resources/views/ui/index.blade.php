@extends('voyager::app', ['sidebarSecondary' => true])

@section('sidebar-secondary')

    <div class="h-16 flex flex-col justify-center">
        <h1 class="pl-8 text-sm font-medium text-gray-600">UI Components</h1>
    </div>

@endsection

@section('content')
<div class="p-8 bg-white border border-gray-300 rounded">
    <h2 class="font-semibold text-gray-700 text-lg mb-8">Form Inputs</h2>
    <label>Text Input</label>
    <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">&lt;v-input type="text" placeholder="Email" name="email" id="email"&gt;&lt;/v-input&gt;</code>
    <v-input type="text" placeholder="Email" name="email" id="email"></v-input>
    <hr class="border-0 border-b border-gray-200 my-8">
    <label>Password Input</label>
    <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">&lt;v-input type="password" placeholder="Password" name="password" id="password"&gt;&lt;/v-input&gt;</code>
    <v-input type="password" placeholder="Password" name="password" id="password"></v-input>
    <hr class="border-0 border-b border-gray-200 my-8">
    <label>Number Input</label>
    <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">&lt;v-input type="number" placeholder="Age" name="age" id="age"&gt;&lt;/v-input&gt;</code>
    <v-input type="number" placeholder="Age" name="age" id="age"></v-input>
</div>

<div class="p-8 bg-white border border-gray-300 rounded mt-4">
    <h2 class="font-semibold text-gray-700 text-lg mb-8">Buttons</h2>
    <label>Primary</label>
    <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">&lt;v-button type="default"&gt;Default&lt;/v-input&gt;</code>
    <v-button type="default">Default</v-button> <v-button type="primary">Primary</v-button> <v-button type="success">Success</v-button> <v-button type="info">Info</v-button> <v-button type="warning">Warning</v-button> <v-button type="danger">Danger</v-button>
    <hr class="border-0 border-b border-gray-200 my-8">

    <h2 class="font-semibold text-gray-700 text-lg mb-8">Button Group</h2>
    <h3 class="font-medium text-gray-700 text-lg mb-8">Simple</h3>
    <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">
<pre>
&lt;div class="button-group"&gt;
    &lt;button class="button blue"&gt;Button 1&lt;/button&gt;
    &lt;button class="button blue"&gt;Button 2&lt;/button&gt;
    &lt;button class="button blue"&gt;Button 3&lt;/button&gt;
&lt;/div&gt;
</pre>
    </code>
    <div class="button-group">
        <button class="button blue">Button 1</button>
        <button class="button blue">Button 2</button>
        <button class="button blue">Button 3</button>
    </div>
    <hr class="border-0 border-b border-gray-200 my-8">
    <h3 class="font-medium text-gray-700 text-lg mb-8">Small</h3>
    <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">
<pre>
&lt;div class="button-group"&gt;
    &lt;button class="button blue small"&gt;Button 1&lt;/button&gt;
    &lt;button class="button blue small"&gt;Button 2&lt;/button&gt;
    &lt;button class="button blue small"&gt;Button 3&lt;/button&gt;
&lt;/div&gt;
</pre>
    </code>
    <div class="button-group">
        <button class="button blue small">Button 1</button>
        <button class="button blue small">Button 2</button>
        <button class="button blue small">Button 3</button>
    </div>
    <hr class="border-0 border-b border-gray-200 my-8">
    <h3 class="font-medium text-gray-700 text-lg mb-8">Colors</h3>
    <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">
<pre>
&lt;div class="button-group"&gt;
    &lt;button class="button green"&gt;Green&lt;/button&gt;
    &lt;button class="button red"&gt;Red&lt;/button&gt;
    &lt;button class="button yellow"&gt;Yellow&lt;/button&gt;
    &lt;button class="button purple"&gt;Purple&lt;/button&gt;
    &lt;button class="button orang&gt;Orange&lt;/button&gt;
&lt;/div&gt;
</pre>
    </code>
    <div class="button-group">
        <button class="button green">Green</button>
        <button class="button red">Red</button>
        <button class="button yellow">Yellow</button>
        <button class="button purple">Purple</button>
        <button class="button orange">Orange</button>
    </div>
</div>
    
@endsection