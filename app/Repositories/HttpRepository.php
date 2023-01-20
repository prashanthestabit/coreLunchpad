<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

/**
 * Class HttpRepository.
 */
class HttpRepository
{
    /**
     * @return string
     *  Return the post
     */
    public function post($url, $data, $image = NULL)
    {
        try{
            //$token = \Session::get('studentToken');
            //dd($token);
            if($image){
                $imageName = $image->getClientOriginalName();
                $response = Http::attach(
                    'image', file_get_contents($image),$imageName
                )->post($url, $data);
            }else{
                $response = Http::post($url, $data);
            }

            if($response->successful() || $response->failed())
            {
                return $response->json();
            }else if($response->serverError() || $response->clientError())
            {
                return $response->throw();
            }else{
                return $response->json();
            }

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => __('messages.error')], 500);
        }

    }

      /**
     * @return string
     *  Return the get
     */
    public function get($url,$header = NULL)
    {
        try{
            if($header)
            {
                $response = Http::withHeaders([
                    'Authorization' => $header,
                ])->get($url);
            }else{
                $response = Http::get($url);
            }

            return $response->json();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => __('messages.error')], 500);
        }

    }
}
