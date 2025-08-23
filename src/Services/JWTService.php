<?php

namespace App\Services;

use DateTimeImmutable;

class JWTService
{
    /**
     * Retourne le string d'un encodage base64 adapté pour un JWT.
     * @param string $data La valeur à encoder.
     * @return string La valeur nettoyée.
     */
    private function base64url(string $data): ?string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Récupère le header d'un token.
     */
    private function getHeader(string $token): ?array
    {
        $array = explode('.', $token);
        $header = json_decode(base64_decode($array[0]), true);
        return $header;
    }

    /**
     * Récupère le payload d'un token.
     * @param string $token Le token à utilisr
     * @return array Un tableau associatif contenant les données du payload décodé.
     */
    public function getPayload(string $token): ?array
    {
        $array = explode('.', $token);
        $payload = json_decode(base64_decode($array[1]), true);
        return $payload;
    }

    /**
     * Génère un Json Web Token (JWT) en prenant en paramètre, le header, le payload, la clé privée secrète et la validité.
     * @param array $header Le bloc tête du JWT, souvent composé des valeurs suivantes : ['typ'=>'JWT','alg'=>'HS256']
     * @param array $payload Le bloc corps du JWT, il contient les valeurs à transporter dans le token, il contient également les valeurs 
     * IssuedAt et Expiration qui représente la durée de vie du token.
     * @param string $secret La clé permettant de hasher et de permettre la véracité du token
     * @param int $validity En seconde, $validity représente la durée de vie du token. (10800 secondes représentant 3h).
     * @return string Renvoie le token généré.
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {

        if ($validity > 0) {
            $now = new DateTimeImmutable();
            $expiration = $now->getTimestamp() + $validity;
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $expiration;
        }


        $base64Header  = $this->base64url(json_encode($header,  JSON_UNESCAPED_SLASHES));
        $base64Payload = $this->base64url(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $signatureBin = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);
        $signature    = $this->base64url($signatureBin);

        // 3) Token final
        $jwt = $base64Header . '.' . $base64Payload . '.' . $signature;
        return $jwt;
    }

    /**
     * Vérifie la signature et donc la véracité du token.
     * @param string $token Le jeton à vérifier
     * @param string $secret La clé privée secrète
     * @return bool En fonction de la validité du token.
     * 
     */
    public function check(string $token, string $secret): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        if (gettype($payload) !== 'array' || gettype($header) !== 'array') {
            return false;
        }

        $checkToken = $this->generate($header, $payload, $secret, 0);

        return $token === $checkToken;
    }

    /**
     * Vérifie si le token est expiré.
     * @param string $token Le token à vérifier.
     * @return bool Renvoi true si le jeton est expiré.
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new DateTimeImmutable();
        if (!isset($payload['exp'])) {
            return false;
        }
        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * Vérifie l'intégrité du token
     * @param string $token Le token à vérifier.
     * @return bool Renvoi true si le jeton est intègre. 
     */
    public function isValid(string $token): ?bool
    {
        return preg_match('/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/', $token) === 1;
    }
}
