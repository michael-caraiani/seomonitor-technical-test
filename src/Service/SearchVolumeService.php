<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SearchVolumeService
{
    private string $url = '/v3/keywords_data/google_ads/search_volume/live';

    public function __construct(
        private readonly HttpClientInterface $dataforseoApiClient,
    ) {
    }

    public function getSearchVolumeForKeywords(array $keywords): array
    {
        $httpResponse = $this->request($keywords);

        try {
            $content = $httpResponse->toArray();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException('Something went wrong, getting keywords content failed', $e);
        }

        if ($content['tasks_error'] > 0 || 0 === $content['tasks_count']) {
            throw new BadRequestHttpException($content['status_message']);
        }
        // just take first task, as multiple tasks case is unknown for me
        $task = $content['tasks'][0];

        return $task['result_count'] > 0 ? $task['result'] : [];
    }

    private function request(array $keywords): ResponseInterface
    {
        try {
            return $this->dataforseoApiClient->request('POST', $this->url, [
                'json' => [
                    [
                        'keywords' => $keywords,
                        'location_code' => 2840,
                        'language_code' => 'en',
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            throw new BadRequestHttpException(
                'Something went wrong, request for getting keywords data failed',
                $e
            );
        }
    }
}
