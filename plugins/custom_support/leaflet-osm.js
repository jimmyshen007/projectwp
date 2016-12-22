L.OSM = {};

L.OSM.TileLayer = L.TileLayer.extend({
  options: {
    url: document.location.protocol === 'https:' ?
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' :
      'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    attribution: '© <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
  },

  initialize: function (options) {
    options = L.Util.setOptions(this, options);
    L.TileLayer.prototype.initialize.call(this, options.url);
  }
});

L.OSM.Mapnik = L.OSM.TileLayer.extend({
  options: {
    url: document.location.protocol === 'https:' ?
      'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' :
      'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    maxZoom: 19
  }
});

L.OSM.CycleMap = L.OSM.TileLayer.extend({
  options: {
    url: document.location.protocol === 'https:' ?
      'https://{s}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey={apikey}' :
      'http://{s}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey={apikey}',
    attribution: '© <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://www.thunderforest.com/" target="_blank">Andy Allan</a>'
  }
});

L.OSM.TransportMap = L.OSM.TileLayer.extend({
  options: {
    url:  document.location.protocol === 'https:' ?
      'https://{s}.tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey={apikey}' :
      'http://{s}.tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey={apikey}',
    attribution: '© <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://www.thunderforest.com/" target="_blank">Andy Allan</a>'
  }
});

L.OSM.MapQuestOpen = L.OSM.TileLayer.extend({
  options: {
    url: document.location.protocol === 'https:' ?
      'https://otile{s}-s.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png' :
      'http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png',
    subdomains: '1234',
    attribution: '© <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. ' + document.location.protocol === 'https:' ?
      'Tiles courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="https://developer.mapquest.com/content/osm/mq_logo.png">' :
      'Tiles courtesy of <a href="http://www.mapquest.com/" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png">'
  }
});

L.OSM.HOT = L.OSM.TileLayer.extend({
  options: {
    url: document.location.protocol === 'https:' ?
      'https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png' :
      'http://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png',
    maxZoom: 20,
    subdomains: 'abc',
    attribution: '© <a href="http://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
  }
});

var curRouteControl= null; //Current route layer on the map.
L.OSM.DataLayer = L.FeatureGroup.extend({
  options: {
    categoryTags: ['amenity', 'shop', 'highway', 'railway', 'leisure', 'public_transport'],
    areaTags: ['area', 'building', 'leisure', 'tourism', 'ruins', 'historic', 'landuse', 'military', 'natural', 'sport'],
    uninterestingTags: ['source', 'source_ref', 'source:ref', 'history', 'attribution', 'created_by', 'tiger:county', 'tiger:tlid', 'tiger:upload_uuid'],
    styles: {},
    targetPoint: null, //The point to search around by.
    mapObj: null //Main map object constructed before.
  },

  layerSelf: this,
  initialize: function (xml, options) {
    L.Util.setOptions(this, options);

    L.FeatureGroup.prototype.initialize.call(this);

    if (xml) {
      this.addData(xml);
    }
  },

  addData: function (features) {
    var self = this;
    function routing(e){
      e.preventDefault();
      var profile = $(this).data('profile'); //profile for routing, such as car, foot or bike.
      var options = $(this).data('options');
      var suffixID = options.sfxid;
      if (self.options.mapObj && curRouteControl) {
        curRouteControl.removeFrom(self.options.mapObj);
      }
      curRouteControl = L.Routing.control({
        waypoints: [
          L.latLng(options.coords[0], options.coords[1]),
          L.latLng(options.coords[2], options.coords[3])],
        fitSelectedRoutes: true,
        router: L.Routing.mapbox('pk.eyJ1IjoianNvbnd1IiwiYSI6ImNpa3YwZnpzMzAwZTN1YWtzYWcwNXg2ZzMifQ.v6YZ9axqDwZSlzbjmMOfTg',
            {profile: profile}),
        collapsible: true,
        show: false
      });
      if (self.options.mapObj) {
        curRouteControl.addTo(self.options.mapObj);
      }
      curRouteControl.on('routesfound', function(e) {
        if (e.routes && e.routes.length > 0) {
          var summary = e.routes[0].summary;
          var totalTime = summary.totalTime / 60;
          var totalDistance = summary.totalDistance / 1000;
          $('div#display_' + suffixID).text(totalDistance.toFixed(1) + ' km, '
              + totalTime.toFixed(1) + ' mins');
        }
      });
    }

    if (!(features instanceof Array)) {
      features = this.buildFeatures(features);
    }
    for (var i = 0; i < features.length; i++) {
      var feature = features[i], layer;

      if (feature.type === "changeset") {
        layer = L.rectangle(feature.latLngBounds, this.options.styles.changeset);
      } else if (feature.type === "node") {
        layer = L.marker(feature.latLng, this.options.styles.node);
        layer.bindPopup("");
        layer._tags = feature.tags;
        layer._featureID = feature.id;
        layer.on('popupopen', function () {
            var tag = "";
            var suffix = "";
            var tags = this._tags;
            var fid = this._featureID;
            if(tags.name){
              tag = tags.name;
            }else if(tags.operator){
              tag = tags.operator;
            }
            for(var ct of self.options.categoryTags){
              if(tags.hasOwnProperty(ct)){
                if(tag){
                  suffix = " - ";
                }
                suffix += tags[ct].replace(/_/g, " ");
                break;
              }
            }
            var POILat = this.getLatLng().lat;
            var POILng = this.getLatLng().lng;
            var POIPoint = new GeoPoint(POILat, POILng);
            var distance = POIPoint.distanceTo(self.options.targetPoint, true);
            var dataOptions = 'data-options=\'{"sfxid": ' + fid + ', "coords": ['
                + self.options.targetPoint.latitude() + ','
                + self.options.targetPoint.longitude() + ','
                + POILat + ','
                + POILng + ']}\'';
            var routeConsole = {
                driveHtml: '<a id="drive_' + fid + '" data-profile="mapbox/driving" ' + dataOptions + ' class="btn btn-raised btn-info btn-xs">Drive</a>',
                walkHtml: '<a id="walk_' + fid + '" data-profile="mapbox/walking" ' + dataOptions + ' class="btn btn-raised btn-info btn-xs">Walk</a>',
                bikeHtml: '<a id="bike_' + fid + '" data-profile="mapbox/cycling" ' + dataOptions + ' class="btn btn-raised btn-info btn-xs">Bike</a>',
                display: '<div style="text-align: center" id="display_' + fid + '"></div>'};
            this.setPopupContent('<b>' + tag + suffix + '<br>'
                + routeConsole.driveHtml + routeConsole.walkHtml + routeConsole.bikeHtml + '<br>'
                + routeConsole.display);

          $('a#drive_' + fid).on('click', routing);
          $('a#walk_' + fid).on('click', routing);
          $('a#bike_' + fid).on('click', routing);
        });

      } else {
        var latLngs = new Array(feature.nodes.length);

        for (var j = 0; j < feature.nodes.length; j++) {
          latLngs[j] = feature.nodes[j].latLng;
        }

        if (this.isWayArea(feature)) {
          latLngs.pop(); // Remove last == first.
          layer = L.polygon(latLngs, this.options.styles.area);
        } else {
          layer = L.polyline(latLngs, this.options.styles.way);
        }
      }

      layer.addTo(this);
      layer.feature = feature;
    }
  },

  buildFeatures: function (xml) {
    var features = L.OSM.getChangesets(xml),
      nodes = L.OSM.getNodes(xml),
      ways = L.OSM.getWays(xml, nodes),
      relations = L.OSM.getRelations(xml, nodes, ways);

    for (var node_id in nodes) {
      var node = nodes[node_id];
      if (this.interestingNode(node, ways, relations)) {
        features.push(node);
      }
    }

    for (var i = 0; i < ways.length; i++) {
      var way = ways[i];
      features.push(way);
    }

    return features;
  },

  isWayArea: function (way) {
    if (way.nodes[0] != way.nodes[way.nodes.length - 1]) {
      return false;
    }

    for (var key in way.tags) {
      if (~this.options.areaTags.indexOf(key)) {
        return true;
      }
    }

    return false;
  },

  interestingNode: function (node, ways, relations) {
    var used = false;

    for (var i = 0; i < ways.length; i++) {
      if (ways[i].nodes.indexOf(node) >= 0) {
        used = true;
        break;
      }
    }

    if (!used) {
      return true;
    }

    for (var i = 0; i < relations.length; i++) {
      if (relations[i].members.indexOf(node) >= 0)
        return true;
    }

    for (var key in node.tags) {
      if (this.options.uninterestingTags.indexOf(key) < 0) {
        return true;
      }
    }

    return false;
  }
});

L.Util.extend(L.OSM, {
  getChangesets: function (xml) {
    var result = [];

    var nodes = xml.getElementsByTagName("changeset");
    for (var i = 0; i < nodes.length; i++) {
      var node = nodes[i], id = node.getAttribute("id");
      result.push({
        id: id,
        type: "changeset",
        latLngBounds: L.latLngBounds(
          [node.getAttribute("min_lat"), node.getAttribute("min_lon")],
          [node.getAttribute("max_lat"), node.getAttribute("max_lon")]),
        tags: this.getTags(node)
      });
    }

    return result;
  },

  getNodes: function (xml) {
    var result = {};

    var nodes = xml.getElementsByTagName("node");
    for (var i = 0; i < nodes.length; i++) {
      var node = nodes[i], id = node.getAttribute("id");
      result[id] = {
        id: id,
        type: "node",
        latLng: L.latLng(node.getAttribute("lat"),
                         node.getAttribute("lon"),
                         true),
        tags: this.getTags(node)
      };
    }

    return result;
  },

  getWays: function (xml, nodes) {
    var result = [];

    var ways = xml.getElementsByTagName("way");
    for (var i = 0; i < ways.length; i++) {
      var way = ways[i], nds = way.getElementsByTagName("nd");

      var way_object = {
        id: way.getAttribute("id"),
        type: "way",
        nodes: new Array(nds.length),
        tags: this.getTags(way)
      };

      for (var j = 0; j < nds.length; j++) {
        way_object.nodes[j] = nodes[nds[j].getAttribute("ref")];
      }

      result.push(way_object);
    }

    return result;
  },

  getRelations: function (xml, nodes, ways) {
    var result = [];

    var rels = xml.getElementsByTagName("relation");
    for (var i = 0; i < rels.length; i++) {
      var rel = rels[i], members = rel.getElementsByTagName("member");

      var rel_object = {
        id: rel.getAttribute("id"),
        type: "relation",
        members: new Array(members.length),
        tags: this.getTags(rel)
      };

      for (var j = 0; j < members.length; j++) {
        if (members[j].getAttribute("type") === "node")
          rel_object.members[j] = nodes[members[j].getAttribute("ref")];
        else // relation-way and relation-relation membership not implemented
          rel_object.members[j] = null;
      }

      result.push(rel_object);
    }

    return result;
  },

  getTags: function (xml) {
    var result = {};

    var tags = xml.getElementsByTagName("tag");
    for (var j = 0; j < tags.length; j++) {
      result[tags[j].getAttribute("k")] = tags[j].getAttribute("v");
    }

    return result;
  }
});
