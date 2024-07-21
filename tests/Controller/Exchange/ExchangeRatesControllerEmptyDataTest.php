<?php

declare(strict_types=1);

namespace Controller\Exchange;

use App\Controller\Exchange\ExchangeRatesController;
use App\Services\Exchange\ExchangeRateService;
use App\Services\Exchange\NbpApiService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ExchangeRatesControllerEmptyDataTest extends WebTestCase
{
    public function testIndexWithEmptyData()
    {
        $emptyResponseData = [[]];

        $currentResponseData = [
            [
                'rates' => [
                    ['currency' => 'euro', 'code' => 'EUR', 'mid' => 4.6],
                    ['currency' => 'dollar', 'code' => 'USD', 'mid' => 3.9],
                    ['currency' => 'czk', 'code' => 'CZK', 'mid' => 0.19],
                    ['currency' => 'idr', 'code' => 'IDR', 'mid' => 0.00027],
                    ['currency' => 'brl', 'code' => 'BRL', 'mid' => 0.78]
                ]
            ]
        ];

        $mockClient = new MockHttpClient([
            new MockResponse(json_encode($emptyResponseData)),
            new MockResponse(json_encode($currentResponseData))
        ]);

        $nbpApiService = new NbpApiService($mockClient);
        $exchangeRateService = new ExchangeRateService();

        $controller = new ExchangeRatesController($nbpApiService, $exchangeRateService);

        $request = new Request();
        $date = '2024-07-19';
        $response = $controller->index($request, $date);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);

        $this->assertEquals($date, $data['date']);
        $this->assertArrayHasKey('rates', $data);
        $this->assertArrayHasKey('current_rates', $data);

        $this->assertEmpty($data['rates']);
    }
}
