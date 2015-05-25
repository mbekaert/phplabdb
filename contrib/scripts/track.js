function Rectangle(bounds, opt_weight, opt_color) {
  this.bounds_ = bounds;
  this.weight_ = opt_weight || 2;
  this.color_ = opt_color || "#888888";
}

Rectangle.prototype = new GOverlay();

Rectangle.prototype.initialize = function(map) {
  var div = document.createElement("div");
  div.style.border = this.weight_ + "px solid " + this.color_;
  div.style.position = "absolute";
  map.getPane(G_MAP_MAP_PANE).appendChild(div);
  this.map_ = map;
  this.div_ = div;
}

Rectangle.prototype.remove = function() {
  this.div_.parentNode.removeChild(this.div_);
}

Rectangle.prototype.copy = function() {
  return new Rectangle(this.bounds_, this.weight_, this.color_, this.backgroundColor_, this.opacity_);
}

Rectangle.prototype.redraw = function(force) {
  if (!force) return;
  var c1 = this.map_.fromLatLngToDivPixel(this.bounds_.getSouthWest());
  var c2 = this.map_.fromLatLngToDivPixel(this.bounds_.getNorthEast());
  this.div_.style.width = Math.abs(c2.x - c1.x) + "px";
  this.div_.style.height = Math.abs(c2.y - c1.y) + "px";
  this.div_.style.left = (Math.min(c2.x, c1.x) - this.weight_) + "px";
  this.div_.style.top = (Math.min(c2.y, c1.y) - this.weight_) + "px";
}

function load(lat, lng, maxlat, maxlng) {
  if (GBrowserIsCompatible()) {
    var map = new GMap2(document.getElementById("map"));
    map.addControl(new GSmallMapControl());
    map.addControl(new GMapTypeControl());
    map.setCenter(new GLatLng(lat, lng), 8);
    if(maxlat != undefined) {
      var rectBounds = new GLatLngBounds(
        new GLatLng(lat, lng),
        new GLatLng(maxlat, maxlng)
      );
      map.addOverlay(new Rectangle(rectBounds));
    } else {
      var point = new GLatLng(lat, lng);
      map.addOverlay(new GMarker(point));
    }
  }
}
