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
    </body>
    <script>
        // Create an empty array to use as markers on the map.
        var patients = [];
        var markers = [];
        // Draw the map in the div element centered on Davis, CA.
        function initMap() {
            var map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: {lat: 38.545, lng: -121.741},
                gestureHandling: "cooperative",
                streetViewControl: false
            });
            // Draw each marker on the map.
            for(var i=0; i<patients.length; i++) {
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
            }
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
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBw8BY4jfE9IdMdfYwoYOd8KiNN4Fexf9Q&callback=initMap"></script>
</html>