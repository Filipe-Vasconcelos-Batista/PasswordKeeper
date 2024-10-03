<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
class PasswordGeneratorService
{
    private $client;
    private $apikey;

    public function __construct(HttpClientInterface $client, string $apikey )
    {
        $this->client = $client;
        $this->apikey = $apikey;
    }
    public function generatePassword(int $length=16, bool $excludeNumbers=false, bool $excludeSpecialChars=false): string
    {
        $response= $this->client->request('GET', 'https://api.api-ninjas.com/vi/passwordgenerator',
        [
            'headers'=>[
                'X-Api-Key' => $this->apikey,
            ],
            'query'=>[
                'length'=>$length,
                'exclude_numbers'=>$excludeNumbers,
                'exclude_special_chars'=>$excludeSpecialChars,
            ],
        ]);
        $data= $response->toArray();
        return $data['password'] ?? '';
    }
}
