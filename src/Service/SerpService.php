<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SerpService
{
    private string $url = '/v3/serp/google/organic/live/regular';

    public function __construct(
        private readonly HttpClientInterface $dataforseoApiClient,
    ) {
    }

    public function getPositionDomainForKeywords(string $domain, array $keywords): array
    {
        $httpResponses = [];
        $resultData = [];
        foreach ($keywords as $keyword) {
            $httpResponses[] = $this->request($domain, $keyword);
        }

        foreach ($this->dataforseoApiClient->stream($httpResponses) as $response => $chunk) {
            try {
                if ($chunk->isLast()) {
                    $content = $response->toArray();

                    if ($content['tasks_error'] > 0 || 0 === $content['tasks_count']) {
                        throw new BadRequestHttpException($content['status_message']);
                    }

                    $resultData[] = $this->processTaskResults($content);
                }
            } catch (\Throwable $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
        }

        return $resultData;
    }

    private function request(string $domain, string $keyword): ResponseInterface
    {
        try {
            return $this->dataforseoApiClient->request('POST', $this->url, [
                'json' => [
                    [
                        'keyword' => $keyword,
                        'location_code' => 2840,
                        'language_code' => 'en',
                        'target' => "$domain*",
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            throw new BadRequestHttpException(sprintf('Something went wrong, request for key %s failed', $keyword), $e);
        }
    }

    private function processTaskResults(array $content): array
    {
        $task = $content['tasks'][0];

        if ($task['result_count'] > 0) {
            // just take first result, as multiple result case is unknown for me
            $result = $task['result'][0];

            if ($result['items_count'] > 0) {
                // just take first item, but shall I sum the rank if there is more than one item ?
                $item = $result['items'][0];

                return [
                    'keyword' => $result['keyword'],
                    'position' => $item['rank_absolute'],
                    'domain' => $item['domain'],
                ];
            }
        }

        return [
            'keyword' => $task['data']['keyword'],
            'position' => 0,
            'domain' => $task['data']['target'],
        ];
    }
}
