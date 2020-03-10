<template>
<div>
    <form method="post" :action="route('voyager.login')">
        <input type="hidden" name="_token" :value="$globals.csrf_token">
        <h1 class="text-gray-800 dark:text-gray-200 mb-6 text-4xl font-bold">{{ __('voyager::auth.login') }}</h1>

        <div v-if="error" class="alert red mb-4" role="alert">{{ error }}</div>
        <div v-if="success" class="alert blue mb-4" role="alert">{{ success }}</div>

        <slot name="login">
            <input :value="old.email" type="email" name="email" id="email" class="voyager-input mb-4" :placeholder="__('voyager::auth.email')" autofocus>
            <input :value="old.password" type="password" name="password" id="password" class="voyager-input mb-3" :placeholder="__('voyager::auth.password')">
            
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">{{ __('voyager::auth.remember_me') }}</label>
        </slot>

        <div class="flex items-center justify-between mt-6">
            <button class="button indigo" type="submit">
                {{ __('voyager::auth.login') }}
            </button>
            <a href="#" v-if="hasPasswordForgot" @click.prevent="passwordForgotOpen = !passwordForgotOpen">
                {{ __('voyager::auth.forgot_password') }}
            </a>
        </div>
    </form>
    <form method="post" :action="route('voyager.forgot_password')" v-if="hasPasswordForgot">
        <input type="hidden" name="_token" :value="$globals.csrf_token">
        <div class="mt-4" v-bind:class="{ 'hidden': !passwordForgotOpen}">
            <slot name="forgot_password" />
            <button class="button indigo mt-4" type="submit">
                {{ __('voyager::auth.request_password') }}
            </button>
        </div>
    </form>
</div>
</template>
<script>
export default {
    props: ['error', 'success', 'old'],
    data: function () {
        return {
            passwordForgotOpen: false,
        };
    },
    computed: {
        hasPasswordForgot: function () {
            return this.$slots['forgot_password'] !== undefined;
        }
    },
};
</script>