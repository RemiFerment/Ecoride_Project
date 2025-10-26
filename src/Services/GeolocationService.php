<?php

namespace App\Services;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocationService
{

    public function __construct(private HttpClientInterface $client, private ParameterBagInterface $params) {}
    /**
     * Check, with the GeoName API, if a city exist
     */
    public function isValideCity(string $input): ?bool
    {
        $data = $this->getCityInfoFromGeonames($input);

        if ($data !== null && !empty($data)) {
            $city = iconv('UTF-8', 'ASCII//TRANSLIT', $data['name']);
            if (strcasecmp($city, iconv('UTF-8', 'ASCII//TRANSLIT', $input)) == 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Allows to get the official name of a desired city from a primary string.
     */
    public function getOfficialCityName(string $value): string|false
    {
        $data = $this->getCityInfoFromGeonames($value);

        if ($data !== null && !empty($data)) {
            return $data['name'];
        }
        return false;
    }

    /**
     * Calcul the time between two position, 
     */
    public function routeTimeCalcul(string $departure, string $destination): ?int
    {
        //Get info from Geonames API of the tow targets cities.
        try {
            $cityA = $this->getCityInfoFromGeonames($departure);
            $cityB = $this->getCityInfoFromGeonames($destination);

            //Get Latitude and Longitude of the City A
            $cityA_latitude = $cityA['lat'];
            $cityA_longitude = $cityA['lng'];

            //Get Latitude and Longitude of the City B
            $cityB_latitude = $cityB['lat'];
            $cityB_longitude = $cityB['lng'];

            $data = $this->getRouteTimeCalculFromMapbox($cityA_latitude, $cityA_longitude, $cityB_latitude, $cityB_longitude);
            return ($data['duration'] / 60);
        } catch (Exception $e) {
            'Impossible de récupérer les données : ' . $e->getMessage();
            return null;
        }
    }

    /**
     * Get an array response using the Geonames API, this array contains a lot of information from a desired city.
     * @param string $cityName The string of the desired city.
     * @return array The response of Geonames API.
     */
    private function getCityInfoFromGeonames(string $cityName): ?array
    {
        try {
            $url = "http://api.geonames.org/searchJSON?name_equals=" . urlencode($cityName) .
                "&featureClass=P&maxRows=1&username=" . urlencode($this->params->get('api.geoname.username'));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            return $data['geonames'][0];
        } catch (Exception $e) {
            'Impossible de récupérer les données : ' . $e->getMessage();
        }
        return null;
    }

    private function getRouteTimeCalculFromMapbox(string $start_lat, string $start_lng, string $end_lat, string $end_lng): ?array
    {
        $coordinate =  urlencode($start_lng . ',' . $start_lat . ";" . $end_lng . ',' . $end_lat);
        try {
            $url = "https://api.mapbox.com/directions/v5/mapbox/driving/$coordinate?access_token=" . urldecode($this->params->get('api.mapbox.token'));
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);


            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            return $data['routes'][0];
        } catch (Exception $e) {
            'Impossible de récupérer les données : ' . $e->getMessage();
        }
        return null;
    }

    public function getCitiesFromGeonames(string $query): array
    {
        $cities = [];
        try {
            $url = "http://api.geonames.org/searchJSON?q=" . urlencode($query) .
                "&featureClass=P&maxRows=10&username=" . urlencode($this->params->get('api.geoname.username'));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);

            if (isset($data['geonames'])) {
                foreach ($data['geonames'] as $cityData) {
                    $cities[] = $cityData['name'];
                }
            }
        } catch (Exception $e) {
            'Impossible de récupérer les données : ' . $e->getMessage();
        }
        return array_unique($cities);
    }
}
