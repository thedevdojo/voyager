function displayAlerts(alerts, alerter, type) {
    if (type) {
        // Only display alerts of this type...
        alerts = alerts.filter(function(alert) {
            return type == alert.type;
        });
    }

    let alert, alertMethod;
    
    for (a in alerts) {
        alert = alerts[a];
        alertMethod = alerter[alert.type];

        if (alertMethod) {
            alertMethod(alert.message);
            continue;
        }
        
        alerter.error("No alert method found for alert type: " + alert.type);
    }
}
