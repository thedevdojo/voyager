@extends('voyager::app', ['nav' => false, 'sidebar' => false, 'fullscreen' => true])

@section('content')

    <div class="flex min-h-screen">
        <div class="w-1/4 bg-gray-200" style="background:url('{{ Voyager::assetUrl('images/login-bg.png') }}'); background-size:cover">
            <div class="flex justify-left pl-4 items-center h-16 logo">
                    <img src="{{ Voyager::assetUrl('images/logo-icon.png') }}" class="h-8 mt-1" alt="Logo Icon">
                    <span class="font-bold ml-2 text-gray-900 mt-1 text-base uppercase">Voyager</span>
                </div>
        </div>
        <div class="w-3/4 bg-white-100 flex flex-col justify-center items-center">
            <div class="flex flex-col p-40 flex w-1/2">
                <h1 class="text-gray-800 mb-6 text-4xl font-bold">Login</h1>
                <v-input type="text" id="email" class="mb-4" placeholder="Email Address" autofocus></v-input>
                <v-input type="password" id="password" placeholder="Password"></v-input>
                <div class="flex items-center justify-between mt-6">
                    <button class="bg-indigo-600 hover:bg-blue-dark text-white font-bold py-3 px-6 rounded" type="button">Sign In</button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker" href="#">Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>

    <!--div class="md:container m-auto sm:w-full sm:mx-0 xl:px-16">
        <div class="min-h-screen flex flex-col justify-center w-full md:w-6/12 lg:w-5/12 xl:w-4/12 m-auto bg-white md:bg-gray-200">
            
            <div class="bg-white md:shadow-xl rounded flex -mt-12">
                <div class="flex flex-col px-12 py-12 mb-4 flex w-full">
                    <h1 class="voyager-h1 mb-6">Login</h1>
                    <v-input type="text" id="email" placeholder="Email Address" autofocus></v-input>
                    <v-input type="password" id="password" placeholder="Password"></v-input>
                    <div class="flex items-center justify-between mt-6">
                        <button class="bg-indigo-600 hover:bg-blue-dark text-white font-bold py-3 px-6 rounded" type="button">Sign In</button>
                        <a class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker" href="#">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div-->

@endsection