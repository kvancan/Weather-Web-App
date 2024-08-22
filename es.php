<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$key = $_ENV["KEY"];

if (isset($_GET['location'])) {
$curl = curl_init();

$location = $_GET['location'] ?? '';

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://dataservice.accuweather.com/locations/v1/cities/search?apikey='.$key.'&q='.$location,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);
curl_close($curl);
$value = json_decode($response, true);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL =>  'http://dataservice.accuweather.com/forecasts/v1/daily/1day/'.$value[0]['Key'].'?apikey='.$key,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
$value = json_decode($response, true);
$Minimum = ($value['DailyForecasts'][0]['Temperature']['Minimum']['Value']);
$Maximum = ($value['DailyForecasts'][0]['Temperature']['Maximum']['Value']);
$Temperature = ($Minimum + $Maximum)/2;
$Comment = ($value['Headline']['Text']);

convert($Temperature);

}


function convert($Temperature){
    $celcius = ($Temperature-32)/1.8;
    return $celcius;
}


?>
<!DOCTYPE html>
<html>
<head>
<style>
</style>
</head>
<body>
    <form method="get" id="form">
  <label style="margin-left: 50px;" >Location</label><br>
  <br>
  <input id="location" name="location" ><br>
</form>
<button type="submit" form="form" value="Submit" style="margin-left: 65px;" >Find</button>
<br>
<?php if (isset($_GET['location'])) { ?>
<label id="celcius"><?php echo round(convert($Temperature)) ?? ''; ?>°C</label>
<?php } ?>
<br>
<label id="comment" ><?= $Comment  ?></label>
</body>
</html> 

