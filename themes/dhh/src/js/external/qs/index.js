!(function() {
  "use strict";
  Array.isArray ||
    (Array.isArray = function(r) {
      return "[object Array]" === Object.prototype.toString.call(r);
    });
  var r = {
    get: function() {
      var r = window.location.search,
        t = {};
      return "" === r
        ? t
        : ((r = r.slice(1)),
          (r = r.split("&")),
          r.map(function(r) {
            var i, o;
            (r = r.split("=")),
              (i = r[0]),
              (o = r[1]),
              t[i]
                ? (Array.isArray(t[i]) || (t[i] = [t[i]]), t[i].push(o))
                : (t[i] = o);
          }),
          t);
    }
  };
  if (window) {
    if (window.qs)
      throw new Error("Error bootstrapping qs: window.qs already set.");
    window.qs = r;
  }
})();
