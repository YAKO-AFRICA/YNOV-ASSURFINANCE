<?php

namespace App;

use Illuminate\Support\Facades\Http;

class ApiHelper
{
    protected static $baseUrl = 'https://api.yakoafricassur.com/enov/';
    protected static $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MjExODcyLCJlbWFpbCI6ImZvcm1hdGlvbi5ibmlAYm5pLmNvbSIsIm5vbSI6IkJOSSIsImNvZGVhZ2VudCI6IkIwNDAiLCJ0eXBlbWVicmUiOm51bGwsInByZW5vbSI6IkZvcm1hdGlvbiJ9.gwxwy43VeMDcfaTpgpFbuWkxjirIBqvuXq3UZOuw_nA';

    /**
     * Fonction générique pour les appels POST
     */
    public static function post($endpoint, $data = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . self::$token,
            'Accept' => 'application/json',
        ])->post(self::$baseUrl . $endpoint, $data);

        if ($response->failed()) {
            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->body(),
            ];
        }

        $data = $response->json();
        $dataAgence = $data['dataAgence'];

        return [
            'success' => true,
            'status' => $response->status(),
            'data' => $dataAgence,
        ];
    }

    /**
     * Fonction spécifique pour rechercher une agence
     */
    public static function getAgence($data = [])
    {
        return self::post('search-agence-web', $data);
    }
}
