<?php

namespace App\Services;

class PhotoEncoderService
{
    public function toBase64(mixed $data): ?string
    {
        if ($data === null) return null;

        if (\is_resource($data)) {
            $data = stream_get_contents($data); // après ça, $data est une string
        }

        return $data === '' || $data === false ? null : base64_encode($data);
    }
}
