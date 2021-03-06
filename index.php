<?php include $_SERVER['DOCUMENT_ROOT'] . '/Scripts/sqlfunc.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Seize</title>
        <meta charset="UTF-8">
        <meta name="description" content="TODO">
        <meta name="keywords" content="TODO">
        <meta name="author" content="TODO">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="/Styles/styles.css">
        <link rel="shortcut icon" type="image/png" href="/Styles/favico.png"/>
    </head>
    <body>
        <ul id="nav">
            <li class="navitem">
                <a href="/">Home</a>
            </li>
            <li class="navitem">
                <a href="/About">About</a>
            </li>
        </ul>
        <div id="map"></div>
        <div id="footer"><p>Each point on the map is someone experiencing a medical emergency who will be receiving help immediately.</p></div>
    </body>
    <script>
        // Create variables.
        var map;
        var directionsService;
        // Create an empty array to use as markers on the map.
        var patients = [];
        var markers = [];
        var j;
        // Draw the map in the div element centered on Davis, CA.
        function initMap() {
            // Create a directions service.
            var directionsService = new google.maps.DirectionsService();
            var map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: {lat: 38.545, lng: -121.741},
                gestureHandling: "cooperative",
                streetViewControl: false
            });
            // Create a places service.
            var placesService = new google.maps.places.PlacesService(map);
            // Draw each marker on the map.
            for(var i=0; i<patients.length; i++) {
                j = i;
                markers.push(new google.maps.Marker({
                    position: {lat: patients[i].lat, lng: patients[i].lon},
                    map: map,
                    title: patients[i].name,
                    clickable: true
                }));
                // Add an event listener for clicking on each marker.
                // Clicking on the marker will take you to the patients page.
                (function () {
                    var id = patients[markers.length-1].id;
                    markers[markers.length-1].addListener("click", function() { window.location.href = "/Patient?id="+id; }, false);
                }()); // Immediate invocation.
                // Search for the nearest hospital and draw a new direction.
                (function () {
                    // Search for the nearest hospital.
                    placesService.nearbySearch({
                        location: {lat: patients[i].lat, lng: patients[i].lon},
                        type: "hospital",
                        rankBy: google.maps.places.RankBy.DISTANCE
                    }, finishsearch);
                }()); // Immediate invocation.
            }
        }
        
        function finishsearch(results, status) {
            if (status == google.maps.places.PlacesServiceStatus.OK) {
                if(results.length > 0) {
                    var nearest = results[0].geometry.location;
                    // Draw a new route.
                    var directionsDisplay = new google.maps.DirectionsRenderer({
                        map: map,
                        markerOptions: {visible: false}
                    });
                    var request = {
                        origin: {lat: patients[j].lat, lng: patients[j].lon},
                        destination: nearest,
                        travelMode: 'DRIVING'
                    };
                    directionsService.route(request, function(result, status) {
                        if (status == 'OK') {
                            directionsDisplay.setDirections(result);
                        }
                    });
                    return;
                }
                console.error("No locations found.");
            }
            console.error("Error in Google Places API.");
        }

        function patient(id, name, lat, lon) {
            this.id = id;
            this.name = name;
            this.lat = lat;
            this.lon = lon;
        }
    </script>
    <?php
        echo '<script>';
        echo 'function addPatients() {';
        $result = query('SELECT id, name, latitude, longitude FROM `'.$dbname.'`.`patients` WHERE hide = 0;');
        while($row = mysqli_fetch_assoc($result)) {
            echo 'patients.push(new patient("'.$row['id'].'", "'.$row['name'].'", '.$row['latitude'].', '.$row['longitude'].'));';
        }
        echo '}'; // function addMarkers
        echo 'addPatients();';
        echo '</script>';
    ?>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBw8BY4jfE9IdMdfYwoYOd8KiNN4Fexf9Q&libraries=places&callback=initMap"></script>
</html>