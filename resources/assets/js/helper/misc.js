window.debug = function (message, printToConsole = true, type = 'log')
{
    if (printToConsole) {
        if (type == 'log')
            console.log(message);
        else if (type == 'error')
            console.error(message);
        else if (type == 'message')
            console.message(message);
        else if (type == 'warn')
            console.warn(message);
    }
}
Vue.prototype.debug = debug;

window.copyToClipboard = function (message)
{
    var dummy = document.createElement('textarea');
    document.body.appendChild(dummy);
    dummy.value = message.replace(/\'/g, "'");
    dummy.select();
    document.execCommand('copy');
    document.body.removeChild(dummy);

    return false;
}
Vue.prototype.copyToClipboard = copyToClipboard;