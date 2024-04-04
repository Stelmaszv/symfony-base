<?php


namespace App\Generic\Auth;
class JWT
{
    private string $appSecret;

    public function __construct(string $appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function encode(array $data)
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($data));
        $signature = hash_hmac('sha256', "$header.$payload", $this->appSecret, true);
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }

    public function decode(string $token)
    {
        list($header, $payload, $signature) = explode('.', $token);
        $data = json_decode(base64_decode($payload), true);
        $expectedSignature = hash_hmac('sha256', "$header.$payload", $this->appSecret, true);
        $expectedSignature = base64_encode($expectedSignature);

        if ($signature !== $expectedSignature) {
            throw new \Exception("Invalid token signature");
        }

        return $data;
    }
}