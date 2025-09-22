<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MapService
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient) {
        $this->httpClient = $httpClient;
    }

    public function getDataFromAddress(string $address): array
    {
        $response = $this->httpClient->request('GET', 'https://nominatim.openstreetmap.org/search', [
            'query' => [
                'q' => $address,
                'format' => 'json',
                'limit' => 10,
            ],
            'headers' => [
                'User-Agent' => 'MySymfonyApp/1.0'
            ]
        ]);

        $data = json_decode($response->getContent(),true);



        if (empty($data)) {
            throw new BadRequestException('Adresse pas trouvée');
        }

        return [
            'lat' => (float)$data[0]['lat'],
            'lon' => (float)$data[0]['lon']
        ];
    }
}