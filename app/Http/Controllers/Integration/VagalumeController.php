<?php
/**
 * Created by PhpStorm.
 * User: brian
 * Date: 01/05/2019
 * Time: 11:17
 */

namespace App\Http\Controllers\Integration;

use GuzzleHttp\Client;

class VagalumeController {

    public static function buscarTrecho($texto) {
        $results = [];
        if (strlen($texto) > 5) {
            $vagalumeApiKey = env('VAGALUME_API_KEY', 'b22ba906fa0b09bed1ec0c99ee9b54ef');

            $texto = ltrim($texto);
            $texto = rtrim($texto);
            $texto = str_replace(' ', '%20', $texto);

            try {
                $client = new Client();
                $request = $client->get('https://api.vagalume.com.br/search.excerpt?q='.$texto.'&limit=25&apikey='.$vagalumeApiKey);
                $results = json_decode($request->getBody()->getContents());
                $results = $results->response->docs;
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                $results = [];
                echo "Erro ao conectar com a API Vagalume\n";
            }
        }

        return $results;
    }

}