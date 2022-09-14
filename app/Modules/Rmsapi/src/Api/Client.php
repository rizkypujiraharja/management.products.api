<?php

namespace App\Modules\Rmsapi\src\Api;

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Class Client.
 */
class Client
{
    /**
     * @param RmsapiConnection $connection
     * @param string           $uri
     * @param array            $query
     *
     * @throws GuzzleException
     *
     * @return RequestResponse
     */
    public static function GET(RmsapiConnection $connection, string $uri, array $query = []): RequestResponse
    {
        $response = new RequestResponse(
            self::getGuzzleClient($connection)->get($uri, ['query' => $query])
        );

        logger('RMSAPI GET', [
            'uri'      => $uri,
            'query'    => $query,
            'response' => [
                'status_code' => $response->getResponseRaw()->getStatusCode(),
            ],
        ]);

        if ($response->isNotSuccess()) {
            Log::alert('Failed fetching from Rmsapi', [
                'status_code' => $response->getResponseRaw()->getStatusCode(),
            ]);
        }

        return $response;
    }

    /**
     * @param RmsapiConnection $connection
     * @param string           $uri
     * @param array            $data
     *
     * @throws GuzzleException
     *
     * @return RequestResponse
     */
    public static function POST(RmsapiConnection $connection, string $uri, array $data): RequestResponse
    {
        $response = new RequestResponse(
            self::getGuzzleClient($connection)->post($uri, [
                'json' => $data,
            ])
        );

        logger('RMSAPI POST', [
            'uri'      => $uri,
            'json'     => $data,
            'response' => [
                'status_code' => $response->getResponseRaw()->getStatusCode(),
            ],
        ]);

        if ($response->isNotSuccess()) {
            Log::alert('Failed fetching from Rmsapi', [
                'status_code' => $response->getResponseRaw()->getStatusCode(),
            ]);
        }

        return $response;
    }

    /**
     * @param RmsapiConnection $connection
     * @param string           $uri
     * @param array            $query
     *
     * @throws GuzzleException
     *
     * @return RequestResponse
     */
    public static function DELETE(RmsapiConnection $connection, string $uri, array $query): RequestResponse
    {
        $response = self::getGuzzleClient($connection)->delete($uri, ['query' => $query]);

        return new RequestResponse($response);
    }

    /**
     * @param RmsapiConnection $connection
     *
     * @return GuzzleClient
     */
    public static function getGuzzleClient(RmsapiConnection $connection): GuzzleClient
    {
        return new GuzzleClient([
            'base_uri'   => $connection->url,
            'timeout'    => 600,
            'exceptions' => false,
            'auth'       => [
                $connection->username,
                Crypt::decryptString($connection->password),
            ],
        ]);
    }
}
