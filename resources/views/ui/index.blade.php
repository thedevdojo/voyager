@extends('voyager::app', ['sidebarSecondary' => true])

@section('sidebar-secondary')

    <div class="h-16 flex flex-col justify-center">
        <h1 class="pl-8 text-sm font-black text-gray-600">UI Components</h1>
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
            
    </div>
    
@endsection