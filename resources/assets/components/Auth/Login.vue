<template>
<div>
    <fade-transition :duration="150" tag="div" group>
        <form method="post" :action="route('voyager.login')" v-if="!passwordForgotOpen" key="login-form">
            <input type="hidden" name="_token" :value="$store.csrf_token">

            <alert v-if="error" color="red" role="alert" :closebutton="false">
                {{ error }}
            </alert>
            <alert v-if="success" color="green" role="alert" :closebutton="false">
                {{ success }}
            </alert>
            <div class="mb-4" v-if="success || error"></div>

            <slot name="login">
                <div class="w-full mt-1">
                    <label for="email" class="block text-sm font-medium leading-5 text-gray-700">{{ __('voyager::auth.email') }}</label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input :value="old.email" type="email" name="email" id="email" class="voyager-input w-full mb-4 placeholder-gray-400" autofocus>
                    </div>
                </div>
                <div class="w-full mt-6">
                    <label for="password" class="block text-sm font-medium leading-5 text-gray-700">{{ __('voyager::auth.password') }}</label>
                    <div class="mt-1 rounded-md shadow-sm">
                        <input :value="old.password" type="password" name="password" id="password" class="voyager-input w-full mb-3 placeholder-gray-400">
                    </div>
                </div>
                <div class="w-full flex justify-between mt-4">
                    <div>
                        <input type="checkbox" class="voyager-input" name="remember" id="remember">
                        <label for="remember" class="text-sm leading-8">{{ __('voyager::auth.remember_me') }}</label>
                    </div>
                    
                    <a href="#" v-if="hasPasswordForgot" class="font-medium text-sm leading-8" @click.prevent="passwordForgotOpen = true">
                        {{ __('voyager::auth.forgot_password') }}
                    </a>
                </div>
            </slot>

            <div class="flex items-center justify-between mt-4">
                <button class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out" type="submit">
                    {{ __('voyager::auth.login') }}
                </button>
                
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