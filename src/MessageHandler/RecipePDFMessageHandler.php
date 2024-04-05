<?php

namespace App\MessageHandler;

use App\Message\RecipePDFMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsMessageHandler]
final class RecipePDFMessageHandler
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/pdf')]
        private readonly string $path,
        private readonly UrlGeneratorInterface $urlGenerator
    )
    {
    }

    public function __invoke(RecipePDFMessage $message)
    {
        $process = new Process([
            'curl',
            '--request',
            'POST',
            'http://localhost:3000/forms/chromium/convert/url',
            '--form',
            'url=' . $this->urlGenerator->generate('admin.recipe.show', ['slug' => $message->slug], UrlGeneratorInterface::ABSOLUTE_URL),
            '-o',
            'my-pdf.pdf',
        ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
