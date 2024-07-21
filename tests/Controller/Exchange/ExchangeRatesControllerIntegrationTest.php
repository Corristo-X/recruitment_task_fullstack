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

class ExchangeRatesControllerIntegrationTest extends WebTestCase
{
    public function testIndex()
    {
        $responseData = [
            [
                'rates' => [
                    ['currency' => 'euro', 'code' => 'EUR', 'mid' => 4.5],
                    ['currency' => 'dollar', 'code' => 'USD', 'mid' => 3.8],
                    ['currency' => 'czk', 'code' => 'CZK', 'mid' => 0.18],
                    ['currency' => 'idr', 'code' => 'IDR', 'mid' => 0.00026],
                    ['currency' => 'brl', 'code' => 'BRL', 'mid' => 0.77]
                ]
            ]
        ];

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
            new MockResponse(json_encode($responseData)),
            new MockResponse(json_encode($currentResponseData))
        ]);

        $nbpApiService = new NbpApiService($mockClient);
        $exchangeRateService = new ExchangeRateService();

        $controller = new ExchangeRatesController($nbpApiService, $exchangeRateService);

        $request = new Request();
        $date = '2024-07-19';
        $response = $controller->index($request, $date);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);

        $this->assertEquals($date, $data['date']);
        $this->assertArrayHasKey('rates', $data);
        $this->assertArrayHasKey('current_rates', $data);

        $expectedRates = [
            'EUR' => ['currency' => 'euro', 'nbp' => 4.5, 'buy' => 4.45, 'sell' => 4.57],
            'USD' => ['currency' => 'dollar', 'nbp' => 3.8, 'buy' => 3.75, 'sell' => 3.87],
            'CZK' => ['currency' => 'czk', 'nbp' => 0.18, 'buy' => null, 'sell' => 0.33],
            'IDR' => ['currency' => 'idr', 'nbp' => 0.00026, 'buy' => null, 'sell' => 0.15026],
            'BRL' => ['currency' => 'brl', 'nbp' => 0.77, 'buy' => null, 'sell' => 0.92]
        ];

        $this->assertRatesEqual($expectedRates, $data['rates']);

        $expectedCurrentRates = [
            'EUR' => ['currency' => 'euro', 'nbp' => 4.6, 'buy' => 4.55, 'sell' => 4.67],
            'USD' => ['currency' => 'dollar', 'nbp' => 3.9, 'buy' => 3.85, 'sell' => 3.97],
            'CZK' => ['currency' => 'czk', 'nbp' => 0.19, 'buy' => null, 'sell' => 0.34],
            'IDR' => ['currency' => 'idr', 'nbp' => 0.00027, 'buy' => null, 'sell' => 0.15027],
            'BRL' => ['currency' => 'brl', 'nbp' => 0.78, 'buy' => null, 'sell' => 0.93]
        ];

        $this->assertRatesEqual($expectedCurrentRates, $data['current_rates']);
    }

    private function assertRatesEqual(array $expectedRates, array $actualRates)
    {
        foreach ($expectedRates as $currency => $expectedRate) {
            $this->assertArrayHasKey($currency, $actualRates);
            $actualRate = $actualRates[$currency];

            foreach ($expectedRate as $key => $expectedValue) {
                if (is_float($expectedValue)) {
                    $this->assertEqualsWithDelta($expectedValue, $actualRate[$key], 0.0001, "$currency $key does not match");
                } else {
                    $this->assertEquals($expectedValue, $actualRate[$key], "$currency $key does not match");
                }
            }
        }
    }
}
