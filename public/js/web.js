let baseUrl = "";
let currentUrl = "";
let currentLat = 0;
let currentLng = 0;
let userLat = 0;
let userLng = 0;
let web, map;
let infoWindow = new google.maps.InfoWindow();
let userInfoWindow = new google.maps.InfoWindow();
let directionsService, directionsRenderer;
let userMarker = new google.maps.Marker();
let destinationMarker = new google.maps.Marker();
let routeArray = [];
let circleArray = [];
let markerArray = {};
let bounds = new google.maps.LatLngBounds();
let selectedShape,
  drawingManager = new google.maps.drawing.DrawingManager();
let geomAreaArray = [];
let routePoints = []; // to store route points
let customStyled = [
  {
    elementType: "labels",
    stylers: [
      {
        visibility: "off",
      },
    ],
  },
  {
    featureType: "administrative.land_parcel",
    stylers: [
      {
        visibility: "off",
      },
    ],
  },
  {
    featureType: "administrative.neighborhood",
    stylers: [
      {
        visibility: "off",
      },
    ],
  },
  {
    featureType: "road",
    elementType: "labels",
    stylers: [
      {
        visibility: "on",
      },
    ],
  },
];

function setBaseUrl(url) {
  baseUrl = url;
}

// Initialize and add the map

function initMap(lat = -1.483815, lng = 101.05896, mobile = false) {
  directionsService = new google.maps.DirectionsService();
  const center = new google.maps.LatLng(lat, lng);
  if (!mobile) {
    map = new google.maps.Map(document.getElementById("googlemaps"), {
      zoom: 7,
      center: center,
      mapTypeId: "roadmap",
    });
  } else {
    map = new google.maps.Map(document.getElementById("googlemaps"), {
      zoom: 18,
      center: center,
      mapTypeControl: false,
    });
  }
  var rendererOptions = {
    map: map,
  };
  map.set("styles", customStyled);
  directionsRenderer = new google.maps.DirectionsRenderer(rendererOptions);
  digitVillage();
  addAreaPolygon(sumbarGeom);
}

// Display tourism village digitizing
function digitVillage() {
  const village = new google.maps.Data();
  $.ajax({
    url: baseUrl + "/api/village",
    type: "POST",
    data: {
      village: "1",
    },
    dataType: "json",
    success: function (response) {
      const data = response.data;

      village.addGeoJson(data);
      village.setStyle({
        fillColor: "#00b300",
        strokeWeight: 0.5,
        strokeColor: "#005000",
        fillOpacity: 0.1,
        clickable: false,
      });
      village.setMap(map);
    },
  });
}
function digitRegion() {
  const a = { type: "Feature", geometry: regionData };
  const region = new google.maps.Data();
  region.addGeoJson(a);
  region.setStyle({
    fillColor: "#00b300",
    strokeWeight: 0.5,
    strokeColor: "#005000",
    fillOpacity: 0.1,
    clickable: false,
  });
  region.setMap(map);
}
// Construct the polygon.
function addAreaPolygon(geoJson, color, opacity) {
  clearAreaGeom();
  // Load GeoJSON.
  map.data.addGeoJson(geoJson);
  map.data.setStyle({
    fillColor: "#00b300",
    strokeWeight: 0.5,
    strokeColor: color,
    fillOpacity: 0.1,
    clickable: false,
  });
  var bounds = new google.maps.LatLngBounds();

  // Loop through features
  map.data.forEach(function (feature) {
    var geo = feature.getGeometry();

    geo.forEachLatLng(function (LatLng) {
      bounds.extend(LatLng);
    });
  });
  map.fitBounds(bounds);
}

// clear pariangan geom on map
function clearAreaGeom() {
  if (map.data) {
    map.data.forEach(function (feature) {
      map.data.remove(feature);
    });
  }
}

// move camera
function moveCamera(z = 17) {
  map.moveCamera({ zoom: z });
}

// Remove user location
function clearUser() {
  userLat = 0;
  userLng = 0;
  userMarker.setMap(null);
}

// Set current location based on user location
function setUserLoc(lat, lng) {
  userLat = lat;
  userLng = lng;
  currentLat = userLat;
  currentLng = userLng;
}

// Remove any route shown
function clearRoute() {
  for (i in routeArray) {
    routeArray[i].setMap(null);
  }
  routeArray = [];
  $("#direction-row").hide();
}

function clearRouteAll() {
  for (let i = 0; i < routeArray.length; i++) {
    if (routeArray[i]) {
      routeArray[i].setMap(null);
    }
  }
  routeArray = [];
  routePoints = []; // Pastikan ini benar-benar reset
  $("#direction-row").hide();
}

// Remove any radius shown
function clearRadius() {
  for (i in circleArray) {
    circleArray[i].setMap(null);
  }
  circleArray = [];
}

// Remove any marker shown
function clearMarker() {
  for (i in markerArray) {
    markerArray[i].setMap(null);
  }
  markerArray = {};
}

// Get user's current position
function currentPosition() {
  clearRadius();
  clearRoute();

  google.maps.event.clearListeners(map, "click");
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        infoWindow.close();
        clearUser();
        markerOption = {
          position: pos,
          animation: google.maps.Animation.DROP,
          map: map,
        };
        userMarker.setOptions(markerOption);
        userInfoWindow.setContent(
          "<p class='text-center'><span class='fw-bold'>You are here.</span> <br> lat: " +
            pos.lat +
            "<br>long: " +
            pos.lng +
            "</p>"
        );
        userInfoWindow.open(map, userMarker);
        map.setCenter(pos);
        setUserLoc(pos.lat, pos.lng);

        userMarker.addListener("click", () => {
          userInfoWindow.open(map, userMarker);
        });
      },
      () => {
        handleLocationError(true, userInfoWindow, map.getCenter());
      }
    );
  } else {
    // Browser doesn't support Geolocation
    handleLocationError(false, userInfoWindow, map.getCenter());
  }
}

// Error handler for geolocation
function handleLocationError(browserHasGeolocation, infoWindow, pos) {
  infoWindow.setPosition(pos);
  infoWindow.setContent(
    browserHasGeolocation
      ? "Error: The Geolocation service failed."
      : "Error: Your browser doesn't support geolocation."
  );
  infoWindow.open(map);
}

// User set position on map
function manualPosition() {
  clearRadius();
  clearRoute();

  if (userLat == 0 && userLng == 0) {
    Swal.fire("Click on Map");
  }
  map.addListener("click", (mapsMouseEvent) => {
    infoWindow.close();
    pos = mapsMouseEvent.latLng;

    clearUser();
    markerOption = {
      position: pos,
      animation: google.maps.Animation.DROP,
      map: map,
    };
    userMarker.setOptions(markerOption);
    userInfoWindow.setContent(
      "<p class='text-center'><span class='fw-bold'>You are here.</span> <br> lat: " +
        pos.lat().toFixed(8) +
        "<br>long: " +
        pos.lng().toFixed(8) +
        "</p>"
    );
    userInfoWindow.open(map, userMarker);

    userMarker.addListener("click", () => {
      userInfoWindow.open(map, userMarker);
    });

    setUserLoc(pos.lat().toFixed(8), pos.lng().toFixed(8));
  });
}

// Render route on selected object
function routeTo(lat, lng, routeFromUser = true) {
  clearRadius();
  clearRoute();
  google.maps.event.clearListeners(map, "click");

  let start, end;
  if (routeFromUser) {
    if (userLat == 0 && userLng == 0) {
      return Swal.fire("Determine your position first!");
    }
    setUserLoc(userLat, userLng);
  }
  start = new google.maps.LatLng(currentLat, currentLng);
  end = new google.maps.LatLng(lat, lng);
  let request = {
    origin: start,
    destination: end,
    travelMode: "DRIVING",
  };
  directionsService.route(request, function (result, status) {
    if (status == "OK") {
      directionsRenderer.setDirections(result);
      showSteps(result);
      directionsRenderer.setMap(map);
      routeArray.push(directionsRenderer);
    }
  });
  boundToRoute(start, end);
}

function routeAll(lat, lng, routeFromUser = true) {
  console.log("routeAll");

  if (routeFromUser && routePoints.length === 0) {
    if (userLat == 0 && userLng == 0) {
      return Swal.fire("Determine your position first!");
    }
    setUserLoc(userLat, userLng);
    routePoints.push(new google.maps.LatLng(userLat, userLng));
  }

  routePoints.push(new google.maps.LatLng(lat, lng));

  if (routePoints.length < 2) {
    return;
  }

  const origin = routePoints[0];
  const destination = routePoints[routePoints.length - 1];
  const waypoints = routePoints
    .slice(1, -1)
    .map((point) => ({ location: point, stopover: true }));

  let request = {
    origin: origin,
    destination: destination,
    travelMode: google.maps.TravelMode.DRIVING,
    waypoints: waypoints,
    optimizeWaypoints: false,
  };

  directionsService.route(request, function (result, status) {
    if (status === google.maps.DirectionsStatus.OK) {
      if (!directionsRenderer) {
        directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);
      }
      directionsRenderer.setDirections(result);
      showSteps(result);

      // Kalau kamu ingin simpan setiap directionsRenderer terpisah (tidak perlu sebenarnya),
      // kamu bisa push directionsRenderer ke routeArray, tapi biasanya cukup 1 saja.
      // routeArray.push(directionsRenderer);
    } else {
      console.error("Directions request failed due to " + status);
    }
  });
}
// Display marker for loaded object
function objectMarker(id, lat, lng, anim = true) {
  google.maps.event.clearListeners(map, "click");
  let pos = new google.maps.LatLng(lat, lng);
  let marker = new google.maps.Marker();

  let icon;
  if (id.substring(0, 1) === "R") {
    icon = baseUrl + "/media/icon/marker_rg.png";
  } else if (id.substring(0, 1) === "C") {
    icon = baseUrl + "/media/icon/marker_cp.png";
  } else if (id.substring(0, 1) === "W") {
    icon = baseUrl + "/media/icon/marker_wp.png";
  } else if (id.substring(0, 1) === "S") {
    icon = baseUrl + "/media/icon/marker_sp.png";
  } else if (id.substring(0, 1) === "E") {
    icon = baseUrl + "/media/icon/marker_ev.png";
  } else if (id.substring(0, 1) === "A") {
    icon = baseUrl + "/media/icon/marker_cp.png";
  }

  markerOption = {
    position: pos,
    icon: icon,
    animation: google.maps.Animation.DROP,
    map: map,
  };
  marker.setOptions(markerOption);
  if (!anim) {
    marker.setAnimation(null);
  }
  marker.addListener("click", () => {
    infoWindow.close();
    objectInfoWindow(id);
    infoWindow.open(map, marker);
  });
  markerArray[id] = marker;
}

// Display markers for loaded objects from package
function objectPackageDetail(objectData) {
  objectData.forEach((element) => {
    let id = element["id"];
    let lat = element["lat"];
    let lng = element["lng"];

    objectMarker(id, lat, lng, (anim = false));
  });
}

// Display info window for loaded object
function objectInfoWindow(id) {
  let content = "";
  let contentButton = "";
  let contentMobile = "";

  if (id.substring(0, 1) === "R") {
    $.ajax({
      url: baseUrl + "/api/rumahGadang/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let rgid = data.id;
        let name = data.name;
        let lat = data.lat;
        let lng = data.lng;
        let ticket_price =
          data.ticket_price == 0 ? "Free" : "Rp " + data.ticket_price;
        let open = data.open.substring(0, data.open.length - 3);
        let close = data.close.substring(0, data.close.length - 3);

        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p>" +
          '<p><i class="fa-solid fa-clock me-2"></i> ' +
          open +
          " - " +
          close +
          " WIB</p>" +
          '<p><i class="fa-solid fa-money-bill me-2"></i> ' +
          ticket_price +
          "</p>" +
          "</div>";
        contentButton =
          '<br><div class="text-center">' +
          '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-road"></i></a>' +
          '<a title="Info" class="btn icon btn-outline-primary mx-1" target="_blank" id="infoInfoWindow" href=' +
          baseUrl +
          "/web/rumahGadang/" +
          rgid +
          '><i class="fa-solid fa-info"></i></a>' +
          '<a title="Nearby" class="btn icon btn-outline-primary mx-1" id="nearbyInfoWindow" onclick="openNearby(`' +
          rgid +
          "`," +
          lat +
          "," +
          lng +
          ')"><i class="fa-solid fa-compass"></i></a>' +
          "</div>";
        contentMobile =
          '<div class="text-center">' +
          '<a title="Route" class="btn icon btn-outline-primary mx-1 btn-sm" id="routeInfoWindow" onclick="route(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-road"></i></a>' +
          '<div class="text-center">' +
          '<a title="Route" class="btn icon btn-success mx-1 btn-sm" id="routeInfoWindow" onclick="routeAll(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-plus"></i></a>' +
          '<a title="Nearby" class="btn icon btn-outline-primary mx-1" id="nearbyInfoWindow" onclick="openNearbyMobile(`' +
          rgid +
          "`," +
          lat +
          "," +
          lng +
          ')"><i class="fa-solid fa-compass"></i></a>' +
          "</div>";

        contentHomestay = "";
        if (data.homestay_id) {
          contentHomestay =
            '<div class="text-center">' +
            `<a title="booking" class="btn icon btn-success mx-1 mt-1 btn-sm" onclick="showReservationModal('${data.id}')" data-bs-toggle="modal" data-bs-target="#reservationModal">
    <i class="fa fa-ticket"></i> booking
</a>` +
            "</div>";
        }

        if (currentUrl.includes(id)) {
          if (currentUrl.includes("mobile")) {
            infoWindow.setContent(content + contentMobile + contentHomestay);
          } else {
            infoWindow.setContent(content);
          }
          infoWindow.open(map, markerArray[rgid]);
        } else {
          infoWindow.setContent(content + contentButton);
        }
      },
    });
  } else if (id.substring(0, 1) === "U") {
    $.ajax({
      url: baseUrl + "/api/uniquePlace/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let upid = data.id;
        let name = data.name;
        let lat = data.lat;
        let lng = data.lng;

        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p> <br>" +
          "</div>";
        contentButton =
          '<br><div class="text-center">' +
          '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-road"></i></a>' +
          '<a title="Info" class="btn icon btn-outline-primary mx-1" target="_blank" id="infoInfoWindow" href=' +
          baseUrl +
          "/web/uniquePlace/" +
          upid +
          '><i class="fa-solid fa-info"></i></a>' +
          '<a title="Nearby" class="btn icon btn-outline-primary mx-1" id="nearbyInfoWindow" onclick="openNearby(`' +
          upid +
          "`," +
          lat +
          "," +
          lng +
          ')"><i class="fa-solid fa-compass"></i></a>' +
          "</div>";
        contentMobile =
          '<br><div class="text-center">' +
          '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-road"></i></a>' +
          "</div>";

        if (currentUrl.includes(id)) {
          if (currentUrl.includes("mobile")) {
            infoWindow.setContent(content + contentMobile);
          } else {
            infoWindow.setContent(content);
          }
          infoWindow.open(map, markerArray[upid]);
        } else {
          infoWindow.setContent(content + contentButton);
        }
      },
    });
  } else if (id.substring(0, 1) === "E") {
    const months = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    $.ajax({
      url: baseUrl + "/api/event/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let evid = data.id;
        let name = data.name;
        let lat = data.lat;
        let lng = data.lng;
        let ticket_price =
          data.ticket_price == 0 ? "Free" : "Rp " + data.ticket_price;
        let category = data.category;
        let date_next = new Date(data.date_start);
        let next =
          date_next.getDate() +
          " " +
          months[date_next.getMonth()] +
          " " +
          date_next.getFullYear();

        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p> <br>" +
          '<p><i class="fa-solid fa-layer-group me-2"></i> ' +
          category +
          "</p>" +
          '<p><i class="fa-solid fa-money-bill me-2"></i> ' +
          ticket_price +
          "</p>" +
          '<p><i class="fa-solid fa-calendar-days me-2"></i> ' +
          next +
          "</p>" +
          "</div>";
        contentButton =
          '<br><div class="text-center">' +
          '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-road"></i></a>' +
          '<a title="Info" class="btn icon btn-outline-primary mx-1" target="_blank" id="infoInfoWindow" href=' +
          baseUrl +
          "/web/event/" +
          evid +
          '><i class="fa-solid fa-info"></i></a>' +
          '<a title="Nearby" class="btn icon btn-outline-primary mx-1" id="nearbyInfoWindow" onclick="openNearby(`' +
          evid +
          "`," +
          lat +
          "," +
          lng +
          ')"><i class="fa-solid fa-compass"></i></a>' +
          "</div>";
        contentMobile =
          '<br><div class="text-center">' +
          '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-road"></i></a>' +
          "</div>";

        if (currentUrl.includes(id)) {
          if (currentUrl.includes("mobile")) {
            infoWindow.setContent(content + contentMobile);
          } else {
            infoWindow.setContent(content);
          }
          infoWindow.open(map, markerArray[evid]);
        } else {
          infoWindow.setContent(content + contentButton);
        }
      },
    });
  } else if (id.substring(0, 1) === "C") {
    $.ajax({
      url: baseUrl + "/api/culinaryPlace/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let name = data.name;
        let lat = data.lat;
        let lng = data.lng;
        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p>" +
          "</div>" +
          '<div class="text-center">' +
          '<a title="Route" class="btn icon btn-success mx-1 btn-sm" id="routeInfoWindow" onclick="routeAll(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-plus"></i></a>';

        infoWindow.setContent(content);
      },
    });
  } else if (id.substring(0, 1) === "W") {
    $.ajax({
      url: baseUrl + "/api/worshipPlace/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let name = data.name;
        let lat = data.lat;
        let lng = data.lng;
        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p>" +
          "</div>" +
          '<div class="text-center">' +
          '<a title="Route" class="btn icon btn-success mx-1 btn-sm" id="routeInfoWindow" onclick="routeAll(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-plus"></i></a>';

        infoWindow.setContent(content);
      },
    });
  } else if (id.substring(0, 1) === "S") {
    $.ajax({
      url: baseUrl + "/api/souvenirPlace/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let name = data.name;
        let lat = data.lat;
        let lng = data.lng;
        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p>" +
          "</div>" +
          '<div class="text-center">' +
          '<a title="Route" class="btn icon btn-success mx-1 btn-sm" id="routeInfoWindow" onclick="routeAll(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-plus"></i></a>';

        infoWindow.setContent(content);
      },
    });
  } else if (id.substring(0, 1) === "A") {
    $.ajax({
      url: baseUrl + "/api/atraction/" + id,
      dataType: "json",
      success: function (response) {
        let data = response.data;
        let name = data.name;
        let lat = data.lat;
        let lng = data.lng;
        content =
          '<div class="text-center">' +
          '<p class="fw-bold fs-6">' +
          name +
          "</p>" +
          "</div>" +
          '<div class="text-center">' +
          '<a title="Route" class="btn icon btn-success mx-1 btn-sm" id="routeInfoWindow" onclick="routeAll(' +
          lat +
          ", " +
          lng +
          ')"><i class="fa-solid fa-plus"></i></a>';

        infoWindow.setContent(content);
      },
      error: function (err) {
        console.log(err.responseText);
      },
    });
  }
}

// Render map to contains all object marker
function boundToObject(firstTime = true) {
  if (Object.keys(markerArray).length > 0) {
    bounds = new google.maps.LatLngBounds();
    for (i in markerArray) {
      bounds.extend(markerArray[i].getPosition());
    }
    if (firstTime) {
      map.fitBounds(bounds, 80);
    } else {
      map.panTo(bounds.getCenter());
    }
  } else {
    let pos = new google.maps.LatLng(-0.5242972, 100.492333);
    map.panTo(pos);
  }
}

// Render map to contains route and its markers
function boundToRoute(start, end) {
  bounds = new google.maps.LatLngBounds();
  bounds.extend(start);
  bounds.extend(end);
  map.panToBounds(bounds, 100);
}

// Add user position to map bound
function boundToRadius(lat, lng, rad) {
  let userBound = new google.maps.LatLng(lat, lng);
  const radiusCircle = new google.maps.Circle({
    center: userBound,
    radius: Number(rad),
  });
  map.fitBounds(radiusCircle.getBounds());
}

// Draw radius circle
function drawRadius(position, radius) {
  const radiusCircle = new google.maps.Circle({
    center: position,
    radius: radius,
    map: map,
    strokeColor: "#FF0000",
    strokeOpacity: 0.8,
    strokeWeight: 2,
    fillColor: "#FF0000",
    fillOpacity: 0.35,
  });
  circleArray.push(radiusCircle);
  boundToRadius(currentLat, currentLng, radius);
}

// Update radiusValue on search by radius
function updateRadius(postfix) {
  console.log(postfix);
  document.getElementById("radiusValue" + postfix).innerHTML =
    document.getElementById("inputRadius" + postfix).value * 100 + " m";
}

function updateRadiusMobile() {
  document.getElementById("radiusValueNearbyMobile").innerHTML =
    document.getElementById("inputRadiusNearbyMobile").value * 100 + " m";
}

// Render search by radius
function radiusSearch({ postfix = null } = {}) {
  if (userLat == 0 && userLng == 0) {
    document.getElementById("radiusValue" + postfix).innerHTML = "0 m";
    document.getElementById("inputRadius" + postfix).value = 0;
    return Swal.fire("Determine your position first!");
  }

  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");
  closeNearby();

  let pos = new google.maps.LatLng(currentLat, currentLng);
  let radiusValue =
    parseFloat(document.getElementById("inputRadius" + postfix).value) * 100;
  map.panTo(pos);

  // find object in radius
  if (postfix === "RG") {
    $.ajax({
      url: baseUrl + "/api/rumahGadang/findByRadius",
      type: "POST",
      data: {
        lat: currentLat,
        long: currentLng,
        radius: radiusValue,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        drawRadius(pos, radiusValue);
      },
    });
  } else if (postfix === "EV") {
    $.ajax({
      url: baseUrl + "/api/event/findByRadius",
      type: "POST",
      data: {
        lat: currentLat,
        long: currentLng,
        radius: radiusValue,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        drawRadius(pos, radiusValue);
      },
    });
  } else if (postfix === "UP") {
    $.ajax({
      url: baseUrl + "/api/uniquePlace/findByRadius",
      type: "POST",
      data: {
        lat: currentLat,
        long: currentLng,
        radius: radiusValue,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        drawRadius(pos, radiusValue);
      },
    });
  }
}

// pan to selected object
function focusObject(id) {
  google.maps.event.trigger(markerArray[id], "click");
  map.panTo(markerArray[id].getPosition());
}

// display objects by feature used
function displayFoundObject(response) {
  $("#table-data").empty();
  let data = response.data;
  let counter = 1;
  const months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  for (i in data) {
    let item = data[i];
    let row;
    if (item.hasOwnProperty("date_next")) {
      let date_next = new Date(item.date_start);
      let next =
        date_next.getDate() +
        " " +
        months[date_next.getMonth()] +
        " " +
        date_next.getFullYear();
      row =
        "<tr>" +
        "<td>" +
        counter +
        "</td>" +
        '<td class="fw-bold">' +
        item.name +
        '<br><span class="text-muted">' +
        next +
        "</span></td>" +
        "<td>" +
        '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Info" class="btn icon btn-primary mx-1" onclick="focusObject(`' +
        item.id +
        '`);">' +
        '<span class="material-symbols-outlined">info</span>' +
        "</a>" +
        "</td>" +
        "</tr>";
    } else {
      row =
        "<tr>" +
        "<td>" +
        counter +
        "</td>" +
        '<td class="fw-bold">' +
        item.name +
        "</td>" +
        "<td>" +
        '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Info" class="btn icon btn-primary mx-1" onclick="focusObject(`' +
        item.id +
        '`);">' +
        '<span class="material-symbols-outlined">info</span>' +
        "</a>" +
        "</td>" +
        "</tr>";
    }
    $("#table-data").append(row);
    objectMarker(item.id, item.lat, item.lng);
    counter++;
  }
}

// reservation modal
function showReservationModal(rgId = null) {
  let userId = UserIdManager.getUserIdFromSessionStorage();
  console.log(userId);
  let homestayData = {};
  $.ajax({
    url: baseUrl + "/api/rumahGadang/" + rgId,
    dataType: "json",
    async: false,
    success: function (response) {
      homestayData = response.data;
    },
    error: function (err) {
      console.error(err);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Failed to load homestay data.",
      });
    },
  });
  if (userId) {
    const avgRating = 0;
    if (homestayData.avg_rating) {
      avgRating = parseInt(homestayData.avg_rating);
    }

    const galleryItems = homestayData.homestayGalleries || [];
    const homestayName = homestayData.homestay_name || "";
    const homestayId = homestayData.homestay_id || null;
    const homestayFullName = homestayData?.homestay_name || "";
    const status = homestayData?.status === 1 ? "available" : "not available";
    const price = homestayData?.homestay_price || 0;
    const formattedPrice = price.toLocaleString("id-ID");
    const address = homestayData?.homestay_address || "";
    const checkin = homestayData?.homestay_checkin || "";
    const checkout = homestayData?.homestay_checkout || "";
    const contactPerson = homestayData?.homestay_contact_person || "";

    // Rating stars
    let starsHtml = "";
    for (let i = 0; i < avgRating; i++) {
      starsHtml +=
        '<span class="material-symbols-outlined rating-color">star</span>';
    }
    for (let i = 0; i < 5 - avgRating; i++) {
      starsHtml += '<span class="material-symbols-outlined">star</span>';
    }

    // Carousel
    let carouselIndicators = "";
    let carouselInner = "";
    galleryItems.forEach((gallery, index) => {
      carouselIndicators += `
                <li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="${index}" class="${
        index === 0 ? "active" : ""
      }"></li>
            `;
      carouselInner += `
                <div class="carousel-item${index === 0 ? " active" : ""}">
                    <img src="/media/photos/homestay/${
                      gallery.url
                    }" class="d-block w-100" alt="${gallery.url}">
                </div>
            `;
    });

    let carouselHtml = "";
    if (galleryItems.length > 0) {
      carouselHtml = `
                <tr>
                    <td colspan="2">
                        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                ${carouselIndicators}
                            </ol>
                            <div class="carousel-inner">
                                ${carouselInner}
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
    }

    // Modal content
    $("#modalTitle").html("Reservation form");
    $("#modalBody").html(`
            <div class="p-2">
                <div class="mb-2 shadow-sm p-4 rounded">
                    <p class="text-center fw-bold text-dark">Homestay Informations</p>
                    <div class="text-center">${starsHtml}</div>
                    <table class="table table-borderless text-dark">
                        <tbody>
                            ${carouselHtml}
                            <tr>
                                <td class="fw-bold">Homestay Name</td>
                                <td>${homestayName} (${homestayFullName})</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status</td>
                                <td>${status}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Price / day</td>
                                <td>Rp ${formattedPrice}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Address</td>
                                <td>${address}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Check in Time</td>
                                <td>${checkin} AM</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Check out Time</td>
                                <td>${checkout} AM (Next day)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Contact Person</td>
                                <td>${contactPerson}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="shadow p-4 rounded">
                    <div class="form-group mb-2">
                        <label for="reservation_date" class="mb-2">Reservation date</label>
                        <input onchange="changeMinDate(this.value)" type="text" id="reservation_date" class="form-control" required>
                    </div>
                    <div class="form-group mb-2" id="reservation_date_end_container"></div>
                    <div class="form-group mb-2">
                        <label for="number_people" class="mb-2">Number of people</label>
                        <input type="number" id="number_people" value="0" class="form-control" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="comment" class="mb-2">Additional information</label>
                        <input type="text" id="comment" class="form-control">
                    </div>
                </div>
            </div>
        `);

    // Setup datepicker
    let dateNow = new Date();
    $("#reservation_date").datepicker({
      format: "yyyy-mm-dd",
      autoclose: true,
      startDate: new Date(
        dateNow.getFullYear(),
        dateNow.getMonth(),
        dateNow.getDate() + 1
      ),
      todayHighlight: false,
    });

    $("#modalFooter").html(
      `<a class="btn btn-success" onclick="makeReservation('${userId}','${homestayId}',${price})"> Make reservation </a>`
    );
  } else {
    $("#modalTitle").html("Login required");
    $("#modalBody").html("Login as user for reservation");
  }
}

function changeMinDate(value) {
  let valueDate = new Date(value);
  let maxDate = new Date(
    valueDate.getFullYear(),
    valueDate.getMonth(),
    valueDate.getDate() + 3
  );
  $("#reservation_date_end_container").html(`
        <label for="reservation_date_end" class="mb-2"> Until <span class="text-sm text-primary"> ( Max 4 days ) </span> </label>
        <input type="text" id="reservation_date_end" class="form-control" required >`);

  let dateNow = new Date();
  let yearNow = dateNow.getFullYear();
  let nextYear = yearNow + 2;
  let yearRange = `${yearNow}:${nextYear}`;
  // date picker
  // let datesForDisable = ["2023-11-08", "2023-11-09", "2023-11-10", "2023-11-11"]

  $("#reservation_date_end").datepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    yearRange: yearRange,
    startDate: valueDate,
    endDate: maxDate,
    defaultViewDate: valueDate,
    // datesDisabled: datesForDisable
  });
}

function makeReservation(user_id, homestay_id, price) {
  let reservationDate = $("#reservation_date").val();
  let reservationDateEnd = $("#reservation_date_end").val();

  let startDate = new Date(reservationDate).getDate();
  let endDate = new Date(reservationDateEnd).getDate();
  let countDate = endDate - startDate + 1;

  let numberPeople = $("#number_people").val();
  let comment = $("#comment").val();

  let dateCheckResult = checkIsDateExpired(reservationDate);
  let sameDateCheckResult = "true";

  if (reservationDate) {
    sameDateCheckResult = checkIsDateDuplicate(
      user_id,
      reservationDate,
      reservationDateEnd
    );
  }

  if (!reservationDate) {
    Swal.fire("Please select reservation date", "", "warning");
  } else if (numberPeople <= 0) {
    Swal.fire("Need 1 people at least", "", "warning");
  } else if (dateCheckResult === false) {
    Swal.fire(
      "Cannot reserve, out of date. Maximum is H-1 before reservation",
      "",
      "warning"
    );
  } else if (sameDateCheckResult === "true") {
    Swal.fire("The date is booked, please select another date", "", "warning");
  } else {
    if (user_id) {
      let ticketPrice = parseInt(price || 0);
      let homestayId = homestay_id;

      let totalPrice = ticketPrice * countDate;

      let requestData = {
        reservation_date: reservationDate,
        reservation_date_end: reservationDateEnd,
        id_user: user_id,
        id_homestay: homestayId,
        id_reservation_status: 1, // Pending status
        number_people: numberPeople,
        total_price: totalPrice,
        comment: comment,
      };

      $.ajax({
        url: "/web/reservation/create",
        type: "POST",
        contentType: "application/json",
        data: requestData,
        async: false,
        contentType: "application/json",
        success: function (response) {
          Swal.fire("Success to make reservation request", "", "success");
        },
        error: function (err) {
          console.log(err.responseText);
        },
      });
    }
  }
}

function checkIsDateExpired(reservation_date) {
  let result;

  let today = new Date();
  let dd = String(today.getDate()).padStart(2, "0");
  let mm = String(today.getMonth() + 1).padStart(2, "0"); //January is 0!
  let yyyy = today.getFullYear();

  today = yyyy + "-" + mm + "-" + dd;

  if (reservation_date > today) {
    result = true;
  } else {
    result = false;
  }
  return result;
}

function checkIsDateDuplicate(user_id, reservation_date, reservation_date_end) {
  let result;
  $.ajax({
    url: `/web/reservation/checkHomestay/${user_id}/${reservation_date}/${reservation_date_end}`,
    type: "GET",
    async: false,
    success: function (response) {
      result = response;
    },
    error: function (err) {
      console.log(err.responseText);
    },
  });
  return result;
}
// end reservation modal

// display steps of direction to selected route
function showSteps(directionResult) {
  $("#direction-row").show();
  $("#table-direction").empty();
  let myRoute = directionResult.routes[0].legs[0];
  for (let i = 0; i < myRoute.steps.length; i++) {
    let distance = myRoute.steps[i].distance.value;
    let instruction = myRoute.steps[i].instructions;
    let row =
      "<tr>" +
      "<td>" +
      distance.toLocaleString("id-ID") +
      "</td>" +
      "<td>" +
      instruction +
      "</td>" +
      "</tr>";
    $("#table-direction").append(row);
  }
}

// close nearby search section
function closeNearby() {
  $("#direction-row").hide();
  $("#check-nearby-col").hide();
  $("#result-nearby-col").hide();
  $("#list-rec-col").show();
  $("#list-rg-col").show();
  $("#list-ev-col").show();
  $("#list-up-col").show();
}

// open nearby search section
function openNearbyMobile(id, lat, lng) {
  $("#check-nearby-col").show();
  currentLat = lat;
  currentLng = lng;
  let pos = new google.maps.LatLng(currentLat, currentLng);
  map.panTo(pos);

  setUserLoc(currentLat, currentLng);
  console.log("openNearbyMobile", currentLat, currentLng);
  document
    .getElementById("inputRadiusNearbyMobile")
    .setAttribute(
      "onchange",
      'updateRadiusMobile(); checkNearbyMobile("' + id + '")'
    );
}

// open nearby search section
function openNearby(id, lat, lng) {
  $("#list-rg-col").hide();
  $("#list-ev-col").hide();
  $("#list-up-col").hide();
  $("#list-rec-col").hide();
  $("#check-nearby-col").show();

  currentLat = lat;
  currentLng = lng;
  let pos = new google.maps.LatLng(currentLat, currentLng);
  map.panTo(pos);
  setUserLoc(currentLat, currentLng);
  console.log("openNearby", currentLat, currentLng);
  document
    .getElementById("inputRadiusNearby")
    .setAttribute(
      "onchange",
      'updateRadius("Nearby"); checkNearby("' + id + '")'
    );
}
// Search Result Object Around
function checkNearbyMobile(id) {
  console.log("checkNearbyMobile", id, currentLat, currentLng);
  clearRadius();
  clearRoute();
  // clearRouteAll();
  clearMarker();
  // clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  objectMarker(id, currentLat, currentLng, false);

  const inputRadius = document.getElementById("inputRadiusNearbyMobile");
  if (!inputRadius) {
    Swal.fire("Input radius tidak ditemukan!", "", "error");
    return;
  }

  let radiusValue = parseFloat(inputRadius.value) * 100;
  findNearby("cp", radiusValue);
  findNearby("wp", radiusValue);
  findNearby("sp", radiusValue);
  findNearby("at", radiusValue);
  drawRadius(new google.maps.LatLng(currentLat, currentLng), radiusValue);
  $("#result-nearby-col").show();
}
function checkNearby(id) {
  console.log("checkNearby", id, currentLat, currentLng);
  clearRadius();
  clearRoute();
  clearMarker();
  // clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  objectMarker(id, currentLat, currentLng, false);

  $("#table-cp").empty();
  $("#table-wp").empty();
  $("#table-sp").empty();
  $("#table-at").empty();
  $("#table-cp").hide();
  $("#table-wp").hide();
  $("#table-sp").hide();
  $("#table-at").hide();

  let radiusValue =
    parseFloat(document.getElementById("inputRadiusNearby").value) * 100;
  const checkCP = document.getElementById("check-cp").checked;
  const checkWP = document.getElementById("check-wp").checked;
  const checkSP = document.getElementById("check-sp").checked;
  const checkAT = document.getElementById("check-at").checked;

  if (!checkCP && !checkWP && !checkSP && !checkAT) {
    document.getElementById("radiusValueNearby").innerHTML = "0 m";
    document.getElementById("inputRadiusNearby").value = 0;
    return Swal.fire("Please choose one object");
  }

  if (checkCP) {
    findNearby("cp", radiusValue);
    $("#table-cp").show();
  }
  if (checkWP) {
    findNearby("wp", radiusValue);
    $("#table-wp").show();
  }
  if (checkSP) {
    findNearby("sp", radiusValue);
    $("#table-sp").show();
  }
  if (checkAT) {
    findNearby("at", radiusValue);
    $("#table-at").show();
  }
  drawRadius(new google.maps.LatLng(currentLat, currentLng), radiusValue);
  $("#result-nearby-col").show();
}

// Fetch object nearby by category
function findNearby(category, radius) {
  let pos = new google.maps.LatLng(currentLat, currentLng);
  if (category === "cp") {
    $.ajax({
      url: baseUrl + "/api/culinaryPlace/findByRadius",
      type: "POST",
      data: {
        lat: currentLat,
        long: currentLng,
        radius: radius,
      },
      dataType: "json",
      success: function (response) {
        displayNearbyResult(category, response);
      },
    });
  } else if (category === "wp") {
    $.ajax({
      url: baseUrl + "/api/worshipPlace/findByRadius",
      type: "POST",
      data: {
        lat: currentLat,
        long: currentLng,
        radius: radius,
      },
      dataType: "json",
      success: function (response) {
        displayNearbyResult(category, response);
      },
    });
  } else if (category === "sp") {
    $.ajax({
      url: baseUrl + "/api/souvenirPlace/findByRadius",
      type: "POST",
      data: {
        lat: currentLat,
        long: currentLng,
        radius: radius,
      },
      dataType: "json",
      success: function (response) {
        displayNearbyResult(category, response);
      },
    });
  } else if (category === "at") {
    $.ajax({
      url: baseUrl + "/api/atraction/findByRadius",
      type: "POST",
      data: {
        lat: currentLat,
        long: currentLng,
        radius: radius,
      },
      dataType: "json",
      success: function (response) {
        displayNearbyResult(category, response);
      },
    });
  }
}

// Add nearby object to corresponding table
function displayNearbyResult(category, response) {
  let data = response.data;
  let headerName;
  if (category === "cp") {
    headerName = "Culinary";
  } else if (category === "wp") {
    headerName = "Worship";
  } else if (category === "sp") {
    headerName = "Souvenir";
  } else if (category === "at") {
    headerName = "Atraction";
  }
  let table =
    "<thead><tr>" +
    "<th>" +
    headerName +
    " Name</th>" +
    "<th>Action</th>" +
    "</tr></thead>" +
    '<tbody id="data-' +
    category +
    '">' +
    "</tbody>";
  $("#table-" + category).append(table);

  for (i in data) {
    let item = data[i];
    let row =
      "<tr>" +
      '<td class="fw-bold">' +
      item.name +
      "</td>" +
      "<td>" +
      '<a title="Route" class="btn icon btn-primary mx-1" onclick="routeTo(' +
      item.lat +
      ", " +
      item.lng +
      ', false)"><i class="fa-solid fa-road"></i></a>' +
      '<a title="Info" class="btn icon btn-primary mx-1" onclick="infoModal(`' +
      item.id +
      '`)"><i class="fa-solid fa-info"></i></a>' +
      '<a title="Location" class="btn icon btn-primary mx-1" onclick="focusObject(`' +
      item.id +
      '`);"><i class="fa-solid fa-location-dot"></i></a>' +
      "</td>" +
      "</tr>";
    $("#data-" + category).append(row);
    objectMarker(item.id, item.lat, item.lng);
  }
}

// Show modal for object
function infoModal(id) {
  let title, content;
  if (id.substring(0, 1) === "C") {
    $.ajax({
      url: baseUrl + "/api/culinaryPlace/" + id,
      dataType: "json",
      success: function (response) {
        let item = response.data;
        let open = item.open.substring(0, item.open.length - 3);
        let close = item.close.substring(0, item.close.length - 3);

        title = "<h3>" + item.name + "</h3>";
        content =
          '<div class="text-start">' +
          '<p><span class="fw-bold">Address</span>: ' +
          item.address +
          "</p>" +
          '<p><span class="fw-bold">Open</span>: ' +
          open +
          " - " +
          close +
          " WIB</p>" +
          '<p><span class="fw-bold">Contact Person:</span> ' +
          item.contact_person +
          "</p>" +
          "</div>" +
          '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
          '<ol class="carousel-indicators">' +
          '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
          '</ol><div class="carousel-inner">' +
          '<div class="carousel-item active">' +
          '<img src="/media/photos/' +
          item.gallery[0] +
          '" alt="' +
          item.name +
          '" class="w-50" alt="' +
          item.name +
          '">' +
          "</div></div>" +
          '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
          '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Previous</span>' +
          " </a>" +
          '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
          '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Next</span>' +
          "</a>" +
          "</div>";

        Swal.fire({
          title: title,
          html: content,
          width: "50%",
          position: "top",
        });
      },
    });
  } else if (id.substring(0, 1) === "W") {
    $.ajax({
      url: baseUrl + "/api/worshipPlace/" + id,
      dataType: "json",
      success: function (response) {
        let item = response.data;

        title = "<h3>" + item.name + "</h3>";
        content =
          '<div class="text-start">' +
          '<p><span class="fw-bold">Address</span>: ' +
          item.address +
          "</p>" +
          '<p><span class="fw-bold">Park Area :</span> ' +
          item.parking_area +
          " m<sup>2</sup></p>" +
          '<p><span class="fw-bold">Building Area</span>: ' +
          item.building_area +
          " m<sup>2</sup></p>" +
          '<p><span class="fw-bold">Capacity</span>: ' +
          item.capacity +
          "</p>" +
          "</div>" +
          '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
          '<ol class="carousel-indicators">' +
          '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
          '</ol><div class="carousel-inner">' +
          '<div class="carousel-item active">' +
          '<img src="/media/photos/' +
          item.gallery[0] +
          '" alt="' +
          item.name +
          '" class="w-50" alt="' +
          item.name +
          '">' +
          "</div></div>" +
          '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
          '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Previous</span>' +
          " </a>" +
          '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
          '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Next</span>' +
          "</a>" +
          "</div>";

        Swal.fire({
          title: title,
          html: content,
          width: "50%",
          position: "top",
        });
      },
    });
  } else if (id.substring(0, 1) === "S") {
    $.ajax({
      url: baseUrl + "/api/souvenirPlace/" + id,
      dataType: "json",
      success: function (response) {
        let item = response.data;

        let open = item.open.substring(0, item.open.length - 3);
        let close = item.close.substring(0, item.close.length - 3);

        title = "<h3>" + item.name + "</h3>";
        content =
          '<div class="text-start">' +
          '<p><span class="fw-bold">Address</span>: ' +
          item.address +
          "</p>" +
          '<p><span class="fw-bold">Contact Person :</span> ' +
          item.contact_person +
          "</p>" +
          '<p><span class="fw-bold">Open</span>: ' +
          open +
          " - " +
          close +
          " WIB</p>" +
          "</div>" +
          '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
          '<ol class="carousel-indicators">' +
          '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
          '</ol><div class="carousel-inner">' +
          '<div class="carousel-item active">' +
          '<img src="/media/photos/' +
          item.gallery[0] +
          '" alt="' +
          item.name +
          '" class="w-50" alt="' +
          item.name +
          '">' +
          "</div></div>" +
          '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
          '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Previous</span>' +
          " </a>" +
          '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
          '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Next</span>' +
          "</a>" +
          "</div>";

        Swal.fire({
          title: title,
          html: content,
          width: "50%",
          position: "top",
        });
      },
    });
  } else if (id.substring(0, 1) === "A") {
    $.ajax({
      url: baseUrl + "/api/atraction/" + id,
      dataType: "json",
      success: function (response) {
        let item = response.data;

        let open = item.open.substring(0, item.open.length - 3);
        let close = item.close.substring(0, item.close.length - 3);

        title = "<h3>" + item.name + "</h3>";
        content =
          '<div class="text-start">' +
          '<p><span class="fw-bold">Address</span>: ' +
          item.address +
          "</p>" +
          '<p><span class="fw-bold">Contact Person :</span> ' +
          item.contact_person +
          "</p>" +
          '<p><span class="fw-bold">Open</span>: ' +
          open +
          " - " +
          close +
          " WIB</p>" +
          "</div>" +
          '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
          '<ol class="carousel-indicators">' +
          '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
          '</ol><div class="carousel-inner">' +
          '<div class="carousel-item active">' +
          '<img src="/media/photos/' +
          item.gallery[0] +
          '" alt="' +
          item.name +
          '" class="w-50" alt="' +
          item.name +
          '">' +
          "</div></div>" +
          '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
          '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Previous</span>' +
          " </a>" +
          '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
          '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
          '<span class="visually-hidden">Next</span>' +
          "</a>" +
          "</div>";

        Swal.fire({
          title: title,
          html: content,
          width: "50%",
          position: "top",
        });
      },
    });
  }
}

// Find object by name
function findByName(category) {
  clearRadius();
  clearRoute();
  clearMarker();
  clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");
  closeNearby();

  let name;
  if (category === "RG") {
    name = document.getElementById("nameRG").value;
    $.ajax({
      url: baseUrl + "/api/rumahGadang/findByName",
      type: "POST",
      data: {
        name: name,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  } else if (category === "EV") {
    name = document.getElementById("nameEV").value;
    $.ajax({
      url: baseUrl + "/api/event/findByName",
      type: "POST",
      data: {
        name: name,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  } else if (category === "UP") {
    name = document.getElementById("nameUP").value;
    $.ajax({
      url: baseUrl + "/api/uniquePlace/findByName",
      type: "POST",
      data: {
        name: name,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  }
}

// Get list of Rumah Gadang facilities
function getFacility() {
  let facility;
  $("#facilitySelect").empty();
  $.ajax({
    url: baseUrl + "/api/facility",
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        facility =
          '<option value="' + item.id + '">' + item.facility + "</option>";
        $("#facilitySelect").append(facility);
      }
    },
  });
}

// Find object by Facility
function findByFacility() {
  clearRadius();
  clearRoute();
  clearMarker();
  clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");
  closeNearby();

  let facility = document.getElementById("facilitySelect").value;
  $.ajax({
    url: baseUrl + "/api/rumahGadang/findByFacility",
    type: "POST",
    data: {
      facility: facility,
    },
    dataType: "json",
    success: function (response) {
      displayFoundObject(response);
      boundToObject();
    },
  });
}

// Set star by user input
function setStar(star) {
  switch (star) {
    case "star-1":
      $("#star-1").addClass("star-checked");
      $("#star-2,#star-3,#star-4,#star-5").removeClass("star-checked");
      document.getElementById("star-rating").value = "1";
      break;
    case "star-2":
      $("#star-1,#star-2").addClass("star-checked");
      $("#star-3,#star-4,#star-5").removeClass("star-checked");
      document.getElementById("star-rating").value = "2";
      break;
    case "star-3":
      $("#star-1,#star-2,#star-3").addClass("star-checked");
      $("#star-4,#star-5").removeClass("star-checked");
      document.getElementById("star-rating").value = "3";
      break;
    case "star-4":
      $("#star-1,#star-2,#star-3,#star-4").addClass("star-checked");
      $("#star-5").removeClass("star-checked");
      document.getElementById("star-rating").value = "4";
      break;
    case "star-5":
      $("#star-1,#star-2,#star-3,#star-4,#star-5").addClass("star-checked");
      document.getElementById("star-rating").value = "5";
      break;
  }
}

// Find object by Rating
function findByRating(category) {
  clearRadius();
  clearRoute();
  clearMarker();
  clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");
  closeNearby();

  let rating = document.getElementById("star-rating").value;
  if (category === "RG") {
    $.ajax({
      url: baseUrl + "/api/rumahGadang/findByRating",
      type: "POST",
      data: {
        rating: rating,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  } else if (category === "EV") {
    $.ajax({
      url: baseUrl + "/api/event/findByRating",
      type: "POST",
      data: {
        rating: rating,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  } else if (category === "UP") {
    $.ajax({
      url: baseUrl + "/api/uniquePlace/findByRating",
      type: "POST",
      data: {
        rating: rating,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  }
}

// Find object by Category
function findByCategory(object) {
  clearRadius();
  clearRoute();
  clearMarker();
  clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");
  closeNearby();

  if (object === "RG") {
    let category = document.getElementById("categoryRGSelect").value;
    $.ajax({
      url: baseUrl + "/api/rumahGadang/findByCategory",
      type: "POST",
      data: {
        category: category,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  } else if (object === "EV") {
    let category = document.getElementById("categoryEVSelect").value;
    $.ajax({
      url: baseUrl + "/api/event/findByCategory",
      type: "POST",
      data: {
        category: category,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  } else if (object === "UP") {
    let category = document.getElementById("categoryUPSelect").value;
    $.ajax({
      url: baseUrl + "/api/uniquePlace/findByCategory",
      type: "POST",
      data: {
        category: category,
      },
      dataType: "json",
      success: function (response) {
        displayFoundObject(response);
        boundToObject();
      },
    });
  }
}

// Get list of Event category
function getCategory() {
  let category;
  $("#categoryEVSelect").empty();
  $.ajax({
    url: baseUrl + "/api/event/category",
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        category =
          '<option value="' + item.id + '">' + item.category + "</option>";
        $("#categoryEVSelect").append(category);
      }
    },
  });
}

// // Find object by Date
function findByDate() {
  clearRadius();
  clearRoute();
  clearMarker();
  clearUser();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");
  closeNearby();

  let eventDate = document.getElementById("eventDate").value;
  $.ajax({
    url: baseUrl + "/api/event/findByDate",
    type: "POST",
    data: {
      date: eventDate,
    },
    dataType: "json",
    success: function (response) {
      displayFoundObject(response);
      boundToObject();
    },
  });
}

// Create compass
function setCompass() {
  const compass = document.createElement("div");
  compass.setAttribute("id", "compass");
  const compassDiv = document.createElement("div");
  compass.appendChild(compassDiv);
  const compassImg = document.createElement("img");
  compassImg.src = baseUrl + "/media/icon/compass.png";
  compassDiv.appendChild(compassImg);

  map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(compass);
}

// Create legend
function getLegend() {
  const icons = {
    rg: {
      name: "Rumah Gadang",
      icon: baseUrl + "/media/icon/marker_rg.png",
    },
    ev: {
      name: "Event",
      icon: baseUrl + "/media/icon/marker_ev.png",
    },
    up: {
      name: "Unique Place",
      icon: "https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png",
    },
    cp: {
      name: "Culinary Place",
      icon: baseUrl + "/media/icon/marker_cp.png",
    },
    wp: {
      name: "Worship Place",
      icon: baseUrl + "/media/icon/marker_wp.png",
    },
    sp: {
      name: "Souvenir Place",
      icon: baseUrl + "/media/icon/marker_sp.png",
    },
  };

  const title = '<p class="fw-bold fs-6">Legend</p>';
  $("#legend").append(title);

  for (key in icons) {
    const type = icons[key];
    const name = type.name;
    const icon = type.icon;
    const div = '<div><img src="' + icon + '"> ' + name + "</div>";

    $("#legend").append(div);
  }
  map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
}

// toggle legend element
function viewLegend() {
  if ($("#legend").is(":hidden")) {
    $("#legend").show();
  } else {
    $("#legend").hide();
  }
}

// list object for new visit history
function getObjectByCategory() {
  const category = document.getElementById("category").value;
  $("#object").empty();
  if (category === "None") {
    object = '<option value="None">Select Category First</option>';
    $("#object").append(object);
    return Swal.fire({
      icon: "warning",
      title: "Please Choose a Object Category!",
    });
  }
  if (category === "1") {
    $.ajax({
      url: baseUrl + "/api/rumahGadang",
      dataType: "json",
      success: function (response) {
        let data = response.data;
        for (i in data) {
          let item = data[i];
          object = '<option value="' + item.id + '">' + item.name + "</option>";
          $("#object").append(object);
        }
      },
    });
  } else if (category === "2") {
    $.ajax({
      url: baseUrl + "/api/event",
      dataType: "json",
      success: function (response) {
        let data = response.data;
        for (i in data) {
          let item = data[i];
          object = '<option value="' + item.id + '">' + item.name + "</option>";
          $("#object").append(object);
        }
      },
    });
  }
}

// Validate if star rating picked yet
function checkStar(event) {
  const star = document.getElementById("star-rating").value;
  if (star == "0") {
    event.preventDefault();
    Swal.fire("Please put rating star");
  }
}

// Check if Category and Object is chose correctly
function checkForm(event) {
  const category = document.getElementById("category").value;
  const object = document.getElementById("object").value;
  if (category === "None" || object === "None") {
    event.preventDefault();
    Swal.fire("Please select the correct Category and Object");
  }
}

// Update preview of uploaded photo profile
function showPreview(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      $("#avatar-preview").attr("src", e.target.result).width(300).height(300);
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// Get list of Recommendation
function getRecommendation(id, recom) {
  let recommendation;
  $("#recommendationSelect" + id).empty();
  $.ajax({
    url: baseUrl + "/api/recommendationList",
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        if (item.id == recom) {
          recommendation =
            '<option value="' +
            item.id +
            '" selected>' +
            item.name +
            "</option>";
        } else {
          recommendation =
            '<option value="' + item.id + '">' + item.name + "</option>";
        }
        $("#recommendationSelect" + id).append(recommendation);
      }
    },
  });
}

// Update option onclick function for updating Recommendation
function changeRecom(status = null) {
  if (status === "edit") {
    $("#recomBtnEdit").hide();
    $("#recomBtnExit").show();

    $(".recomSelect").on("change", updateRecom);
  } else {
    $("#recomBtnEdit").show();
    $("#recomBtnExit").hide();

    $(".recomSelect").off("change", updateRecom);
  }
}

// Update recommendation based on input User
function updateRecom() {
  let recom = $(this).find("option:selected").val();
  let id = $(this).attr("id");
  $.ajax({
    url: baseUrl + "/api/recommendation",
    type: "POST",
    data: {
      id: id,
      recom: recom,
    },
    dataType: "json",
    success: function (response) {
      if (response.status === 201) {
        console.log("Success update recommendation @" + id + ":" + recom);
        Swal.fire("Success updating Rumah Gadang ID @" + id);
      }
    },
  });
}

// Set map to coordinate put by user
function findCoords(object) {
  clearMarker();
  google.maps.event.clearListeners(map, "click");

  const lat = Number(document.getElementById("latitude").value);
  const lng = Number(document.getElementById("longitude").value);

  if (lat === 0 || lng === 0 || isNaN(lat) || isNaN(lng)) {
    return Swal.fire("Please input Lat and Long");
  }

  let pos = new google.maps.LatLng(lat, lng);
  map.panTo(pos);
}

// Unselect shape on drawing map
function clearSelection() {
  if (selectedShape) {
    selectedShape.setEditable(false);
    selectedShape = null;
  }
}

// Make selected shape editable on maps
function setSelection(shape) {
  clearSelection();
  selectedShape = shape;
  shape.setEditable(true);
}

// Remove selected shape on maps
function deleteSelectedShape() {
  if (selectedShape) {
    document.getElementById("latitude").value = "";
    document.getElementById("longitude").value = "";
    document.getElementById("geo-json").value = "";
    document.getElementById("lat").value = "";
    document.getElementById("lng").value = "";
    clearMarker();
    selectedShape.setMap(null);
    setSelection(selectedShape);
    // To show:
    drawingManager.setOptions({
      drawingMode: google.maps.drawing.OverlayType.POLYGON,
      drawingControl: true,
    });
  }
}

// Initialize drawing manager on maps
function initDrawingManager(edit = false) {
  const drawingManagerOpts = {
    drawingMode: google.maps.drawing.OverlayType.POLYGON,
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: [google.maps.drawing.OverlayType.POLYGON],
    },
    polygonOptions: {
      fillColor: "blue",
      strokeColor: "blue",
      editable: true,
    },
    map: map,
  };
  drawingManager.setOptions(drawingManagerOpts);
  let newShape;

  if (!edit) {
    google.maps.event.addListener(
      drawingManager,
      "overlaycomplete",
      function (event) {
        drawingManager.setOptions({
          drawingControl: false,
          drawingMode: null,
        });
        newShape = event.overlay;
        newShape.type = event.type;
        setSelection(newShape);
        saveSelection(newShape);

        google.maps.event.addListener(newShape, "click", function () {
          setSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "insert_at", () => {
          saveSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "remove_at", () => {
          saveSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "set_at", () => {
          saveSelection(newShape);
        });
      }
    );
  } else {
    drawingManager.setOptions({
      drawingControl: false,
      drawingMode: null,
    });

    newShape = drawGeom();
    newShape.type = "polygon";
    setSelection(newShape);

    const paths = newShape.getPath().getArray();
    let bounds = new google.maps.LatLngBounds();
    for (let i = 0; i < paths.length; i++) {
      bounds.extend(paths[i]);
    }
    let pos = bounds.getCenter();
    map.panTo(pos);

    clearMarker();
    let marker = new google.maps.Marker();
    markerOption = {
      position: pos,
      animation: google.maps.Animation.DROP,
      map: map,
    };
    marker.setOptions(markerOption);
    markerArray["newRG"] = marker;

    google.maps.event.addListener(
      drawingManager,
      "overlaycomplete",
      function (event) {
        drawingManager.setOptions({
          drawingControl: false,
          drawingMode: null,
        });
        newShape = event.overlay;
        newShape.type = event.type;
        setSelection(newShape);
        saveSelection(newShape);

        google.maps.event.addListener(newShape, "click", function () {
          setSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "insert_at", () => {
          saveSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "remove_at", () => {
          saveSelection(newShape);
        });
        google.maps.event.addListener(newShape.getPath(), "set_at", () => {
          saveSelection(newShape);
        });
      }
    );
  }

  google.maps.event.addListener(map, "click", clearSelection);
  google.maps.event.addDomListener(
    document.getElementById("clear-drawing"),
    "click",
    deleteSelectedShape
  );
}

// Get geoJSON of selected shape on map
function saveSelection(shape) {
  const paths = shape.getPath().getArray();
  let bounds = new google.maps.LatLngBounds();
  for (let i = 0; i < paths.length; i++) {
    bounds.extend(paths[i]);
  }
  let pos = bounds.getCenter();
  map.panTo(pos);

  clearMarker();
  let marker = new google.maps.Marker();
  markerOption = {
    position: pos,
    animation: google.maps.Animation.DROP,
    map: map,
  };
  marker.setOptions(markerOption);
  markerArray["newRG"] = marker;

  document.getElementById("latitude").value = pos.lat().toFixed(8);
  document.getElementById("longitude").value = pos.lng().toFixed(8);
  document.getElementById("lat").value = pos.lat().toFixed(8);
  document.getElementById("lng").value = pos.lng().toFixed(8);

  const dataLayer = new google.maps.Data();
  dataLayer.add(
    new google.maps.Data.Feature({
      geometry: new google.maps.Data.Polygon([shape.getPath().getArray()]),
    })
  );
  dataLayer.toGeoJson(function (object) {
    document.getElementById("geo-json").value = JSON.stringify(
      object.features[0].geometry
    );
  });
}

// Get list of users
function getListUsers(owner) {
  let users;
  $("#ownerSelect").empty();
  $.ajax({
    url: baseUrl + "/api/owner",
    dataType: "json",
    success: function (response) {
      let data = response.data;

      for (i in data) {
        let item = data[i];
        if (item.id == owner) {
          users =
            '<option value="' +
            item.id +
            '" selected>' +
            item.first_name +
            " " +
            item.last_name +
            "</option>";
        } else {
          users =
            '<option value="' +
            item.id +
            '">' +
            item.first_name +
            " " +
            item.last_name +
            "</option>";
        }
        $("#ownerSelect").append(users);
      }
    },
  });
}

// Draw current GeoJSON on drawing manager
function drawGeom() {
  const geoJSON = $("#geo-json").val();
  if (geoJSON !== "") {
    const geoObj = JSON.parse(geoJSON);
    const coords = geoObj.coordinates[0];
    let polygonCoords = [];
    for (i in coords) {
      polygonCoords.push({ lat: coords[i][1], lng: coords[i][0] });
    }
    const polygon = new google.maps.Polygon({
      paths: polygonCoords,
      fillColor: "blue",
      strokeColor: "blue",
      editable: true,
    });
    polygon.setMap(map);
    return polygon;
  }
}

// Delete selected object
function deleteObject(id = null, name = null, user = false) {
  if (id === null) {
    return Swal.fire("ID cannot be null");
  }

  let content, apiUri;
  if (id.substring(0, 1) === "R") {
    content = "Rumah Gadang";
    apiUri = "rumahGadang/";
  } else if (id.substring(0, 2) === "AF") {
    content = "Atraction Facility";
    apiUri = "atractionFacility/";
  } else if (id.substring(0, 1) === "A") {
    content = "Atraction";
    apiUri = "atraction/";
  } else if (id.substring(0, 1) === "E") {
    content = "Event";
    apiUri = "event/";
  } else if (id.substring(0, 2) === "UF") {
    content = "Unit Facility";
    apiUri = "homestayUnitFacility/";
  } else if (id.substring(0, 1) === "U") {
    content = "Unique Place";
    apiUri = "uniquePlace/";
  } else if (id.substring(0, 2) === "PT") {
    content = "Package Type";
    apiUri = "packageType/";
  } else if (id.substring(0, 1) === "P") {
    content = "Tourism Package";
    apiUri = "package/";
  } else if (id.substring(0, 2) === "SP") {
    content = "Service";
    apiUri = "service/";
  } else if (id.substring(0, 2) === "HF") {
    content = "Homestay Facility";
    apiUri = "homestayFacility/";
  } else if (id.substring(0, 2) === "HU") {
    content = "Homestay Unit";
    apiUri = "homestayUnit/";
  } else if (id.substring(0, 1) === "H") {
    content = "Homestay";
    apiUri = "homestay/";
  } else if (user === true) {
    content = "User";
    apiUri = "user/";
  } else {
    content = "Facility";
    apiUri = "facility/";
  }

  Swal.fire({
    title: "Delete " + content + "?",
    text: "You are about to remove " + name,
    icon: "warning",
    showCancelButton: true,
    denyButtonText: "Delete",
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#343a40",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: baseUrl + "/api/" + apiUri + id,
        type: "DELETE",
        dataType: "json",
        success: function (response) {
          console.log(response);
          if (response.status === 200) {
            Swal.fire(
              "Deleted!",
              "Successfully remove " + name,
              "success"
            ).then((result) => {
              if (result.isConfirmed) {
                document.location.reload();
              }
            });
          } else {
            Swal.fire("Failed", "Delete " + name + " failed!", "warning");
          }
        },
      });
    }
  });
}

/// Android API ///

// Get user's current position
function userPositionAPI(lat = null, lng = null) {
  clearRadius();
  clearRoute();

  infoWindow.close();
  let pos = new google.maps.LatLng(lat, lng);

  clearUser();
  markerOption = {
    position: pos,
    map: map,
  };
  userMarker.setOptions(markerOption);

  setUserLoc(pos.lat().toFixed(8), pos.lng().toFixed(8));
}

// Pan map to user position
function panToUser() {
  if (userLat == 0 && userLng == 0) {
    return Swal.fire("Determine your position first!");
  }
  let pos = new google.maps.LatLng(userLat, userLng);
  map.panTo(pos);
}

// Find RG on mobile
function findRG(name = null) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  currentUrl = "mobile";
  $.ajax({
    url: baseUrl + "/api/rumahGadang/findByName",
    type: "POST",
    data: {
      name: name,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// Find RG by Rating on Mobile
function findByRatingRG(rating) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  currentUrl = "mobile";
  $.ajax({
    url: baseUrl + "/api/rumahGadang/findByRating",
    type: "POST",
    data: {
      rating: rating,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// Find object by Facility on Mobile
function findByFacilityRG(facility) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  currentUrl = "mobile";
  $.ajax({
    url: baseUrl + "/api/rumahGadang/findByFacility",
    type: "POST",
    data: {
      facility: facility,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// Find RG by Category on Mobile
function findByCategoryRG(category) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  currentUrl = "mobile";
  $.ajax({
    url: baseUrl + "/api/rumahGadang/findByCategory",
    type: "POST",
    data: {
      category: category,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// Find EV on mobile
function findEV(name = null) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  currentUrl = "mobile";
  $.ajax({
    url: baseUrl + "/api/event/findByName",
    type: "POST",
    data: {
      name: name,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// Find EV by Rating on Mobile
function findByRatingEV(rating) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  currentUrl = "mobile";
  $.ajax({
    url: baseUrl + "/api/event/findByRating",
    type: "POST",
    data: {
      rating: rating,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// Find EV by Category on Mobile
function findByCategoryEV(category) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  currentUrl = "mobile";
  $.ajax({
    url: baseUrl + "/api/event/findByCategory",
    type: "POST",
    data: {
      category: category,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// // Find EV by Date
function findByDateEV(eventDate) {
  clearRadius();
  clearRoute();
  clearMarker();
  destinationMarker.setMap(null);
  google.maps.event.clearListeners(map, "click");

  $.ajax({
    url: baseUrl + "/api/event/findByDate",
    type: "POST",
    data: {
      date: eventDate,
    },
    dataType: "json",
    success: function (response) {
      let data = response.data;
      for (i in data) {
        let item = data[i];
        currentUrl = currentUrl + item.id;
        objectMarker(item.id, item.lat, item.lng);
      }
      boundToObject();
    },
  });
}

// API weather
saribuWeather();
function saribuWeather() {
  const apiWeather = "2390a9743ed947a7ab68238ae3039af1";
  const lat = -1.4815029;
  const lng = 101.0574556;

  $.ajax({
    url: `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lng}&appid=${apiWeather}`,
    method: "get",
    dataType: "json",
    success: function (response) {
      let data = response;

      const tempInCelsius = (data.main.temp - 273.15).toFixed(2); // Konversi dari Kelvin ke Celsius
      const weatherDescription = data.weather[0].description;
      const humidity = data.main.humidity;
      const windSpeed = data.wind.speed;
      const iconUrl = `https://openweathermap.org/img/wn/${data.weather[0].icon}.png`;

      // Memeriksa apakah elemen ada sebelum memperbarui kontennya
      let weatherTempElement = document.getElementById("weatherTemp");
      if (weatherTempElement) {
        weatherTempElement.textContent = `${tempInCelsius}°C`;
      }

      let weatherCloudElement = document.getElementById("weatherCloud");
      if (weatherCloudElement) {
        weatherCloudElement.textContent =
          weatherDescription.charAt(0).toUpperCase() +
          weatherDescription.slice(1);
      }

      let weatherHumidityElement = document.getElementById("weatherHumidity");
      if (weatherHumidityElement) {
        weatherHumidityElement.textContent = `Humidity: ${humidity}%`;
      }

      let weatherWindElement = document.getElementById("weatherWind");
      if (weatherWindElement) {
        weatherWindElement.textContent = `Wind: ${windSpeed} m/s`;
      }

      let weatherIconElement = document.querySelector("#weather-info img");
      if (weatherIconElement) {
        weatherIconElement.src = iconUrl;
      }
    },
    error: function (err) {
      console.error("Error fetching weather data", err);
    },
  });
}

// user
// Fungsi untuk menyimpan dan mengelola User ID
const UserIdManager = {
  saveUserIdToSessionStorage: function (userId) {
    try {
      sessionStorage.setItem("user_id", userId);
      console.log("User ID berhasil disimpan di sessionStorage");
    } catch (error) {
      console.error("Gagal menyimpan User ID di sessionStorage:", error);
    }
  },

  getUserIdFromSessionStorage: function () {
    try {
      return sessionStorage.getItem("user_id") || null;
    } catch (error) {
      console.error("Gagal mengambil User ID dari sessionStorage:", error);
      return null;
    }
  },

  removeUserIdFromSessionStorage: function () {
    try {
      sessionStorage.removeItem("user_id");
      console.log("User ID berhasil dihapus dari sessionStorage");
    } catch (error) {
      console.error("Gagal menghapus User ID dari sessionStorage:", error);
    }
  },
};

// Fungsi untuk menerima User ID dari B4A WebView
function receiveUserIdFromB4A(userId) {
  UserIdManager.saveUserIdToSessionStorage(userId);
  console.log("User ID diterima dari B4A:", userId);
}
