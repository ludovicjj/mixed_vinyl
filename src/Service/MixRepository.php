<?php

namespace App\Service;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bridge\Twig\Command\DebugCommand;

class MixRepository
{
    public function __construct(
        private HttpClientInterface $githubClient,
        private CacheInterface $cache,
        #[Autowire('%kernel.debug%')]
        private bool $isDebug,
        #[Autowire(service: 'twig.command.debug')]
        private DebugCommand $twigDebugCommand
    ){}

    public function findAll(): array
    {
        // Executing a console command manually with using non-autowireable service
        //
        // $output = new BufferedOutput();
        // $this->twigDebugCommand->run(new ArrayInput([]), $output);
        // dd($output);

        return $this->cache->get('mixes_data', function (CacheItemInterface $cacheItem) {
            $cacheItem->expiresAfter($this->isDebug ? 5 : 60);
            $response = $this->githubClient->request('GET', '/SymfonyCasts/vinyl-mixes/main/mixes.json');
            return $response->toArray();
        });
    }
}