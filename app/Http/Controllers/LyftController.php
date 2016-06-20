<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp;
use App\Http\Requests;

class LyftController extends Controller
{
    public function index()
    {
        $locations = [];

        $apis = [
            ['client' => '3Tcj_10h6Eba', 'secret' => 'V1lNt-9AMzZxWS6b4e7Lwk4hUdxXFWGG'],
            //['client' => 'c4rFMb4GPNj_', 'secret' => 'rqOFaQBiRKB3bmm1_Y2qLQ_tE9NWzNKf'],
            //['client' => 'Wj4AApiwzY9c', 'secret' => '0ACO0lRiQOuEBsACnRnmUN3PguDum3G1'],
            //['client' => 'H1U4GtNq7d6y', 'secret' => 'O-3-TnHaGv4412ce7W59jcI0gnSoqbwc'],
            //['client' => 'gCLrKGa7Bd9O', 'secret' => 'X_2xLEV-8FF2xNo7S9yc6r8Bt6ynv3Vh'],
            //['client' => 'kgJJVnLMbz_B', 'secret' => 'vsO0smWKzgxqjmRaUKg1p4Qg21Ok85x6'],
            //['client' => 'aM8Wr6omLrh7', 'secret' => 'sJJsw-cMQH7v5H8Ij7A_6o5XtM9-4RH4'],
            //['client' => 'OOeivzzvKw_R', 'secret' => '5OlGY6FJcQ2KRzODaNlq_4m5CjOcPkrp'],
            //['client' => 'V60vbSAwkpz-', 'secret' => 'WGZEoFFh3-uOKXM0u2RElQ4uTGF_Btbe'],
            //['client' => '1bj4OW9x5RQZ', 'secret' => 'PZMCs7UNSRZl4KmVG20HK5zOJzqRM5uB'],
            //['client' => 'jcaRNzlF2zUZ', 'secret' => 'p8Rymxqb1X6ZZe4D2LQTuZcmMp9woRL1'],
            //['client' => 'LDTuio47NcXX', 'secret' => '_uDcXyjCzyIF-t-sOgJNi_oL38Mk-S9S'],
            //['client' => 'yMxmGx-2lStg', 'secret' => '8GBhm91gWDawRLP50q6KM4M4L0-LjTjZ'],
            //['client' => 'au4hzJnNMGky', 'secret' => 'HvTsBXxA9G-kRwk6knF1wwS9oEaZv77Z'],
            //['client' => 'doPceRIS2xjm', 'secret' => 'Ao1KIwYUptWdCrIXoj36lXVf1ulUtMp-'],
            //['client' => 'dNazrK7TlcOK', 'secret' => 'fBXvJ838O5fjOifOV6ypnHw6KBi0jMmD'],
            //['client' => 'lLXN6kZ851zP', 'secret' => 'dFLO7cGTswyobxOX3jXrWvJ8vsr3qqY1'],
            //['client' => 'gyFtBMN_z2ZI', 'secret' => 'Mg31kW17ZriesD4-7XRxHCVIGVe3xJjV'],
        ];

        $sum = 0;

	error_log("Started Script",0);
        foreach ($apis as $api) {
            $locations = $this->connectDifferentClients($locations, $api['client'], $api['secret'], $sum);
            $sum += 5;
        }
        $locations = json_encode($locations);

	error_log("finished Script Ready to write on file",0);
        $myFile = "data/data.txt";
        $fh = fopen($myFile, 'a') or die("can't open file");
        error_log("File Opened",0);
	$stringData = "\n//" . Carbon::now()->toDayDateTimeString() . "\n";
        fwrite($fh, $stringData);
	error_log("Date Written",0);
        fwrite($fh, $locations);
	error_log("Locations Written",0);
        fclose($fh);
	error_log("DONE",0);
        return "done";
        //return view('lyft', compact('locations'));
    }

    public function connectDifferentClients($locations, $clientApi, $secret, $sum){
        error_log("Starting Request for client: " .$clientApi, 0); 
	$client = new GuzzleHttp\Client(['base_uri' => 'https://api.lyft.com/']);
        $res = $client->request('POST', 'oauth/token', [
            'auth' => [$clientApi, $secret],
            'json' => [
                'grant_type' => 'client_credentials',
                'scope' => 'public'
            ]
        ]);
        $body = json_decode($res->getBody());
        $accessToken = $body->access_token;
	error_log("Got access token: " . $accessToken, 0);
        $locations = $this->getLocations($client, $accessToken, $locations, $sum);

        return $locations;
    }

    public function getLocations($client, $accessToken, $locations, $sum){
        $arr = [
            //Weston
            ['lat' => '26.117132', 'lng' => '-80.406189'],
            ['lat' => '26.094627', 'lng' => '-80.380440'],
            ['lat' => '26.074892', 'lng' => '-80.391083'],
            //Pine Insland Ridge
            ['lat' => '26.089077', 'lng' => '-80.329971'],
            ['lat' => '26.092468', 'lng' => '-80.272980'],
            ['lat' => '26.070266', 'lng' => '-80.294952'],
            //Davie
            ['lat' => '26.078901', 'lng' => '-80.253754'],
            ['lat' => '26.082293', 'lng' => '-80.226288'],
            ['lat' => '26.069033', 'lng' => '-80.237274'],
            //Seminole Hard Rock
            ['lat' => '26.053193', 'lng' => '-80.240707'],
            ['lat' => '26.053038', 'lng' => '-80.205860'],
            //Dania Beach and Airport
            ['lat' => '26.064098', 'lng' => '-80.175476'],
            ['lat' => '26.053920', 'lng' => '-80.154533'],
            ['lat' => '26.068416', 'lng' => '-80.131874'],
            ['lat' => '26.038498', 'lng' => '-80.174103'],
            ['lat' => '26.025893', 'lng' => '-80.149555'],
            //South Fort Lauderdale
            ['lat' => '26.093393', 'lng' => '-80.139599'],
            ['lat' => '26.099868', 'lng' => '-80.143890'],
            ['lat' => '26.116516', 'lng' => '-80.188522'],
            ['lat' => '26.097093', 'lng' => '-80.197105'],
            //Fort Lauderdale & Beach
            ['lat' => '26.116559', 'lng' => '-80.163460'],
            ['lat' => '26.134129', 'lng' => '-80.139084'],
            ['lat' => '26.127502', 'lng' => '-80.159340'],
            ['lat' => '26.116867', 'lng' => '-80.107327'],
            ['lat' => '26.108698', 'lng' => '-80.106983'],
            ['lat' => '26.100220', 'lng' => '-80.110760'],
            ['lat' => '26.130276', 'lng' => '-80.105267'],
            //Plantation && Sunrise
            ['lat' => '26.149804', 'lng' => '-80.229034'],
            ['lat' => '26.161823', 'lng' => '-80.297699'],
            ['lat' => '26.131004', 'lng' => '-80.298042'],
            ['lat' => '26.126688', 'lng' => '-80.243454'],
            ['lat' => '26.102334', 'lng' => '-80.226288'],
            ['lat' => '26.150729', 'lng' => '-80.186806'],
            //Hollywood
            ['lat' => '25.999932', 'lng' => '-80.150757'],
            ['lat' => '25.994377', 'lng' => '-80.202255'],
            ['lat' => '25.977712', 'lng' => '-80.119858'],
            //Pembroke Pines
            ['lat' => '25.996846', 'lng' => '-80.262337'],
            ['lat' => '25.997463', 'lng' => '-80.322075'],
            ['lat' => '25.999932', 'lng' => '-80.388336'],
            ['lat' => '26.040570', 'lng' => '-80.400009'],
            ['lat' => '26.043125', 'lng' => '-80.338898'],
            ['lat' => '26.044358', 'lng' => '-80.275383'],
            ['lat' => '26.027700', 'lng' => '-80.223198'],
            //Sunlife Stadium && Miramar
            ['lat' => '25.965366', 'lng' => '-80.312805'],
            ['lat' => '25.957230', 'lng' => '-80.234871'],
            ['lat' => '25.976478', 'lng' => '-80.199852'],
            //Miami Gardens && Palm Spring
            ['lat' => '25.930482', 'lng' => '-80.333405'],
            ['lat' => '25.941596', 'lng' => '-80.289116'],
            ['lat' => '25.935730', 'lng' => '-80.252037'],
            //North Miami Beach && Aventura
            ['lat' => '25.937583', 'lng' => '-80.165176'],
            ['lat' => '25.953636', 'lng' => '-80.138741'],
            ['lat' => '25.922145', 'lng' => '-80.123978'],
            //Miami Beach
            ['lat' => '25.851765', 'lng' => '-80.121403'],
            ['lat' => '25.825037', 'lng' => '-80.121918'],
            ['lat' => '25.816560', 'lng' => '-80.127926'],
            ['lat' => '25.817023', 'lng' => '-80.135307'],
            ['lat' => '25.803115', 'lng' => '-80.125523'],
            ['lat' => '25.794306', 'lng' => '-80.131617'],
            ['lat' => '25.883850', 'lng' => '-80.125008'],
            //South Beach
            ['lat' => '25.783177', 'lng' => '-80.142260'],
            ['lat' => '25.782018', 'lng' => '-80.134277'],
            ['lat' => '25.775834', 'lng' => '-80.134964'],
            ['lat' => '25.771352', 'lng' => '-80.137110'],
            //Wynwood
            ['lat' => '25.807442', 'lng' => '-80.195475'],
            ['lat' => '25.799792', 'lng' => '-80.190668'],
            ['lat' => '25.796315', 'lng' => '-80.197792'],
            ['lat' => '25.788355', 'lng' => '-80.191956'],
            //Dowtown Miami
            ['lat' => '25.782327', 'lng' => '-80.198307'],
            ['lat' => '25.778694', 'lng' => '-80.191269'],
            ['lat' => '25.997463', 'lng' => '-80.190239'],
            ['lat' => '25.965366', 'lng' => '-80.312805'],
            ['lat' => '25.753121', 'lng' => '-80.218048'],
            ['lat' => '25.768890', 'lng' => '-80.225601'],
            ['lat' => '25.740751', 'lng' => '-80.242767'],
            //Hialeah
            ['lat' => '25.857592', 'lng' => '-80.298386'],
            ['lat' => '25.878599', 'lng' => '-80.321045'],
            ['lat' => '25.857592', 'lng' => '-80.253410'],
            ['lat' => '25.828238', 'lng' => '-80.254784'],
            //Little Haiti && El Portal
            ['lat' => '25.831328', 'lng' => '-80.190926'],
            ['lat' => '25.856974', 'lng' => '-80.201225'],
            ['lat' => '25.849868', 'lng' => '-80.178909'],
            ['lat' => '25.817113', 'lng' => '-80.221481'],
            //Doral & Airport
            ['lat' => '25.813713', 'lng' => '-80.330315'],
            ['lat' => '25.836890', 'lng' => '-80.362244'],
            ['lat' => '25.784657', 'lng' => '-80.372200'],
            ['lat' => '25.795542', 'lng' => '-80.273838'],
            //North Miami & Executive Opa locka Airport
            ['lat' => '25.903218', 'lng' => '-80.257530'],
            ['lat' => '25.894351', 'lng' => '-80.198822'],
            ['lat' => '25.909484', 'lng' => '-80.307999'],
            ['lat' => '25.908866', 'lng' => '-80.179939'],
        ];
	
	error_log("Gettin Geolocations from " . $sum . " to " . $sum + 5,0);
        for ($i = $sum; $i < $sum+5; $i++) {
            $locations = $this->multipleLocations($client, $accessToken, $locations, $arr[$i]['lat'], $arr[$i]['lng']);
            //sleep(10);
        }
        return $locations;
    }

    public function multipleLocations($client, $accessToken, $locations, $lat, $lng){
        $res2 = $client->request('GET', 'v1/drivers', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken
            ],
            'query' => [
                'lat' => $lat,
                'lng' => $lng,
            ]
        ]);
        $body2 = json_decode($res2->getBody());
	
	error_log("Rquest Successful to get DriverLocation",0);
        $drivers_line = $body2->nearby_drivers[1]->drivers;
        $drivers_normal = $body2->nearby_drivers[2]->drivers;
	error_log("Got Drivers for [lat:" . $lat . ", lng:" . $lng . "]",0);

        foreach($drivers_line as $driver){
            for($i = 0; $i < count($driver->locations); $i++) {
                array_push($locations, $driver->locations[$i]);
            }
        }

        foreach($drivers_normal as $driver){
            for($i = 0; $i < count($driver->locations); $i++) {
                array_push($locations, $driver->locations[$i]);
            }
        }
        return $locations;
    }
}
