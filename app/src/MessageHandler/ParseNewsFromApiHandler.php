<?php
namespace App\MessageHandler;

use App\Message\ParseNewsFromApi;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AsMessageHandler]
class ParseNewsFromApiHandler
{
    public function __invoke(ParseNewsFromApi $parseNewsFromApi)
    {
        dump($parseNewsFromApi);        
    }
}
