function loadVoyagerRemoteJS(url)
{
    // dynamically Load the script if it exists
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.setAttribute("async", "");
    script.src = url;
    document.body.appendChild(script);
}
