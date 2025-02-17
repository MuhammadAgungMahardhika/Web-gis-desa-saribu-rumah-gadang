let baseUrl = '';
let currentUrl = '';
let currentLat = 0, currentLng = 0
let userLat = 0, userLng = 0;
let web, map;
let infoWindow = new google.maps.InfoWindow();
let userInfoWindow = new google.maps.InfoWindow();
let directionsService, directionsRenderer;
let userMarker = new google.maps.Marker();
let destinationMarker = new google.maps.Marker();
let routeArray = [], circleArray = [], markerArray = {};
let bounds = new google.maps.LatLngBounds();
let selectedShape, drawingManager = new google.maps.drawing.DrawingManager();
let geomAreaArray = []

let customStyled = [
    {
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative.land_parcel",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative.neighborhood",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    }
];

function setBaseUrl(url) {
    baseUrl = url;
}

// Initialize and add the map

function initMap(lat = -1.483815, lng = 101.058960, mobile = false) {
    directionsService = new google.maps.DirectionsService();
    const center = new google.maps.LatLng(lat, lng);
    if (!mobile) {
        map = new google.maps.Map(document.getElementById("googlemaps"), {
            zoom: 7,
            center: center,
            mapTypeId: 'roadmap',
        });
    } else {
        map = new google.maps.Map(document.getElementById("googlemaps"), {
            zoom: 18,
            center: center,
            mapTypeControl: false,
        });
    }
    var rendererOptions = {
        map: map
    }
    map.set('styles', customStyled);
    directionsRenderer = new google.maps.DirectionsRenderer(rendererOptions);
    digitVillage();
    addAreaPolygon(geomSumbar);
}

// Display tourism village digitizing
function digitVillage() {
    const village = new google.maps.Data();
    $.ajax({
        url: baseUrl + '/api/village',
        type: 'POST',
        data: {
            village: '1'
        },
        dataType: 'json',
        success: function (response) {
            const data = response.data;
            console.log(data)
            village.addGeoJson(data);
            village.setStyle({
                fillColor:'#00b300',
                strokeWeight:0.5,
                strokeColor:'#005000',
                fillOpacity: 0.1,
                clickable: false
            });
            village.setMap(map);
        }
    });
}
function digitRegion() {
    const a = { type: 'Feature', geometry: regionData }
    const region = new google.maps.Data();
    region.addGeoJson(a)
    region.setStyle({
      fillColor:'#00b300',
      strokeWeight:0.5,
      strokeColor:'#005000',
      fillOpacity: 0.1,
       clickable: false
    });
    region.setMap(map);
}
// Construct the polygon.
function addAreaPolygon(geoJson, color, opacity) {
    clearAreaGeom()
        // Load GeoJSON.
        map.data.addGeoJson(geoJson);
        map.data.setStyle({
            fillColor: '#00b300',
            strokeWeight: 0.5,
            strokeColor: color,
            fillOpacity: 0.1,
            clickable: false
        })
        var bounds = new google.maps.LatLngBounds();
    
        // Loop through features
        map.data.forEach(function(feature) {
          var geo = feature.getGeometry();
    
          geo.forEachLatLng(function(LatLng) {
            bounds.extend(LatLng);
          });
        });
        map.fitBounds(bounds);
}

// clear pariangan geom on map
function clearAreaGeom() {
    if(map.data){
        map.data.forEach(function(feature) {
            map.data.remove(feature)
        })
    }
}

// move camera
function moveCamera(z = 17) {
    map.moveCamera({ zoom: z })
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
    for(i in routeArray) {
        routeArray[i].setMap(null);
    }
    routeArray = [];
    $('#direction-row').hide();
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

    google.maps.event.clearListeners(map, 'click');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
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
                userInfoWindow.setContent("<p class='text-center'><span class='fw-bold'>You are here.</span> <br> lat: " + pos.lat + "<br>long: " + pos.lng+ "</p>");
                userInfoWindow.open(map, userMarker);
                map.setCenter(pos);
                setUserLoc(pos.lat, pos.lng);

                userMarker.addListener('click', () => {
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
        Swal.fire('Click on Map');
    }
    map.addListener('click', (mapsMouseEvent) => {

        infoWindow.close();
        pos = mapsMouseEvent.latLng;

        clearUser();
        markerOption = {
            position: pos,
            animation: google.maps.Animation.DROP,
            map: map,
        };
        userMarker.setOptions(markerOption);
        userInfoWindow.setContent("<p class='text-center'><span class='fw-bold'>You are here.</span> <br> lat: " + pos.lat().toFixed(8) + "<br>long: " + pos.lng().toFixed(8)+ "</p>");
        userInfoWindow.open(map, userMarker);

        userMarker.addListener('click', () => {
            userInfoWindow.open(map, userMarker);
        });

        setUserLoc(pos.lat().toFixed(8), pos.lng().toFixed(8))
    });

}

// Render route on selected object
function routeTo(lat, lng, routeFromUser = true) {

    clearRadius();
    clearRoute();
    google.maps.event.clearListeners(map, 'click')

    let start, end;
    if (routeFromUser) {
        if (userLat == 0 && userLng == 0) {
            return Swal.fire('Determine your position first!');
        }
        setUserLoc(userLat, userLng);
    }
    start = new google.maps.LatLng(currentLat, currentLng);
    end = new google.maps.LatLng(lat, lng)
    let request = {
        origin: start,
        destination: end,
        travelMode: 'DRIVING'
    };
    directionsService.route(request, function(result, status) {
        if (status == 'OK') {
            directionsRenderer.setDirections(result);
            showSteps(result);
            directionsRenderer.setMap(map);
            routeArray.push(directionsRenderer);
        }
    });
    boundToRoute(start, end);
}

// Display marker for loaded object
function objectMarker(id, lat, lng, anim = true) {

    google.maps.event.clearListeners(map, 'click');
    let pos = new google.maps.LatLng(lat, lng);
    let marker = new google.maps.Marker();

    let icon;
    if (id.substring(0,1) === "R") {
        icon = baseUrl + '/media/icon/marker_rg.png';
    } else if (id.substring(0,1) === "C") {
        icon = baseUrl + '/media/icon/marker_cp.png';
    } else if (id.substring(0,1) === "W") {
        icon = baseUrl + '/media/icon/marker_wp.png';
    } else if (id.substring(0,1) === "S") {
        icon = baseUrl + '/media/icon/marker_sp.png';
    } else if (id.substring(0,1) === "E") {
        icon = baseUrl + '/media/icon/marker_ev.png';
    } else if (id.substring(0,1) === "A") {
        icon = baseUrl + '/media/icon/marker_cp.png';
    }

    markerOption = {
        position: pos,
        icon: icon,
        animation: google.maps.Animation.DROP,
        map: map,
    }
    marker.setOptions(markerOption);
    if (!anim) {
        marker.setAnimation(null);
    }
    marker.addListener('click', () => {
        infoWindow.close();
        objectInfoWindow(id);
        infoWindow.open(map, marker);
    });
    markerArray[id] = marker;
}

// Display markers for loaded objects from package
function objectPackageDetail(objectData) {
    objectData.forEach(element => {
        let id = element['id']
        let lat = element['lat']
        let lng = element['lng']
        
        objectMarker(id,lat,lng,anim=false)
    });
}

// Display info window for loaded object
function objectInfoWindow(id){
    let content = '';
    let contentButton = '';
    let contentMobile = '';

    if (id.substring(0,1) === "R") {
        $.ajax({
            url: baseUrl + '/api/rumahGadang/' + id,
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                let rgid = data.id;
                let name = data.name;
                let lat = data.lat;
                let lng = data.lng;
                let ticket_price = (data.ticket_price == 0) ? 'Free' : 'Rp ' + data.ticket_price;
                let open = data.open.substring(0, data.open.length - 3);
                let close = data.close.substring(0, data.close.length - 3);

                content =
                    '<div class="text-center">' +
                    '<p class="fw-bold fs-6">'+ name +'</p> <br>' +
                    '<p><i class="fa-solid fa-clock me-2"></i> '+open+' - '+close+' WIB</p>' +
                    '<p><i class="fa-solid fa-money-bill me-2"></i> '+ ticket_price +'</p>' +
                    '</div>';
                contentButton =
                    '<br><div class="text-center">' +
                    '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo('+lat+', '+lng+')"><i class="fa-solid fa-road"></i></a>' +
                    '<a title="Info" class="btn icon btn-outline-primary mx-1" target="_blank" id="infoInfoWindow" href='+baseUrl+'/web/rumahGadang/'+rgid+'><i class="fa-solid fa-info"></i></a>' +
                    '<a title="Nearby" class="btn icon btn-outline-primary mx-1" id="nearbyInfoWindow" onclick="openNearby(`'+ rgid +'`,'+ lat +','+ lng +')"><i class="fa-solid fa-compass"></i></a>' +
                    '</div>'
                contentMobile =
                    '<br><div class="text-center">' +
                    '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo('+lat+', '+lng+')"><i class="fa-solid fa-road"></i></a>' +
                    '</div>'

                if (currentUrl.includes(id)) {
                    if (currentUrl.includes('mobile')){
                        infoWindow.setContent(content + contentMobile);
                    } else {
                        infoWindow.setContent(content);
                    }
                    infoWindow.open(map, markerArray[rgid])
                } else {
                    infoWindow.setContent(content + contentButton);
                }
            }
        });
    } else if (id.substring(0,1) === "U") {
        $.ajax({
            url: baseUrl + '/api/uniquePlace/' + id,
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                let upid = data.id;
                let name = data.name;
                let lat = data.lat;
                let lng = data.lng;

                content =
                    '<div class="text-center">' +
                    '<p class="fw-bold fs-6">'+ name +'</p> <br>' +
                    '</div>';
                contentButton =
                    '<br><div class="text-center">' +
                    '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo('+lat+', '+lng+')"><i class="fa-solid fa-road"></i></a>' +
                    '<a title="Info" class="btn icon btn-outline-primary mx-1" target="_blank" id="infoInfoWindow" href='+baseUrl+'/web/uniquePlace/'+upid+'><i class="fa-solid fa-info"></i></a>' +
                    '<a title="Nearby" class="btn icon btn-outline-primary mx-1" id="nearbyInfoWindow" onclick="openNearby(`'+ upid +'`,'+ lat +','+ lng +')"><i class="fa-solid fa-compass"></i></a>' +
                    '</div>'
                contentMobile =
                    '<br><div class="text-center">' +
                    '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo('+lat+', '+lng+')"><i class="fa-solid fa-road"></i></a>' +
                    '</div>'

                if (currentUrl.includes(id)) {
                    if (currentUrl.includes('mobile')){
                        infoWindow.setContent(content + contentMobile);
                    } else {
                        infoWindow.setContent(content);
                    }
                    infoWindow.open(map, markerArray[upid])
                } else {
                    infoWindow.setContent(content + contentButton);
                }
            }
        });
    } else if (id.substring(0,1) === "E") {
        const months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];
        $.ajax({
            url: baseUrl + '/api/event/' + id,
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                let evid = data.id;
                let name = data.name;
                let lat = data.lat;
                let lng = data.lng;
                let ticket_price = (data.ticket_price == 0) ? 'Free' : 'Rp ' + data.ticket_price;
                let category = data.category;
                let date_next = new Date(data.date_start);
                let next = date_next.getDate() + ' ' + months[date_next.getMonth()] + ' ' + date_next.getFullYear();

                content =
                    '<div class="text-center">' +
                    '<p class="fw-bold fs-6">'+ name +'</p> <br>' +
                    '<p><i class="fa-solid fa-layer-group me-2"></i> '+ category +'</p>' +
                    '<p><i class="fa-solid fa-money-bill me-2"></i> '+ ticket_price +'</p>' +
                    '<p><i class="fa-solid fa-calendar-days me-2"></i> '+ next +'</p>' +
                    '</div>';
                contentButton =
                    '<br><div class="text-center">' +
                    '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo('+lat+', '+lng+')"><i class="fa-solid fa-road"></i></a>' +
                    '<a title="Info" class="btn icon btn-outline-primary mx-1" target="_blank" id="infoInfoWindow" href='+baseUrl+'/web/event/'+evid+'><i class="fa-solid fa-info"></i></a>' +
                    '<a title="Nearby" class="btn icon btn-outline-primary mx-1" id="nearbyInfoWindow" onclick="openNearby(`'+ evid +'`,'+ lat +','+ lng +')"><i class="fa-solid fa-compass"></i></a>' +
                    '</div>'
                contentMobile =
                    '<br><div class="text-center">' +
                    '<a title="Route" class="btn icon btn-outline-primary mx-1" id="routeInfoWindow" onclick="routeTo('+lat+', '+lng+')"><i class="fa-solid fa-road"></i></a>' +
                    '</div>'

                if (currentUrl.includes(id)) {
                    if (currentUrl.includes('mobile')){
                        infoWindow.setContent(content + contentMobile);
                    } else {
                        infoWindow.setContent(content);
                    }
                    infoWindow.open(map, markerArray[evid])
                } else {
                    infoWindow.setContent(content + contentButton);
                }
            }
        });
    } else if (id.substring(0,1) === "C") {
        $.ajax({
            url: baseUrl + '/api/culinaryPlace/' + id,
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                let name = data.name;

                content =
                    '<div class="text-center">' +
                    '<p class="fw-bold fs-6">'+ name +'</p>' +
                    '</div>';

                infoWindow.setContent(content);
            }
        });
    } else if (id.substring(0,1) === "W") {
        $.ajax({
            url: baseUrl + '/api/worshipPlace/' + id,
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                let name = data.name;

                content =
                    '<div class="text-center">' +
                    '<p class="fw-bold fs-6">'+ name +'</p>' +
                    '</div>';

                infoWindow.setContent(content);
            }
        });
    } else if (id.substring(0,1) === "S") {
        $.ajax({
            url: baseUrl + '/api/souvenirPlace/' + id,
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                let name = data.name;

                content =
                    '<div class="text-center">' +
                    '<p class="fw-bold fs-6">'+ name +'</p>' +
                    '</div>';

                infoWindow.setContent(content);
            }
        });
    } else if (id.substring(0,1) === "A") {
      
        $.ajax({
            url: baseUrl + '/api/atraction/' + id,
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                let name = data.name;
             
                content =
                    '<div class="text-center">' +
                    '<p class="fw-bold fs-6">'+ name +'</p>' +
                    '</div>';

                infoWindow.setContent(content);
            },
            error: function (err) {
                console.log(err.responseText)
            }
        });
    }
}

// Render map to contains all object marker
function boundToObject(firstTime = true) {
    if (Object.keys(markerArray).length > 0) {
        bounds = new google.maps.LatLngBounds();
        for (i in markerArray) {
            bounds.extend(markerArray[i].getPosition())
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
        radius: Number(rad)
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
    document.getElementById('radiusValue' + postfix).innerHTML = (document.getElementById('inputRadius' + postfix).value * 100) + " m";
}

// Render search by radius
function radiusSearch({postfix= null, } = {}) {

    if (userLat == 0 && userLng == 0) {
        document.getElementById('radiusValue' + postfix).innerHTML = "0 m";
        document.getElementById('inputRadius' + postfix).value = 0;
        return Swal.fire('Determine your position first!');
    }

    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');
    closeNearby();

    let pos = new google.maps.LatLng(currentLat, currentLng);
    let radiusValue = parseFloat(document.getElementById('inputRadius' + postfix).value) * 100;
    map.panTo(pos);

    // find object in radius
    if (postfix === 'RG') {
        $.ajax({
            url: baseUrl + '/api/rumahGadang/findByRadius',
            type: 'POST',
            data: {
                lat: currentLat,
                long: currentLng,
                radius: radiusValue
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                drawRadius(pos, radiusValue);
            }
        });
    } else if (postfix === 'EV') {
        $.ajax({
            url: baseUrl + '/api/event/findByRadius',
            type: 'POST',
            data: {
                lat: currentLat,
                long: currentLng,
                radius: radiusValue
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                drawRadius(pos, radiusValue);
            }
        });
    } else if (postfix === 'UP') {
        $.ajax({
            url: baseUrl + '/api/uniquePlace/findByRadius',
            type: 'POST',
            data: {
                lat: currentLat,
                long: currentLng,
                radius: radiusValue
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                drawRadius(pos, radiusValue);
            }
        });
    }

}

// pan to selected object
function focusObject(id) {
    google.maps.event.trigger(markerArray[id], 'click');
    map.panTo(markerArray[id].getPosition())
}

// display objects by feature used
function displayFoundObject(response) {
    $('#table-data').empty();
    let data = response.data;
    let counter = 1;
    const months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ];
    for (i in data) {
        let item = data[i];
        let row;
        if (item.hasOwnProperty('date_next')){
            let date_next = new Date(item.date_start);
            let next = date_next.getDate() + ' ' + months[date_next.getMonth()] + ' ' + date_next.getFullYear();
            row =
                '<tr>'+
                '<td>'+ counter +'</td>' +
                '<td class="fw-bold">'+ item.name +'<br><span class="text-muted">' + next + '</span></td>' +
                '<td>'+
                '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Info" class="btn icon btn-primary mx-1" onclick="focusObject(`'+ item.id +'`);">' +
                '<span class="material-symbols-outlined">info</span>' +
                '</a>' +
                '</td>'+
                '</tr>';
        } else {
            row =
                '<tr>'+
                '<td>'+ counter +'</td>' +
                '<td class="fw-bold">'+ item.name +'</td>' +
                '<td>'+
                '<a data-bs-toggle="tooltip" data-bs-placement="bottom" title="More Info" class="btn icon btn-primary mx-1" onclick="focusObject(`'+ item.id +'`);">' +
                '<span class="material-symbols-outlined">info</span>' +
                '</a>' +
                '</td>'+
                '</tr>';
        }
        $('#table-data').append(row);
        objectMarker(item.id, item.lat, item.lng);
        counter++;
    }
}

// display steps of direction to selected route
function showSteps(directionResult) {
    $('#direction-row').show();
    $('#table-direction').empty();
    let myRoute = directionResult.routes[0].legs[0];
    for (let i = 0; i < myRoute.steps.length; i++) {
        let distance = myRoute.steps[i].distance.value;
        let instruction = myRoute.steps[i].instructions;
        let row =
            '<tr>' +
            '<td>'+ distance.toLocaleString("id-ID") +'</td>' +
            '<td>'+ instruction +'</td>' +
            '</tr>';
        $('#table-direction').append(row);
    }
}

// close nearby search section
function closeNearby() {
    $('#direction-row').hide();
    $('#check-nearby-col').hide();
    $('#result-nearby-col').hide();
    $('#list-rec-col').show();
    $('#list-rg-col').show();
    $('#list-ev-col').show();
    $('#list-up-col').show();
}

// open nearby search section
function openNearby(id, lat, lng) {
    $('#list-rg-col').hide();
    $('#list-ev-col').hide();
    $('#list-up-col').hide();
    $('#list-rec-col').hide();
    $('#check-nearby-col').show();

    currentLat = lat;
    currentLng = lng;
    let pos = new google.maps.LatLng(currentLat, currentLng);
    map.panTo(pos);

    document.getElementById('inputRadiusNearby').setAttribute('onchange', 'updateRadius("Nearby"); checkNearby("'+id+'")');
}

// Search Result Object Around
function checkNearby(id) {
    clearRadius();
    clearRoute();
    clearMarker();
    clearUser();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click')

    objectMarker(id, currentLat, currentLng, false);

    $('#table-cp').empty();
    $('#table-wp').empty();
    $('#table-sp').empty();
    $('#table-at').empty();
    $('#table-cp').hide();
    $('#table-wp').hide();
    $('#table-sp').hide();
    $('#table-at').hide();


    let radiusValue = parseFloat(document.getElementById('inputRadiusNearby').value) * 100;
    const checkCP = document.getElementById('check-cp').checked;
    const checkWP = document.getElementById('check-wp').checked;
    const checkSP = document.getElementById('check-sp').checked;
    const checkAT = document.getElementById('check-at').checked;

    if (!checkCP && !checkWP && !checkSP && !checkAT) {
        document.getElementById('radiusValueNearby').innerHTML = "0 m";
        document.getElementById('inputRadiusNearby').value = 0;
        return Swal.fire('Please choose one object');
    }

    if (checkCP) {
        findNearby('cp', radiusValue);
        $('#table-cp').show();
    }
    if (checkWP) {
        findNearby('wp', radiusValue);
        $('#table-wp').show();
    }
    if (checkSP) {
        findNearby('sp', radiusValue);
        $('#table-sp').show();
    }
    if (checkAT) {
        findNearby('at', radiusValue);
        $('#table-at').show();
    }
    drawRadius(new google.maps.LatLng(currentLat, currentLng), radiusValue);
    $('#result-nearby-col').show();
}

// Fetch object nearby by category
function findNearby(category, radius) {
    let pos = new google.maps.LatLng(currentLat, currentLng);
    if (category === 'cp') {
    
        $.ajax({
            url: baseUrl + '/api/culinaryPlace/findByRadius',
            type: 'POST',
            data: {
                lat: currentLat,
                long: currentLng,
                radius: radius
            },
            dataType: 'json',
            success: function (response) {
                displayNearbyResult(category, response);
            }
        });
    } else if (category === 'wp') {
        $.ajax({
            url: baseUrl + '/api/worshipPlace/findByRadius',
            type: 'POST',
            data: {
                lat: currentLat,
                long: currentLng,
                radius: radius
            },
            dataType: 'json',
            success: function (response) {
                displayNearbyResult(category, response);
            }
        });
    } else if (category === 'sp') {
        $.ajax({
            url: baseUrl + '/api/souvenirPlace/findByRadius',
            type: 'POST',
            data: {
                lat: currentLat,
                long: currentLng,
                radius: radius
            },
            dataType: 'json',
            success: function (response) {
                displayNearbyResult(category, response);
            }
        });
    }else if (category === 'at') {

        $.ajax({
            url: baseUrl + '/api/atraction/findByRadius',
            type: 'POST',
            data: {
                lat: currentLat,
                long: currentLng,
                radius: radius
            },
            dataType: 'json',
            success: function (response) {
                displayNearbyResult(category, response);
            }
        });
    } 
}

// Add nearby object to corresponding table
function displayNearbyResult(category, response) {
    let data = response.data;
    let headerName;
    if (category === 'cp') {
       
        headerName = 'Culinary';
    } else if (category === 'wp') {
        headerName = 'Worship';
    } else if (category === 'sp') {
        headerName = 'Souvenir'
    } else if (category === 'at') {
        headerName = 'Atraction'
    }
    let table =
        '<thead><tr>' +
        '<th>'+ headerName +' Name</th>' +
        '<th>Action</th>' +
        '</tr></thead>' +
        '<tbody id="data-'+category+'">' +
        '</tbody>';
    $('#table-'+category).append(table);

    for (i in data) {
        let item = data[i];
        let row =
            '<tr>'+
            '<td class="fw-bold">'+ item.name +'</td>' +
            '<td>'+
            '<a title="Route" class="btn icon btn-primary mx-1" onclick="routeTo('+item.lat+', '+item.lng+', false)"><i class="fa-solid fa-road"></i></a>' +
            '<a title="Info" class="btn icon btn-primary mx-1" onclick="infoModal(`'+ item.id +'`)"><i class="fa-solid fa-info"></i></a>' +
            '<a title="Location" class="btn icon btn-primary mx-1" onclick="focusObject(`'+ item.id +'`);"><i class="fa-solid fa-location-dot"></i></a>' +
            '</td>'+
            '</tr>';
        $('#data-'+category).append(row);
        objectMarker(item.id, item.lat, item.lng);
    }
}

// Show modal for object
function infoModal(id) {
    let title, content;
    if (id.substring(0,1) === "C") {
        $.ajax({
            url: baseUrl + '/api/culinaryPlace/' + id,
            dataType: 'json',
            success: function (response) {
                let item = response.data;
                let open = item.open.substring(0, item.open.length - 3);
                let close = item.close.substring(0, item.close.length - 3);

                title = '<h3>'+item.name+'</h3>';
                content =
                    '<div class="text-start">'+
                    '<p><span class="fw-bold">Address</span>: '+ item.address +'</p>'+
                    '<p><span class="fw-bold">Open</span>: '+ open +' - '+ close+' WIB</p>'+
                    '<p><span class="fw-bold">Contact Person:</span> '+ item.contact_person+'</p>'+
                    '</div>'+
                    '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
                    '<ol class="carousel-indicators">' +
                    '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
                    '</ol><div class="carousel-inner">' +
                    '<div class="carousel-item active">' +
                    '<img src="/media/photos/'+item.gallery[0]+'" alt="'+ item.name +'" class="w-50" alt="'+item.name+'">' +
                    '</div></div>' +
                    '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
                    '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Previous</span>' +
                    ' </a>' +
                    '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
                    '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Next</span>' +
                    '</a>' +
                    '</div>';

                Swal.fire({
                    title: title,
                    html: content,
                    width: '50%',
                    position: 'top'
                });
            }
        });
    } else if (id.substring(0,1) === "W") {
        $.ajax({
            url: baseUrl + '/api/worshipPlace/' + id,
            dataType: 'json',
            success: function (response) {
                let item = response.data;

                title = '<h3>'+item.name+'</h3>';
                content =
                    '<div class="text-start">'+
                    '<p><span class="fw-bold">Address</span>: '+ item.address +'</p>'+
                    '<p><span class="fw-bold">Park Area :</span> '+ item.parking_area+' m<sup>2</sup></p>'+
                    '<p><span class="fw-bold">Building Area</span>: '+ item.building_area+' m<sup>2</sup></p>'+
                    '<p><span class="fw-bold">Capacity</span>: '+ item.capacity+'</p>'+
                    '</div>' +
                    '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
                    '<ol class="carousel-indicators">' +
                    '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
                    '</ol><div class="carousel-inner">' +
                    '<div class="carousel-item active">' +
                    '<img src="/media/photos/'+item.gallery[0]+'" alt="'+ item.name +'" class="w-50" alt="'+item.name+'">' +
                    '</div></div>' +
                    '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
                    '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Previous</span>' +
                    ' </a>' +
                    '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
                    '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Next</span>' +
                    '</a>' +
                    '</div>';

                Swal.fire({
                    title: title,
                    html: content,
                    width: '50%',
                    position: 'top'
                });
            }
        });
    } else if (id.substring(0,1) === "S") {
        $.ajax({
            url: baseUrl + '/api/souvenirPlace/' + id,
            dataType: 'json',
            success: function (response) {
                let item = response.data;
               
                let open = item.open.substring(0, item.open.length - 3);
                let close = item.close.substring(0, item.close.length - 3);

                title = '<h3>'+item.name+'</h3>';
                content =
                    '<div class="text-start">'+
                    '<p><span class="fw-bold">Address</span>: '+ item.address +'</p>'+
                    '<p><span class="fw-bold">Contact Person :</span> '+ item.contact_person+'</p>'+
                    '<p><span class="fw-bold">Open</span>: '+ open +' - '+ close+' WIB</p>'+
                    '</div>' +
                    '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
                    '<ol class="carousel-indicators">' +
                    '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
                    '</ol><div class="carousel-inner">' +
                    '<div class="carousel-item active">' +
                    '<img src="/media/photos/'+item.gallery[0]+'" alt="'+ item.name +'" class="w-50" alt="'+item.name+'">' +
                    '</div></div>' +
                    '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
                    '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Previous</span>' +
                    ' </a>' +
                    '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
                    '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Next</span>' +
                    '</a>' +
                    '</div>';

                Swal.fire({
                    title: title,
                    html: content,
                    width: '50%',
                    position: 'top'
                });
            }
        });
    }else if (id.substring(0,1) === "A") {
        $.ajax({
            url: baseUrl + '/api/atraction/' + id,
            dataType: 'json',
            success: function (response) {
                let item = response.data;
            
                let open = item.open.substring(0, item.open.length - 3);
                let close = item.close.substring(0, item.close.length - 3);

                title = '<h3>'+item.name+'</h3>';
                content =
                    '<div class="text-start">'+
                    '<p><span class="fw-bold">Address</span>: '+ item.address +'</p>'+
                    '<p><span class="fw-bold">Contact Person :</span> '+ item.contact_person+'</p>'+
                    '<p><span class="fw-bold">Open</span>: '+ open +' - '+ close+' WIB</p>'+
                    '</div>' +
                    '<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">' +
                    '<ol class="carousel-indicators">' +
                    '<li data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active"></li>' +
                    '</ol><div class="carousel-inner">' +
                    '<div class="carousel-item active">' +
                    '<img src="/media/photos/'+item.gallery[0]+'" alt="'+ item.name +'" class="w-50" alt="'+item.name+'">' +
                    '</div></div>' +
                    '<a style="color: #000" class="carousel-control-prev" href="#carouselExampleControls" role="button" data-bs-slide="prev">\n' +
                    '<i class="fa-solid fa-angle-left" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Previous</span>' +
                    ' </a>' +
                    '<a style="color: #000" class="carousel-control-next" href="#carouselExampleControls" role="button" data-bs-slide="next">' +
                    '<i class="fa-solid fa-angle-right" aria-hidden="true"></i>' +
                    '<span class="visually-hidden">Next</span>' +
                    '</a>' +
                    '</div>';

                Swal.fire({
                    title: title,
                    html: content,
                    width: '50%',
                    position: 'top'
                });
            }
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
    google.maps.event.clearListeners(map, 'click');
    closeNearby();

    let name;
    if (category === 'RG') {
        name = document.getElementById('nameRG').value;
        $.ajax({
            url: baseUrl + '/api/rumahGadang/findByName',
            type: 'POST',
            data: {
                name: name,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    } else if (category === 'EV') {
        name = document.getElementById('nameEV').value;
        $.ajax({
            url: baseUrl + '/api/event/findByName',
            type: 'POST',
            data: {
                name: name,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    } else if (category === 'UP') {
        name = document.getElementById('nameUP').value;
        $.ajax({
            url: baseUrl + '/api/uniquePlace/findByName',
            type: 'POST',
            data: {
                name: name,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    }
}

// Get list of Rumah Gadang facilities
function getFacility() {
    let facility;
    $('#facilitySelect').empty()
    $.ajax({
        url: baseUrl + '/api/facility',
        dataType: 'json',
        success: function (response) {
            let data = response.data;
            for (i in data) {
                let item = data[i];
                facility =
                    '<option value="'+ item.id +'">'+ item.facility +'</option>';
                $('#facilitySelect').append(facility);
            }
        }
    });
}

// Find object by Facility
function findByFacility() {
    clearRadius();
    clearRoute();
    clearMarker();
    clearUser();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');
    closeNearby();

    let facility = document.getElementById('facilitySelect').value;
    $.ajax({
        url: baseUrl + '/api/rumahGadang/findByFacility',
        type: 'POST',
        data: {
            facility: facility,
        },
        dataType: 'json',
        success: function (response) {
            displayFoundObject(response);
            boundToObject();
        }
    });
}

// Set star by user input
function setStar(star) {
    switch (star) {
        case 'star-1' :
            $("#star-1").addClass('star-checked');
            $("#star-2,#star-3,#star-4,#star-5").removeClass('star-checked');
            document.getElementById('star-rating').value = '1';
            break;
        case 'star-2' :
            $("#star-1,#star-2").addClass('star-checked');
            $("#star-3,#star-4,#star-5").removeClass('star-checked');
            document.getElementById('star-rating').value = '2';
            break;
        case 'star-3' :
            $("#star-1,#star-2,#star-3").addClass('star-checked');
            $("#star-4,#star-5").removeClass('star-checked');
            document.getElementById('star-rating').value = '3';
            break;
        case 'star-4' :
            $("#star-1,#star-2,#star-3,#star-4").addClass('star-checked');
            $("#star-5").removeClass('star-checked');
            document.getElementById('star-rating').value = '4';
            break;
        case 'star-5' :
            $("#star-1,#star-2,#star-3,#star-4,#star-5").addClass('star-checked');
            document.getElementById('star-rating').value = '5';
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
    google.maps.event.clearListeners(map, 'click');
    closeNearby();

    let rating = document.getElementById('star-rating').value;
    if (category === 'RG') {
        $.ajax({
            url: baseUrl + '/api/rumahGadang/findByRating',
            type: 'POST',
            data: {
                rating: rating,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    } else if (category === 'EV') {
        $.ajax({
            url: baseUrl + '/api/event/findByRating',
            type: 'POST',
            data: {
                rating: rating,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    } else if (category === 'UP') {
        $.ajax({
            url: baseUrl + '/api/uniquePlace/findByRating',
            type: 'POST',
            data: {
                rating: rating,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
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
    google.maps.event.clearListeners(map, 'click');
    closeNearby();

    if (object === 'RG') {
        let category = document.getElementById('categoryRGSelect').value;
        $.ajax({
            url: baseUrl + '/api/rumahGadang/findByCategory',
            type: 'POST',
            data: {
                category: category,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    } else if (object === 'EV') {
        let category = document.getElementById('categoryEVSelect').value;
        $.ajax({
            url: baseUrl + '/api/event/findByCategory',
            type: 'POST',
            data: {
                category: category,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    } else if (object === 'UP') {
        let category = document.getElementById('categoryUPSelect').value;
        $.ajax({
            url: baseUrl + '/api/uniquePlace/findByCategory',
            type: 'POST',
            data: {
                category: category,
            },
            dataType: 'json',
            success: function (response) {
                displayFoundObject(response);
                boundToObject();
            }
        });
    }
}

// Get list of Event category
function getCategory() {
    let category;
    $('#categoryEVSelect').empty()
    $.ajax({
        url: baseUrl + '/api/event/category',
        dataType: 'json',
        success: function (response) {
            let data = response.data;
            for (i in data) {
                let item = data[i];
                category =
                    '<option value="'+ item.id +'">'+ item.category +'</option>';
                $('#categoryEVSelect').append(category);
            }
        }
    });
}

// // Find object by Date
function findByDate() {
    clearRadius();
    clearRoute();
    clearMarker();
    clearUser();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');
    closeNearby();

    let eventDate = document.getElementById('eventDate').value;
    $.ajax({
        url: baseUrl + '/api/event/findByDate',
        type: 'POST',
        data: {
            date: eventDate,
        },
        dataType: 'json',
        success: function (response) {
            displayFoundObject(response);
            boundToObject();
        }
    });
}

// Create compass
function setCompass() {
    const compass = document.createElement("div");
    compass.setAttribute("id", "compass");
    const compassDiv = document.createElement("div");
    compass.appendChild(compassDiv);
    const compassImg = document.createElement("img");
    compassImg.src = baseUrl + '/media/icon/compass.png';
    compassDiv.appendChild(compassImg);

    map.controls[google.maps.ControlPosition.LEFT_BOTTOM].push(compass);
}

// Create legend
function getLegend() {
    const icons = {
        rg :{
            name: 'Rumah Gadang',
            icon: baseUrl + '/media/icon/marker_rg.png',
        },
        ev :{
            name: 'Event',
            icon: baseUrl + '/media/icon/marker_ev.png',
        },
        up :{
            name: 'Unique Place',
            icon: 'https://maps.gstatic.com/mapfiles/ms2/micons/red-dot.png',
        },
        cp :{
            name: 'Culinary Place',
            icon: baseUrl + '/media/icon/marker_cp.png',
        },
        wp :{
            name: 'Worship Place',
            icon: baseUrl + '/media/icon/marker_wp.png',
        },
        sp :{
            name: 'Souvenir Place',
            icon: baseUrl + '/media/icon/marker_sp.png',
        },
    }

    const title = '<p class="fw-bold fs-6">Legend</p>';
    $('#legend').append(title);

    for (key in icons) {
        const type = icons[key];
        const name = type.name;
        const icon = type.icon;
        const div = '<div><img src="' + icon + '"> ' + name +'</div>';

        $('#legend').append(div);
    }
    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
}

// toggle legend element
function viewLegend() {
    if ($('#legend').is(':hidden')) {
        $('#legend').show();
    } else {
        $('#legend').hide();
    }
}

// list object for new visit history
function getObjectByCategory(){
    const category = document.getElementById('category').value;
    $('#object').empty();
    if (category === 'None') {
        object =
            '<option value="None">Select Category First</option>';
        $('#object').append(object);
        return Swal.fire({
            icon: 'warning',
            title: 'Please Choose a Object Category!'
        });
    }
    if (category === '1') {
        $.ajax({
            url: baseUrl + '/api/rumahGadang',
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                for (i in data) {
                    let item = data[i];
                    object =
                        '<option value="'+ item.id +'">'+ item.name +'</option>';
                    $('#object').append(object);
                }
            }
        });
    } else if (category === '2') {
        $.ajax({
            url: baseUrl + '/api/event',
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                for (i in data) {
                    let item = data[i];
                    object =
                        '<option value="'+ item.id +'">'+ item.name +'</option>';
                    $('#object').append(object);
                }
            }
        });
    }
}

// Validate if star rating picked yet
function checkStar(event) {
    const star = document.getElementById('star-rating').value;
    if (star == '0') {
        event.preventDefault();
        Swal.fire('Please put rating star');
    }
}

// Check if Category and Object is chose correctly
function checkForm(event) {
    const category = document.getElementById('category').value;
    const object = document.getElementById('object').value;
    if (category === 'None' || object === 'None') {
        event.preventDefault();
        Swal.fire('Please select the correct Category and Object');
    }
}

// Update preview of uploaded photo profile
function showPreview(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#avatar-preview').attr('src', e.target.result).width(300).height(300);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Get list of Recommendation
function getRecommendation(id, recom) {
    let recommendation;
    $('#recommendationSelect' + id).empty()
    $.ajax({
        url: baseUrl + '/api/recommendationList',
        dataType: 'json',
        success: function (response) {
            let data = response.data;
            for (i in data) {
                let item = data[i];
                if (item.id == recom) {
                    recommendation =
                        '<option value="'+ item.id +'" selected>'+ item.name +'</option>';
                } else {
                    recommendation =
                        '<option value="'+ item.id +'">'+ item.name +'</option>';
                }
                $('#recommendationSelect' + id).append(recommendation);
            }
        }
    });
}

// Update option onclick function for updating Recommendation
function changeRecom(status = null) {
    if (status === 'edit') {
        $('#recomBtnEdit').hide();
        $('#recomBtnExit').show();
        console.log('entering edit mode');
        $('.recomSelect').on('change', updateRecom);
    } else {
        $('#recomBtnEdit').show();
        $('#recomBtnExit').hide();
        console.log('exiting edit mode');
        $('.recomSelect').off('change', updateRecom);
    }
}

// Update recommendation based on input User
function updateRecom() {
    let recom = $(this).find('option:selected').val();
    let id = $(this).attr('id');
    $.ajax({
        url: baseUrl + '/api/recommendation',
        type: 'POST',
        data: {
            id: id,
            recom: recom,
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === 201) {
                console.log('Success update recommendation @' + id + ':' + recom);
                Swal.fire('Success updating Rumah Gadang ID @' + id)
            }
        }
    });
}

// Set map to coordinate put by user
function findCoords(object) {
    clearMarker();
    google.maps.event.clearListeners(map, 'click');

    const lat = Number(document.getElementById('latitude').value);
    const lng = Number(document.getElementById('longitude').value);

    if (lat === 0 || lng === 0 || isNaN(lat) || isNaN(lng)) {
        return Swal.fire('Please input Lat and Long');
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
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('geo-json').value = '';
        document.getElementById('lat').value = '';
        document.getElementById('lng').value = '';
        clearMarker();
        selectedShape.setMap(null);
        setSelection(selectedShape)
        // To show:
        drawingManager.setOptions({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true
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
            drawingModes: [
                google.maps.drawing.OverlayType.POLYGON,
            ]
        },
        polygonOptions: {
            fillColor: 'blue',
            strokeColor: 'blue',
            editable: true,
        },
        map: map
    };
    drawingManager.setOptions(drawingManagerOpts);
    let newShape;

    if (!edit) {
        google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
            drawingManager.setOptions({
                drawingControl: false,
                drawingMode: null,
            });
            newShape = event.overlay;
            newShape.type = event.type;
            setSelection(newShape);
            saveSelection(newShape);

            google.maps.event.addListener(newShape, 'click', function() {
                setSelection(newShape);
            });
            google.maps.event.addListener(newShape.getPath(), 'insert_at', () => {
                saveSelection(newShape);
            });
            google.maps.event.addListener(newShape.getPath(), 'remove_at', () => {
                saveSelection(newShape);
            });
            google.maps.event.addListener(newShape.getPath(), 'set_at', () => {
                saveSelection(newShape);
            });
        });
    } else {
        drawingManager.setOptions({
            drawingControl: false,
            drawingMode: null,
        });

        newShape = drawGeom();
        newShape.type = 'polygon';
        setSelection(newShape);

        const paths = newShape.getPath().getArray();
        let bounds = new google.maps.LatLngBounds();
        for (let i = 0; i < paths.length; i++) {
            bounds.extend(paths[i])
        }
        let pos = bounds.getCenter();
        map.panTo(pos);

        clearMarker();
        let marker = new google.maps.Marker();
        markerOption = {
            position: pos,
            animation: google.maps.Animation.DROP,
            map: map,
        }
        marker.setOptions(markerOption);
        markerArray['newRG'] = marker;

         google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
            drawingManager.setOptions({
                drawingControl: false,
                drawingMode: null,
            });
            newShape = event.overlay;
            newShape.type = event.type;
            setSelection(newShape);
            saveSelection(newShape);

            google.maps.event.addListener(newShape, 'click', function() {
                setSelection(newShape);
            });
            google.maps.event.addListener(newShape.getPath(), 'insert_at', () => {
                saveSelection(newShape);
            });
            google.maps.event.addListener(newShape.getPath(), 'remove_at', () => {
                saveSelection(newShape);
            });
            google.maps.event.addListener(newShape.getPath(), 'set_at', () => {
                saveSelection(newShape);
            });
        });
    }

    google.maps.event.addListener(map, 'click', clearSelection);
    google.maps.event.addDomListener(document.getElementById('clear-drawing'), 'click', deleteSelectedShape);
}

// Get geoJSON of selected shape on map
function saveSelection(shape) {

    const paths = shape.getPath().getArray();
    let bounds = new google.maps.LatLngBounds();
    for (let i = 0; i < paths.length; i++) {
        bounds.extend(paths[i])
    }
    let pos = bounds.getCenter();
    map.panTo(pos);

    clearMarker();
    let marker = new google.maps.Marker();
    markerOption = {
        position: pos,
        animation: google.maps.Animation.DROP,
        map: map,
    }
    marker.setOptions(markerOption);
    markerArray['newRG'] = marker;

    document.getElementById('latitude').value = pos.lat().toFixed(8);
    document.getElementById('longitude').value = pos.lng().toFixed(8);
    document.getElementById('lat').value = pos.lat().toFixed(8);
    document.getElementById('lng').value = pos.lng().toFixed(8);

    const dataLayer = new google.maps.Data();
    dataLayer.add(new google.maps.Data.Feature({
        geometry: new google.maps.Data.Polygon([shape.getPath().getArray()])
    }));
    dataLayer.toGeoJson(function (object) {
        document.getElementById('geo-json').value = JSON.stringify(object.features[0].geometry);
    });

}

// Get list of users
function getListUsers(owner) {
    let users;
    $('#ownerSelect').empty()
    $.ajax({
        url: baseUrl + '/api/owner',
        dataType: 'json',
        success: function (response) {
            let data = response.data;
            console.log(data)
            for (i in data) {
                let item = data[i];
                if (item.id == owner) {
                    users =
                        '<option value="'+ item.id +'" selected>'+ item.first_name +' ' + item.last_name +'</option>';
                } else {
                    users =
                        '<option value="'+ item.id +'">'+ item.first_name +' ' + item.last_name +'</option>';
                }
                $('#ownerSelect').append(users);
            }
        }
    });
}

// Draw current GeoJSON on drawing manager
function drawGeom() {
    const geoJSON = $('#geo-json').val();
    if (geoJSON !== '') {
        const geoObj = JSON.parse(geoJSON);
        const coords = geoObj.coordinates[0];
        let polygonCoords = []
        for (i in coords) {
            polygonCoords.push(
                {lat: coords[i][1], lng: coords[i][0]}
            );
        }
        const polygon = new google.maps.Polygon({
            paths: polygonCoords,
            fillColor: 'blue',
            strokeColor: 'blue',
            editable: true,
        });
        polygon.setMap(map);
        return polygon;
    }
}

// Delete selected object
function deleteObject(id = null, name = null, user = false) {
    
    if (id === null) {
        return Swal.fire('ID cannot be null');
    }

    let content, apiUri;
    if (id.substring(0,1) === 'R') {
        content = 'Rumah Gadang';
        apiUri = 'rumahGadang/';
    } else if(id.substring(0,2) === 'AF') {
        content = 'Atraction Facility';
        apiUri = 'atractionFacility/'
    } else if (id.substring(0,1) === 'A') {
        content = 'Atraction';
        apiUri = 'atraction/'
    } else if (id.substring(0,1) === 'E') {
        content = 'Event';
        apiUri = 'event/'
    } else if (id.substring(0,2) === 'UF') {
        content = 'Unit Facility';
        apiUri = 'homestayUnitFacility/'
    } else if (id.substring(0,1) === 'U') {
        content = 'Unique Place';
        apiUri = 'uniquePlace/'
    }else if (id.substring(0,2) === 'PT') {
        content = 'Package Type';
        apiUri = 'packageType/'
    } else if (id.substring(0,1) === 'P') {
        content = 'Tourism Package';
        apiUri = 'package/'
    } else if (id.substring(0,2) === 'SP') {
        content = 'Service';
        apiUri = 'service/'
    } else if (id.substring(0,2) === 'HF') {
        content = 'Homestay Facility';
        apiUri = 'homestayFacility/'
    } else if (id.substring(0,2) === 'HU') {
        content = 'Homestay Unit';
        apiUri = 'homestayUnit/'
    } else if (id.substring(0,1) === 'H') {
        content = 'Homestay';
        apiUri = 'homestay/'
    } else if (user === true) {
        content = 'User';
        apiUri = 'user/'
    } else  {
        content = 'Facility';
        apiUri = 'facility/'
    }
    
    Swal.fire({
        title: 'Delete ' + content + '?',
        text: 'You are about to remove ' + name,
        icon: 'warning',
        showCancelButton: true,
        denyButtonText: 'Delete',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#343a40',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: baseUrl + '/api/' + apiUri + id,
                type: 'DELETE',
                dataType: 'json',
                success: function (response) {
                    console.log(response)
                    if (response.status === 200) {
                        Swal.fire('Deleted!', 'Successfully remove ' + name, 'success').then((result) => {
                            if(result.isConfirmed) {
                                document.location.reload();
                            }
                        });

                    } else {
                        Swal.fire('Failed', 'Delete ' + name + ' failed!', 'warning');
                    }
                }
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

    setUserLoc(pos.lat().toFixed(8), pos.lng().toFixed(8))

}

// Pan map to user position
function panToUser() {
    if (userLat == 0 && userLng == 0) {
        return Swal.fire('Determine your position first!');
    }
    let pos = new google.maps.LatLng(userLat, userLng);
    map.panTo(pos);
}

// Find RG on mobile
function findRG(name = null){
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    currentUrl = 'mobile'
    $.ajax({
        url: baseUrl + '/api/rumahGadang/findByName',
        type: 'POST',
        data: {
            name: name,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}

// Find RG by Rating on Mobile
function findByRatingRG(rating) {
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    currentUrl = 'mobile'
    $.ajax({
        url: baseUrl + '/api/rumahGadang/findByRating',
        type: 'POST',
        data: {
            rating: rating,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}

// Find object by Facility on Mobile
function findByFacilityRG(facility) {
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    currentUrl = 'mobile'
    $.ajax({
        url: baseUrl + '/api/rumahGadang/findByFacility',
        type: 'POST',
        data: {
            facility: facility,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}

// Find RG by Category on Mobile
function findByCategoryRG(category) {
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    currentUrl = 'mobile'
    $.ajax({
        url: baseUrl + '/api/rumahGadang/findByCategory',
        type: 'POST',
        data: {
            category: category,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}

// Find EV on mobile
function findEV(name = null){
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    currentUrl = 'mobile'
    $.ajax({
        url: baseUrl + '/api/event/findByName',
        type: 'POST',
        data: {
            name: name,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}

// Find EV by Rating on Mobile
function findByRatingEV(rating) {
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    currentUrl = 'mobile'
    $.ajax({
        url: baseUrl + '/api/event/findByRating',
        type: 'POST',
        data: {
            rating: rating,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}


// Find EV by Category on Mobile
function findByCategoryEV(category) {
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    currentUrl = 'mobile'
    $.ajax({
        url: baseUrl + '/api/event/findByCategory',
        type: 'POST',
        data: {
            category: category,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}

// // Find EV by Date
function findByDateEV(eventDate) {
    clearRadius();
    clearRoute();
    clearMarker();
    destinationMarker.setMap(null);
    google.maps.event.clearListeners(map, 'click');

    $.ajax({
        url: baseUrl + '/api/event/findByDate',
        type: 'POST',
        data: {
            date: eventDate,
        },
        dataType: 'json',
        success: function (response) {
            let data = response.data
            for (i in data) {
                let item = data[i]
                currentUrl = currentUrl + item.id
                objectMarker(item.id, item.lat, item.lng);
            }
            boundToObject();
        }
    });
}

let geomSumbar = {
    type: 'Feature',
    geometry: {
        "type": "MultiPolygon",
        "coordinates": [
            [
                [
                    [99.16198, 0.231812],
                    [99.235527, 0.327712],
                    [99.297096, 0.337691],
                    [99.331474, 0.362899],
                    [99.407265, 0.493468],
                    [99.499702, 0.538817],
                    [99.587463, 0.516625],
                    [99.640007, 0.547226],
                    [99.670876, 0.481021],
                    [99.716553, 0.454064],
                    [99.765022, 0.481917],
                    [99.82309, 0.461878],
                    [99.908188, 0.474805],
                    [99.964767, 0.554445],
                    [99.919365, 0.605901],
                    [99.914688, 0.680979],
                    [99.845169, 0.686254],
                    [99.845215, 0.83873],
                    [99.835236, 0.889678],
                    [99.881943, 0.90572],
                    [99.957077, 0.875112],
                    [100.083336, 0.791869],
                    [100.183716, 0.761895],
                    [100.25573, 0.709395],
                    [100.205925, 0.593234],
                    [100.219559, 0.524918],
                    [100.282326, 0.408069],
                    [100.281677, 0.332958],
                    [100.361946, 0.354324],
                    [100.394623, 0.339763],
                    [100.43718, 0.247852],
                    [100.464088, 0.222191],
                    [100.605743, 0.183473],
                    [100.687881, 0.177849],
                    [100.727119, 0.193165],
                    [100.778122, 0.169268],
                    [100.792191, 0.120829],
                    [100.753342, 0.048535],
                    [100.770103, -0.033094],
                    [100.748398, -0.065219],
                    [100.781006, -0.23042],
                    [100.838776, -0.290596],
                    [100.833672, -0.318377],
                    [100.910423, -0.370743],
                    [100.94133, -0.312437],
                    [100.996368, -0.314363],
                    [101.031708, -0.340868],
                    [101.024826, -0.398442],
                    [101.06768, -0.498227],
                    [101.181267, -0.549494],
                    [101.256569, -0.626754],
                    [101.359833, -0.659254],
                    [101.407272, -0.736848],
                    [101.500015, -0.807856],
                    [101.595917, -0.8512],
                    [101.6539, -0.935173],
                    [101.747887, -0.967631],
                    [101.821533, -0.977189],
                    [101.830315, -1.10193],
                    [101.878525, -1.110225],
                    [101.858885, -1.158166],
                    [101.815407, -1.209807],
                    [101.709229, -1.237597],
                    [101.689585, -1.295043],
                    [101.719172, -1.344921],
                    [101.691318, -1.397239],
                    [101.720899, -1.42113],
                    [101.694035, -1.480637],
                    [101.567154, -1.572305],
                    [101.526581, -1.616804],
                    [101.492464, -1.681019],
                    [101.431198, -1.696775],
                    [101.415512, -1.671203],
                    [101.319626, -1.693206],
                    [101.258324, -1.693337],
                    [101.21579, -1.714355],
                    [101.18763, -1.677177],
                    [101.125816, -1.679094],
                    [101.124443, -1.78474],
                    [101.144699, -1.917723],
                    [101.194092, -1.989151],
                    [101.243668, -2.021798],
                    [101.291611, -2.133106],
                    [101.306076, -2.23891],
                    [101.294159, -2.280203],
                    [101.244644, -2.344946],
                    [101.155373, -2.390157],
                    [101.027755, -2.482182],
                    [100.992081, -2.431542],
                    [100.897308, -2.337769],
                    [100.877205, -2.247514],
                    [100.831245, -2.188847],
                    [100.82914, -2.147044],
                    [100.869568, -2.104535],
                    [100.884331, -2.042843],
                    [100.858192, -1.919218],
                    [100.78273, -1.819229],
                    [100.684204, -1.661846],
                    [100.64151, -1.618727],
                    [100.633499, -1.562898],
                    [100.555435, -1.403785],
                    [100.576469, -1.376961],
                    [100.544067, -1.308304],
                    [100.492241, -1.299992],
                    [100.458786, -1.26039],
                    [100.402206, -1.268252],
                    [100.417633, -1.200178],
                    [100.382729, -1.178114],
                    [100.410194, -1.037552],
                    [100.364952, -0.995286],
                    [100.342545, -0.882115],
                    [100.269402, -0.782622],
                    [100.117134, -0.631379],
                    [100.080696, -0.549656],
                    [99.971771, -0.443441],
                    [99.918625, -0.404366],
                    [99.886421, -0.349305],
                    [99.820641, -0.306837],
                    [99.806374, -0.245461],
                    [99.750984, -0.153287],
                    [99.765541, -0.065272],
                    [99.735329, -0.027319],
                    [99.610626, 0.069696],
                    [99.471992, 0.134715],
                    [99.394257, 0.152445],
                    [99.353981, 0.230505],
                    [99.312424, 0.238678],
                    [99.253555, 0.211412],
                    [99.16198, 0.231812]
                ],
                [
                    [100.148926, -0.294198],
                    [100.165466, -0.310519],
                    [100.165375, -0.388144],
                    [100.218018, -0.396025],
                    [100.226418, -0.288869],
                    [100.194832, -0.252879],
                    [100.148926, -0.294198]
                ],
                [
                    [100.500008, -0.534535],
                    [100.487389, -0.577913],
                    [100.540764, -0.666713],
                    [100.59713, -0.680994],
                    [100.544914, -0.55475],
                    [100.500008, -0.534535]
                ]
            ],
            [
                [
                    [99.263031, -1.718946],
                    [99.241554, -1.791817],
                    [99.170258, -1.775342],
                    [99.104195, -1.809051],
                    [99.009583, -1.762335],
                    [98.872444, -1.675564],
                    [98.81971, -1.61324],
                    [98.831772, -1.58409],
                    [98.798141, -1.519863],
                    [98.686104, -1.357201],
                    [98.628815, -1.283929],
                    [98.597008, -1.222869],
                    [98.64505, -1.10431],
                    [98.65641, -0.979386],
                    [98.715179, -0.94198],
                    [98.786461, -0.954942],
                    [98.905327, -0.907312],
                    [98.901535, -0.943195],
                    [98.930374, -1.056429],
                    [98.997421, -1.109993],
                    [99.003639, -1.159117],
                    [99.029007, -1.183758],
                    [99.069817, -1.264657],
                    [99.095047, -1.360714],
                    [99.162605, -1.427637],
                    [99.159073, -1.492668],
                    [99.192131, -1.505583],
                    [99.193535, -1.554393],
                    [99.22258, -1.605247],
                    [99.258133, -1.590373],
                    [99.280853, -1.628746],
                    [99.263031, -1.718946]
                ]
            ],
            [
                [
                    [100.432472, -3.000107],
                    [100.465523, -3.033659],
                    [100.468422, -3.144241],
                    [100.394447, -3.149384],
                    [100.389984, -3.197419],
                    [100.475174, -3.334633],
                    [100.421425, -3.310643],
                    [100.342789, -3.24575],
                    [100.325836, -3.212034],
                    [100.332291, -3.129642],
                    [100.296883, -3.080192],
                    [100.228333, -3.045524],
                    [100.186508, -2.986326],
                    [100.200531, -2.933076],
                    [100.17469, -2.801497],
                    [100.23597, -2.778423],
                    [100.261101, -2.820208],
                    [100.354225, -2.895449],
                    [100.432472, -3.000107]
                ]
            ],
            [
                [
                    [99.995766, -2.749434],
                    [100.004059, -2.723478],
                    [99.970131, -2.509001],
                    [99.994774, -2.498358],
                    [100.070412, -2.575727],
                    [100.168579, -2.640264],
                    [100.221405, -2.740431],
                    [100.11644, -2.831824],
                    [100.015343, -2.805559],
                    [99.995766, -2.749434]
                ]
            ],
            [
                [
                    [99.822586, -2.306719],
                    [99.833534, -2.360941],
                    [99.793526, -2.368245],
                    [99.74221, -2.344413],
                    [99.673538, -2.286497],
                    [99.598953, -2.281999],
                    [99.52739, -2.152481],
                    [99.571342, -2.143682],
                    [99.545197, -2.067696],
                    [99.565895, -2.036215],
                    [99.618942, -2.025629],
                    [99.689621, -2.074452],
                    [99.710625, -2.154402],
                    [99.783386, -2.28467],
                    [99.822586, -2.306719]
                ]
            ]
        ]
    }
}