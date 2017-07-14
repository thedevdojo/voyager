// src URL for news and other Voyager related stuff
var remote_src_url = 'https://s3.amazonaws.com/laravelvoyager/voyager.js';

if(urlExists(remote_src_url)){
    loadJS(remote_src_url)
}

function urlExists(url)
{
    var http = new XMLHttpRequest();
    http.open('GET', url, true);
    http.send();
    return http.status!=404;

} 

function loadJS(url){
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.setAttribute("async", "");
    script.src = url;
    document.body.appendChild(script);
}