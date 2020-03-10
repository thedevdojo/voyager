@extends('voyager::app', ['nav' => false, 'sidebar' => false, 'fullscreen' => true])
@section('page-title', __('voyager::auth.login'))
@section('content')
<div class="flex min-h-screen" id="voyager-login">
    <div class="w-0 md:w-3/5 bg-gray-200" style="background:url('{{ Voyager::assetUrl('images/login-bg.png') }}'); background-size:cover">
        <div class="flex justify-left pl-4 items-center h-16 logo">
            <img src="{{ Voyager::assetUrl('images/logo-icon.png') }}" class="h-8 mt-1" alt="Logo Icon">
            <span class="font-bold ml-2 text-gray-900 mt-1 text-base uppercase">{{ Voyager::setting('admin.title', true, 'Voyager') }}</span>
        </div>
    </div>
    <div class="w-full md:w-3/5 bg-white-100 dark:bg-gray-900 flex flex-col justify-center items-center">
        <div class="flex flex-col p-3 lg:p-40 flex">
            <login error="{{ Session::get('error', null) }}" success="{{ Session::get('success', null) }}" :old="{{ json_encode(old()) }}">
                @if ($authentication->loginView())
                <div slot="login">
                    {!! $authentication->loginView() !!}
                </div>
                @endif

                @if ($authentication->forgotPasswordView())
                <div slot="forgot_password">
                    {!! $authentication->forgotPasswordView() !!}
                </div>
                @endif
            </login>
        </div>
    </div>
</div>
@endsection