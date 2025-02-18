<?php declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class InsightsRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $domain,
        #[Assert\NotBlank]
        public array $keywords,
    ) {
    }
}
