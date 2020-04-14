Vue.prototype.$notify = new Vue({
    data: function () {
        return {
            notifications: [],
        };
    },
    methods: {
        confirm: function (
            message,
            callback,
            title = false,
            color = 'blue',
            trueText = 'Yes',
            falseText = 'No',
            timeout = null,
            indeterminate = false,
            icon = 'question-circle',
            onClose = null,
            autoClose = true,
            classes = ''
        ) {
            var buttons = [
                {
                    text: trueText,
                    class: 'green',
                    callback: callback,
                    value: true
                }, {
                    text: falseText,
                    class: 'red',
                    callback: callback,
                    value: false
                }
            ];
            this.notify(message, title, color, timeout, indeterminate, icon, buttons, null, onClose, autoClose, classes);
        },

        prompt: function (
            message,
            input,
            callback,
            color = 'blue',
            okText = 'Ok',
            cancelText = 'Cancel',
            title = false,
            timeout = null,
            indeterminate = false,
            icon = 'question-circle',
            onClose = null,
            autoClose = true,
            classes = ''
        ) {
            var buttons = [
                {
                    text: okText,
                    class: 'green',
                    callback: callback,
                    value: true,
                }, {
                    text: cancelText,
                    class: 'red',
                    callback: callback,
                    value: false,
                }
            ];
            this.notify(message, title, color, timeout, indeterminate, icon, buttons, input, onClose, autoClose, classes);
        },

        notify: function (message, title = null, color = 'blue', timeout = null, indeterminate = false, icon = 'info-circle', buttons = [], input = null, onClose = null, autoClose = true, classes = '') {
            var vm = this;

            var uuid = vm.uuid();

            var notification = {
                message: message,
                title: title,
                color: color,
                timeout: timeout,
                indeterminate: indeterminate,
                icon: icon,
                buttons: buttons,
                input: input,
                onClose: onClose,
                autoClose: autoClose,
                classes: classes,
                uuid: uuid,
            };
            vm.notifications.push(notification);

            vm.$eventHub.$emit('add-notification', notification);
        },

        remove: function (notification) {
            var index = this.notifications.indexOf(notification);
            if (index >= 0) {
                this.notifications.splice(index, 1);
            }
        }
    }
});