<template>
<div>
    <fade-transition :duration="150" tag="div" group>
        <form method="post" :action="route('voyager.login')" v-if="!passwordForgotOpen" key="login-form">
            <input type="hidden" name="_token" :value="$store.csrf_token">
            <h2 class="text-gray-800 dark:text-gray-200 mb-6 font-bold">{{ __('voyager::auth.login') }}</h2>

            <alert v-if="error" color="red" role="alert" :closebutton="false">
                {{ error }}
            </alert>
            <alert v-if="success" color="green" role="alert" :closebutton="false">
                {{ success }}
            </alert>

            <slot name="login">
                <div class="w-full">
                    <input :value="old.email" type="email" name="email" id="email" class="voyager-input w-full mb-4" :placeholder="__('voyager::auth.email')" autofocus>
                </div>
                <div class="w-full">
                    <input :value="old.password" type="password" name="password" id="password" class="voyager-input w-full mb-3" :placeholder="__('voyager::auth.password')">
                </div>
                <div class="w-full flex-items">
                    <input type="checkbox" class="voyager-input" name="remember" id="remember">
                    <label for="remember">{{ __('voyager::auth.remember_me') }}</label>
                </div>
            </slot>

            <div class="flex items-center justify-between mt-6">
                <button class="button indigo" type="submit">
                    {{ __('voyager::auth.login') }}
                </button>
                <a href="#" v-if="hasPasswordForgot" @click.prevent="passwordForgotOpen = true">
                    {{ __('voyager::auth.forgot_password') }}
                </a>
            </div>
        </form>
        <form method="post" :action="route('voyager.forgot_password')" v-if="hasPasswordForgot && passwordForgotOpen" key="password-form">
            <h2 class="text-gray-800 dark:text-gray-200 mb-6 font-bold">{{ __('voyager::auth.forgot_password') }}</h2>
            <input type="hidden" name="_token" :value="$store.csrf_token">
            <div class="mt-4">
                <slot name="forgot_password" />
                <div class="flex items-center justify-between mt-6">
                    <button class="button indigo mt-4" type="submit">
                        {{ __('voyager::auth.request_password') }}
                    </button>
                    <a href="#" @click.prevent="passwordForgotOpen = false">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </fade-transition>
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