/*--------------------
|
| HELPERS
|
--------------------*/

var displayAlert = function(alert, alerter) {
    let alertMethod = alerter[alert.type];

    if (alertMethod) {
        return alertMethod(alert.message);
    }

    alerter.error("No alert method found for alert type: " + alert.type);
}

var displayAlerts = function(alerts, alerter, type) {
    if (type) {
        // Only display alerts of this type...
        alerts = alerts.filter(function(alert) {
            return type == alert.type;
        });
    }

    for (a in alerts) {
        displayAlert(alerts[a], alerter);
    }
}

var bootstrapAlerter = function(customOptions) {
    // Default options
    let options = {
        alertsContainer: '#alertsContainer',
        dismissible: false,
        dismissButton: '<button class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
    };

    if (customOptions) {
        options = $.extend({}, options, customOptions);
    }

    let dismissibleClass = '';
    let dismissButton = '';

    if (options.dismissible) {
        dismissButton = options.dismissButton;
        dismissibleClass = ' alert-dismissible';
    }

    function notify(type, message) {
        let alert = '<div class="alert alert-'  + type +  dismissibleClass + '" role="alert">'
                        + dismissButton + message +
                    '</div>';

        $(options.alertsContainer).append(alert);
    }

    return {
        success(message) {
            notify('success', message);
        },
        info(message) {
            notify('info', message);
        },
        warning(message) {
            notify('warning', message);
        },
        error(message) {
            notify('danger', message);
        }
    };
}

var setImageValue = function(url){
  $('.mce-btn.mce-open').parent().find('.mce-textbox').val(url);
}

exports.setImageValue = setImageValue;
exports.displayAlert = displayAlert;
exports.displayAlerts = displayAlerts;
exports.bootstrapAlerter = bootstrapAlerter;