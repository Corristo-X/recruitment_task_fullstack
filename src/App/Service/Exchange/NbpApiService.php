<?php

declare(strict_types=1);

namespace App\Service\Exchange;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NbpApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchNbpRates(array $currencies, string $date): array
    {
        $rates = [];
        try {
            $response = $this->client->request('GET', "https://api.nbp.pl/api/exchangerates/tables/A/$date/?format=json");
            $data = $response->toArray();

            if (empty($data) || empty($data[0]['rates'])) {
                return $rates;
            }

            foreach ($data[0]['rates'] as $rate) {
                if (in_array($rate['code'], $currencies)) {
                    $rates[$rate['code']] = [
                        'currency' => $rate['currency'],
                        'mid' => $rate['mid']
                    ];
                }
            }
        } catch (Exception $e) {
            return $rates;
        }

        return $rates;
    }
}
