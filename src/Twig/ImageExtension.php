<?php

namespace App\Twig;

use App\Services\PhotoEncoderService;
use Twig\Attribute\AsTwigFilter;

final class ImageExtension
{
    public function __construct(private PhotoEncoderService $encoder) {}

    #[AsTwigFilter('to_base64')]
    public function photoBase64(mixed $data): ?string
    {
        return $this->encoder->toBase64($data);
    }
}
