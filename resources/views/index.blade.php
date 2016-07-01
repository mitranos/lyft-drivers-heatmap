<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>Lyft Drivers Heatmap</title>
    <link rel="stylesheet" type="text/css" href="{{ url('/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('/css/bootstrap-slider.min.css') }}">
    <style>
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0; font-family:sans-serif; }
        #map-canvas { height: 100% }
        h1 { position:absolute; background:black; color:#FF00BF; padding:10px; font-weight:200; z-index:10000; margin-top: 0px}
        h3 { position:absolute; background:black; color:#FF00BF; padding:10px; font-weight:200; z-index:10000; margin-top: 55px}
        h5 { position:absolute; background:black; color:#FF00BF; padding:10px; font-weight:200; z-index:10000; margin-top: 125px; float: left;}
        #all-examples-info { position:absolute; font-size:16px; padding-bottom: 20px; bottom: 0px; width:210px; line-height:150%; background-color: rgba(255, 171, 251, 0.5);}
        #hider {padding:10px; position: absolute;}
        #slider {position:absolute;padding:20px;bottom:20px;z-index:10000;}
        .slider.slider-vertical {height: 310px; margin-top: 60px; margin-left: 20px;}
        .slider-handle.custom{background-color: #FF00BF;border-radius: 50%;}
        .slider-handle.custom::before {content: none;}
        .slider-tick.custom{border-radius: 50%; background: #FFFFFF;}
        .slider-tick.custom::before {content: none;}
        .slider-selection.tick-slider-selection {background-image: -webkit-linear-gradient(top,#ff9ed9 0,#ff9ed9 100%);}
        .btn-custom {background: #FF00BF; color: #ffffff; border-radius: 0px; border-color: transparent;}
        .btn-custom:hover, .btn-custom:focus, .btn-custom:active, .btn-custom.active, .btn-custom:active, .btn-custom:active:focus, .open > .dropdown-toggle.btn-custom { background: #E500AB; border-color: transparent; color: #ffffff;}
        .btn.sharp {border-radius:0; margin: 0px; width:210px;}
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" src="{{ url('/js/heatmap.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/gmaps-heatmap.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/bootstrap-slider.min.js') }}"></script>
</head>
<body>
<h1>Lyft Drivers Heatmap</h1>
<h3>Last Updated: <div id="lastUpdated"></div></h3>
<h5>  Riders Online <div id="ridersNumber" style="float:left; margin-right: 5px;"> </div></h5>
<div id="map-canvas"></div>
<div id="all-examples-info">
    <div id="hider" class="btn btn-custom sharp" type="button">Hide Dates</div>
    <div id="hide">
        <input id="slider" type="text"/>
    </div>
</div>
<script>
    $("#hider").click(function(){
        $("#hide").toggle();
        
        if ($.trim($(this).text()) === 'Hide Dates') {
            $(this).text('Show Dates');
            $(this).css("bottom", "0px");
        } else {
            $(this).text('Hide Dates');
            $(this).css("bottom", "");      
        }
    });

    var lines = [];
    var ticks = [];
    var ticks_lables = [];
    var ticks_position = [];
    var finalValue;
    jQuery.get('data/data.txt', function(data) {
        //process text file line by line
        lines = data.split("\n");
        finalValue = lines.length;

        //Add Last Updated Text
        $("#lastUpdated").text(lines[finalValue-2].replace('//',''));
        
        for (var i = 0, len = lines.length; i < len; i++) {
            if(lines[i].indexOf('//') > -1)
            {
                var line = lines[i].replace('//','')
                var list = line.split(",");
                var cleanedDate = list[0]+","+list[1];
                var found = jQuery.inArray(cleanedDate, ticks_lables);
                if (found < 0) {
                    ticks_lables.push(cleanedDate);
                    ticks.push(i+1);
                    ticks_position.push(i/(lines.length)*100);
                }
            }
        }

        // map center
        var myLatlng = new google.maps.LatLng(25.972687, -80.232468);
        // map options,
        var myOptions = {
            zoom: 11,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            disableDefaultUI: true
        };
        // standard map
        map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
        // heatmap layer
        heatmap = new HeatmapOverlay(map,
                {
                    // radius should be small ONLY if scaleRadius is true (or small radius is intended)
                    "radius": 0.003,
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
        var finalLine = lines[1].replace("[", "").replace("]", "");
        for(var i = 3; i < lines.length; i+=2){
            var line = lines[i].replace("[", "").replace("]", "");
            finalLine = finalLine + "," + line;
        }
        var complete = "[" + finalLine + "]";
        var lastLine = lines[finalValue-1];
        var testData = {
            max: 8,
            data: JSON.parse(lastLine)
        };
        heatmap.setData(testData);

        var ChangeData = function() {
            $a = slider.getValue();
            var dataGeo = JSON.parse(lines[$a]);
            var date = lines[$a -1].replace('//','');
            $("#ridersNumber").text(dataGeo.length/4);
            $("h3").html("Set Date: <div id='lastUpdated'></div>");
            $("#lastUpdated").text(date);
            var data = {
                max: 8,
                data: dataGeo
            };
            heatmap.setData(data);

        };

        var slider = $("#slider").slider({
            min  : 1,
            value: finalValue-1,
            step: 2,
            ticks: ticks,
            ticks_labels: ticks_lables,
            orientation: 'vertical',
            handle: 'custom',
            reversed : true,
            tooltip: "hide"
        }).on('change', ChangeData).data('slider');

    });
</script>
</body>
</html>