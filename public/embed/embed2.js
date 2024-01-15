function optionalCallbackFunction(response) {
  if (response.getId() || response.getExId()) {
    getResult(response);
  }
}
function getResult(response) {
  var method = 'POST';
  var params = ['instance=3168457626'];
  if (response.getId()) params.push('id='+encodeURIComponent(response.getId()));
  if (response.getExId()) params.push('exid='+encodeURIComponent(response.getExId()));
  var url = 'https://script.anura.io/result.json'+('GET' === method ? '?'+params.join('&'): '');
  // internet explorer 8-9
  if (window.XDomainRequest) {
    var http = new XDomainRequest();
    if (http) {
      http.open(method, document.location.protocol === 'https:' ? url: url.replace('https:', 'http:'));
      http.onprogress = function(){}
      http.ontimeout = function(){}
      http.onerror = function(){}
      http.onload = function(){anuraResultHandler(this);}
      setTimeout(function(){http.send('POST' === method ? params.join('&'): '');}, 0);
    }
  // other browsers
  } else if (window.XMLHttpRequest) {
    var http = new XMLHttpRequest();
    if (http && 'withCredentials' in http) {
      http.open(method, url, true);
      if ('POST' === method) http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      http.onload = function(){anuraResultHandler(this);}
      http.send('POST' === method ? params.join('&'): '');
    }
  }
}

function anuraResultHandler(http) {
  let responseData = JSON.parse(http.response);
  if (responseData.result != 'bad') {
    renderVidpopWidget();
  }
}

function renderVidpopWidget() {
  const d=document.createElement("div"),
    t=document.createElement("div");
    d.className="vidpop-common vidpop-wrapper";
    const o=document.createElement("div");
    o.className="vidpop-widget-inner",
    d.id="vidpop-wrapper",
    t.className="vidpop-common vidpop-widget border-rect d-custom-none",
    t.id="vidpop-widget",
    "right"===window.VIDPOP_EMBED_CONFIG.option.position?
      (d.classList.add("vidpop-right"),t.classList.add("vidpop-right")):
      (d.classList.add("vidpop-left"),t.classList.add("vidpop-left"));
    const i=document.createElement("video"),
    c=document.createElement("source");
    if(c.type="video/mp4",
      c.src=window.VIDPOP_EMBED_CONFIG.thumb,i.appendChild(c),
      i.autoplay=!0,
      i.muted="muted",
      i.loop=!0,
      i.className="vidpop-video-wrapper",
      i.style.border="5px solid "+window.VIDPOP_EMBED_CONFIG.option.background,
      "circle"===window.VIDPOP_EMBED_CONFIG.widget?
        (d.classList.add("border-circle"),
        i.classList.add("border-circle")):
        "v-rect"===window.VIDPOP_EMBED_CONFIG.widget&&(d.classList.add("border-rect"),
        i.classList.add("border-rect")),
        o.addEventListener("click",function(){
          document.getElementById("iframe-widget").src=window.VIDPOP_EMBED_CONFIG.url;
          const e=document.getElementById("vidpop-widget");
          e.classList.remove("d-custom-none"),
          e.classList.add("d-custom-block");
          const d=document.getElementById("vidpop-wrapper");
          d.classList.add("d-custom-none"),
          d.classList.remove("d-custom-block")}),
          o.appendChild(i),
          window.VIDPOP_EMBED_CONFIG.option.text&&"null"!==window.VIDPOP_EMBED_CONFIG.option.text&&"undefined"!==window.VIDPOP_EMBED_CONFIG.text){
            const e=document.createElement("div");
            e.className="vidpop-widget-text",
            e.innerText=window.VIDPOP_EMBED_CONFIG.option.text,
            o.appendChild(e)
    }
    d.appendChild(o),
    document.body.appendChild(d),
    i.play();
    const n=document.createElement("div");
    n.className="vidpop-widget-wrapper";
    const s=document.createElement("div");
    s.className="vidpop-widget-close",
    s.addEventListener("click",function(){
      document.getElementById("iframe-widget").src="https://vid-gen.com/loader";
      const e=document.getElementById("vidpop-widget");
      e.classList.add("d-custom-none"),
      e.classList.remove("d-custom-block");
      const d=document.getElementById("vidpop-wrapper");
      d.classList.add("d-custom-block"),
      d.classList.remove("d-custom-none")
    });

    const p=document.createElement("iframe");
    p.allow="camera *;microphone *;autoplay",
    p.id="iframe-widget",
    p.src="https://vid-gen.com/loader",
    n.appendChild(p),
    n.appendChild(s),
    t.appendChild(n),
    document.body.appendChild(t)
}
document.addEventListener("DOMContentLoaded",function(e){
  // let arr = window.location.origin.split('//');
  // let site_name = arr[1];
  // if (window.VIDPOP_EMBED_CONFIG.site !== site_name) {
      var req = new XMLHttpRequest();
      req.open("GET", window.VIDPOP_EMBED_CONFIG.main + "/api/metrics-click-count?pv_id=" + window.VIDPOP_EMBED_CONFIG.vidgenID);
      req.send();
      req.onload = () => {
        if (req.status == 200) {
          var res = JSON.parse(req.response);
          if (res['visible'] == 1 && res['status'] == 'Approved') {
            if (res['count'] % 100 == 0) { // every 100 times call Anura.
              var anura = document.createElement('script');
              if ('object' === typeof anura) {
                var request = {
                    instance: '3168457626',
                    callback: 'optionalCallbackFunction'
                };
                var params = [];
                for (var x in request) params.push(x+'='+encodeURIComponent(request[x]));
                params.push(Math.floor(1E12*Math.random()+1));
                anura.type = 'text/javascript';
                anura.async = true;
                anura.src = 'https://script.anura.io/request.js?'+params.join('&');
                var script = document.getElementsByTagName('script')[0];
                script.parentNode.insertBefore(anura, script);
              }
            } else
              renderVidpopWidget();
          }
        }
      }
  // }
});
