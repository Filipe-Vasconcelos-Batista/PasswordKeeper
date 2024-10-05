<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
class PasswordGeneratorService
{
    private $client;
    private $apikey;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apikey = $_ENV['API_NINJA_KEY'];
    }
    public function generatePassword(int $length, bool $excludeNumbers, bool $excludeSpecialChars): string
    {
        if ($excludeNumbers==1) {
            $excludeNumbers="true";
        }
        if ($excludeSpecialChars==1) {
            $excludeSpecialChars="true";
        }

        try{
            $response= $this->client->request('GET', 'https://api.api-ninjas.com/v1/passwordgenerator',
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
            return $data['random_password'] ?? '';
        }catch (\Exception $e){
            throw new \RuntimeException('Error generating password: ' .$e->getMessage(),0,$e);
        }
    }
}
