<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SeoInsightsService
{
    private string $openAIApiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        private readonly string $openAiApiKey,
        private readonly HttpClientInterface $httpClient,
        private readonly VisibilityMetricsService $visibilityMetricsService,
    ) {
    }

    public function generateSeoInsights(string $domain, array $keywords): array
    {
        $response = $this->visibilityMetricsService->calculate($domain, $keywords);
        $userMessage = $this->prepareUserMessage($response['list']);
        $insights = $this->request($userMessage);

        return [
            'insights' => $insights,
            'metrics' => $response,
        ];
    }

    private function prepareUserMessage(array $data): string
    {
        return "Analyze the following JSON data and provide 5 key insights based on the following definitions:
- Ranking is represented by the 'position' field (lower values indicate better ranking, with 0 being the highest).
- Visibility Score 'vscore' represents how well the keyword is performing in search results, where a higher value means better visibility.
- Search Volume 'search_volume' represents the estimated number of searches for the keyword per month.
Generate insights based on these metrics, such as identifying high-ranking but low-visibility keywords, high search volume keywords with poor rankings, and any opportunities for improvement.
Respond **only in valid JSON format**, following this schema:
{
  \"insights\": [
    {
      \"insight\": \"string (A key insight derived from the data)\",
      \"keywords\": \"string\" (List of relevant keywords),
      \"ranking_impact\": \"string (High, Medium, Low, None)\"
    }
  ]
}
Do not add ```json. Do not include any explanations or additional text outside this JSON structure.
JSON Data: ".json_encode($data);
    }

    private function request(string $userMessage): array
    {
        try {
            $response = $this->httpClient->request('POST', $this->openAIApiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->openAiApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o',
                    'messages' => [
                        ['role' => 'developer', 'content' => 'You are an expert SEO analyst.'],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                ],
            ]);

            $responseData = $response->toArray();
        } catch (\Throwable $e) {
            throw new BadRequestHttpException(sprintf('Something went wrong, request to openAI API failed: %s', $e->getMessage()), $e);
        }

        $choice = $responseData['choices'][0];
        $finishReason = $choice['finish_reason'];

        if ('stop' !== $finishReason) {
            $errorMessage = match ($finishReason) {
                'length' => 'The response exceeded the token limit.',
                'content_filter' => 'The response was blocked due to content filtering.',
                default => 'Unknown finish reason: '.$finishReason,
            };

            throw new BadRequestHttpException($errorMessage);
        }

        $content = json_decode($choice['message']['content'], true);

        return $content['insights'] ?? [];
    }
}
