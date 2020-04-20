<template>
<div class="notifications sm:p-6 sm:justify-end" v-on:animationend="timeout($event)" v-on:animationcancel="timeout($event)">
    <div>
        <slide-x-right-transition group :duration="{enter: 500, leave: 0}">
            <div
                v-for="notification in $notify.notifications"
                :key="notification.uuid"
                :class="[`border-${notification.color}-500`, 'notification']"
                v-on:keyup.enter="submit(notification)"
                @mouseover="stopTimeout(notification)"
                @mouseleave="startTimeout(notification)">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0" v-if="notification.icon">
                            <icon :icon="notification.icon" :class="`text-${notification.color}-500`" />
                        </div>
                        <div class="w-0 flex-1" :class="notification.icon ? 'ml-3' : ''">
                            <span v-if="notification.title">
                                <p class="title">{{ notification.title }}</p>
                                <p class="message mt-1">{{ notification.message }}</p>
                            </span>
                            <p class="title" v-else v-html="notification.message"></p>
                            <div class="mt-4 flex" v-if="notification.input !== null">
                                <input
                                    type="text"
                                    class="voyager-input small w-full"
                                    v-model="notification.input"
                                    v-on:keyup="stopTimeout(notification)"
                                    v-focus />
                            </div>
                            <div class="mt-4 flex" v-if="notification.buttons.length >= 1">
                                <span class="inline-flex" v-for="(button, key) in notification.buttons" :key="'button-'+key">
                                    <button type="button" class="button" :class="button.class" @click="triggerClick(button, notification)">
                                        {{ button.text }}
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="close(notification)" class="inline-flex text-gray-400 focus:outline-none">
                                <icon icon="times" />
                            </button>
                        </div>
                    </div>
                </div>
                <div
                    v-if="notification.indeterminate === true"
                    class="w-full relative"
                    style="height: 0.4rem;"
                    >
                    <div class="h-full progress_bar_indeterminate" :class="`bg-${notification.color}-500`"></div>
                </div>
                <div
                    v-else-if="notification.timeout !== null"
                    class="w-full relative"
                    style="height: 0.4rem;">
                    <div class="h-full progress_bar" :class="`bg-${notification.color}-500`" :style="getProgressStyle(notification)" :data-uuid="notification.uuid"></div>
                </div>
            </div>
        </slide-x-right-transition>
    </div>
</div>
</template>
<script>
export default {
    methods: {
        close: function (notification) {
            this.$notify.remove(notification);

            if (notification.onClose) {
                notification.onClose(false);
            }
        },
        triggerClick: function (button, notification) {
            if (notification.input !== null) {
                if (button.value == true) {
                    button.callback(notification.input, notification);
                } else {
                    button.callback(false, notification);
                }
            } else {
                button.callback(button.value, notification);
            }

            if (notification.autoClose) {
                this.close(notification);
            }
        },
        submit: function (notification) {
            if (notification.input !== null) {
                notification.buttons.forEach(function (button) {
                    if (button.value == true) {
                        button.callback(notification.input, notification);
                    }
                });

                if (notification.autoClose) {
                    this.close(notification);
                }
            }
        },
        getProgressStyle: function (notification) {
            return {
                animationDuration: notification.timeout + 'ms',
                animationPlayState: notification.timeout_running ? 'running' : 'paused',
            };
        },
        stopTimeout: function (notification) {
            if (notification.timeout !== null) {
                notification.timeout_running = false;
            }
        },
        startTimeout: function (notification) {
            if (notification.timeout !== null) {
                notification.timeout_running = true;
            }
        },
        timeout: function (e) {
            if (e.animationName.startsWith('scale-x')) {
                var uuid = e.target.dataset.uuid;
                var notification = this.$notify.notifications.filter(function (n) {
                    return n.uuid == uuid;
                })[0];
                if (notification.timeout !== null) {
                    this.close(notification);
                }
            }
        },
    },
    mounted: function () {
        var vm = this;

        vm.$eventHub.$on('add-notification', function (notification) {
            if (notification.timeout !== null) {
                Vue.set(notification, 'timeout_running', true);
            }
        });
    }
};
</script>

<style lang="scss" scoped>
@keyframes scale-x {
    0% {
        transform: scaleX(1);
    }
    100% {
        transform: scaleX(0);
    }
}

@keyframes indeterminate {
    0% {
        width: 30%;
        left: -40%;
    }
    50% {
        left: 100%;
        width: 100%;
    }
    to {
        left: 100%;
        width: 0;
    }
}

.progress_bar {
    @apply rounded;
    transform-origin: left;
    animation: scale-x linear 1 forwards;
}
.progress_bar_indeterminate {
    @apply relative rounded;
    transition: width 0.25s ease;
    animation: indeterminate 2s ease infinite;
}
</style>