// src URL for news and other Voyager related stuff
var remote_src_url = 'https://s3.amazonaws.com/laravelvoyager/voyager.js';

loadVoyagerRemoteJS(remote_src_url);

function loadVoyagerRemoteJS(url)
{
    // dynamically Load the script if it exists
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.setAttribute("async", "");
    script.src = url;
    document.body.appendChild(script);
}