@extends('voyager::app')

@section('page-title', 'UI')

@section('content')
<div class="voyager-card">
    <h2 class="title">Buttons</h2>
    <div class="body">

        <h2 class="font-semibold text-gray-700 text-lg mb-8">Button Group</h2>
        <h3 class="font-medium text-gray-700 text-lg mb-8">Simple</h3>
        <code class="bg-gray-800 font-mono text-xs text-gray-200 rounded my-2 block px-6 py-4 border border-gray-900">
    <pre>
&lt;div class="button-group"&gt;
    &lt;button class="button blue"&gt;Button 1&lt;/button&gt;
    &lt;button class="button blue"&gt;Button 2&lt;/button&gt;
    &lt;button class="button blue"&gt;Button 3&lt;/button&gt;
&lt;/div&gt;</pre>
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
&lt;/div&gt;</pre>
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
&lt;/div&gt;</pre>
        </code>
        <div class="button-group">
            <button class="button green">Green</button>
            <button class="button red">Red</button>
            <button class="button yellow">Yellow</button>
            <button class="button purple">Purple</button>
            <button class="button orange">Orange</button>
        </div>
    </div>
</div>
    
@endsection