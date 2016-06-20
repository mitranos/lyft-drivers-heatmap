<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>Googlemaps Heatmap Layer</title>
    <style>
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0; font-family:sans-serif; }
        #map-canvas { height: 100% }
        h1 { position:absolute; background:black; color:white; padding:10px; font-weight:200; z-index:10000;}
        #all-examples-info { position:absolute; background:white; font-size:16px; padding:20px; bottom:20px; width:350px; line-height:150%; border:1px solid rgba(0,0,0,.2);}
    </style>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="{{ url('/js/heatmap.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/gmaps-heatmap.js') }}"></script>
</head>
<body>
<h1>Lyft Drivers Heatmap</h1>
<div id="map-canvas"></div>
<script>
    // map center
    var myLatlng = new google.maps.LatLng(25.972687, -80.232468);
    // map options,
    var myOptions = {
        zoom: 11,
        center: myLatlng
    };
    // standard map
    map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
    // heatmap layer
    heatmap = new HeatmapOverlay(map,
            {
                // radius should be small ONLY if scaleRadius is true (or small radius is intended)
                "radius": 0.002,
                "maxOpacity": 1,
                // scales the radius based on map zoom
                "scaleRadius": true,
                // if set to false the heatmap uses the global maximum for colorization
                // if activated: uses the data maximum within the current map boundaries
                //   (there will always be a red spot with useLocalExtremas true)
                "useLocalExtrema": true,
                // which field name in your data represents the latitude - default "lat"
                latField: 'lat',
                // which field name in your data represents the longitude - default "lng"
                lngField: 'lng',
                // which field name in your data represents the data value - default "value"
                valueField: 'count'
            }
    );

    var testData = {
        max: 100,
        data: <?php echo $locations; ?>
    };


    heatmap.setData(testData);

</script>
</body>
</html>