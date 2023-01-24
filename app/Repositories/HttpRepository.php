<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class HttpRepository.
 */
class HttpRepository
{
    /**
     * @return string
     *  Return the post
     */
    public function post($url, $data, $image = null, $header = null)
    {
        if ($image) {
            if (! $image->getClientOriginalName()) {
                Log::error('Image Not Found');

                return response()->json(['error' => __('messages.error')], 500);
            }
            $imageName = $image->getClientOriginalName();
        }

        try {
            if ($header) {
                $response = Http::post($url, $data)->withHeaders(['Authorization' => $header]);
                if ($image) {
                    $response->attach('image', file_get_contents($image), $imageName);
                }
            } else {
                $response = Http::post($url, $data);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => __('messages.error')], 500);
        }
    }

    /**
     * @return string
     *  Return the get
     */
    public function get($url, $header = null)
    {
        try {
            if ($header) {
                $response = Http::withHeaders([
                    'Authorization' => $header,
                ])->get($url);
            } else {
                $response = Http::get($url);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => __('messages.error')], 500);
        }
    }

    public function getResponse($response)
    {
        try {
            if ($response->ok()) {
                return $response->json();
            } elseif ($response->status() === 401) {
                return response()->json(['error' => 'Unauthorized'], 401);
            } else {
                return response()->json(['error' => __('messages.error').$response->status()], 500);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => __('messages.error')], 500);
        }
    }
}
