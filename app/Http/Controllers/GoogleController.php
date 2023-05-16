<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use Cache;
use Carbon\Carbon;

class GoogleController extends Controller
{
    public function getRestaurantsList(Request $request) {
        // settime
        $expiresAt = Carbon::now()->addMinutes(60);
        // Cache::flush();
        /**
         * Checking cache data has exist or not
         * if not will calling the Google API with keyword from the front-end
         */
        if($request->nextPage == false && Cache::has('keyword-' . $request->keyword)) {
            return Cache::get('keyword-' . $request->keyword);
        } else if (Cache::has('keyword-' . $request->keyword . '-' . $request->nextPage)) {
            return Cache::get('keyword-' . $request->keyword . '-' . $request->nextPage);
        } else {

            /**
             * Call Google API using cURL package 
             */
            $endpoint = "https://maps.googleapis.com/maps/api/place/textsearch/json";
            $client = new \GuzzleHttp\Client();
            if($request->nextPage != false) {
                $response = $client->request('GET', $endpoint, ['query' => [
                    'query' => $request->keyword, 
                    'radius' => '1000',
                    'type' => 'restaurant|food',
                    'language' => 'th',
                    'key' => config('app.googleapi'), // key api set in the ENV, Please make sure the api key added in the 
                    'pagetoken' => $request->nextPage
                ]]);
                Cache::put('keyword-' . $request->keyword .'-'.$request->nextPage, $response->getBody()->getContents(), $expiresAt);
            } else {
                $response = $client->request('GET', $endpoint, ['query' => [
                    'query' => $request->keyword, 
                    'radius' => '1000',
                    'type' => 'restaurant|food',
                    'language' => 'th',
                    'key' => config('app.googleapi') // key api set in the ENV, Please make sure the api key added in the 
                ]]);
                Cache::put('keyword-' . $request->keyword, $response->getBody()->getContents(), $expiresAt);
            }

            /**
             * Store result from front-end if already the same keyword
             */
            return $response;
        }
    }
}
