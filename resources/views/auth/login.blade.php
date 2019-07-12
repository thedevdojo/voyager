@extends('voyager::app', ['nav' => false, 'sidebar' => false])

@section('content')

    <div class="min-h-screen flex flex-col justify-center w-4/12 m-auto">
        
        <div class="bg-white shadow-md rounded flex">
            <div class="flex flex-col px-12 py-16 mb-4 flex w-full">
                <p class="text-gray-900 text-4xl font-black mb-4">Login</p>
                <div class="mb-4">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="username">Username</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" id="username" type="text" placeholder="Username">
                </div>
                <div class="mb-6">
                    <label class="block text-grey-darker text-sm font-bold mb-2" for="password">Password</label>
                    <input class="shadow appearance-none border border-red rounded w-full py-2 px-3 text-grey-darker mb-3" id="password" type="password" placeholder="******************">
                    <p class="text-red text-xs italic">Please choose a password.</p>
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue hover:bg-blue-dark text-white font-bold py-2 px-4 rounded" type="button">Sign In</button>
                    <a class="inline-block align-baseline font-bold text-sm text-blue hover:text-blue-darker" href="#">Forgot Password?</a>
                </div>
            </div>
        </div>
    </div>

@endsection