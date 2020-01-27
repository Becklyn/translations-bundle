<?php declare(strict_types=1);

namespace Becklyn\Translations\Extractor;

use Becklyn\Translations\Exception\TranslationsCompilationFailedException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TranslationsCompiler
{
    /** @var string */
    private $compiler;


    /**
     */
    public function __construct (string $projectDir)
    {
        $this->compiler = "{$projectDir}/node_modules/.bin/compile-translations";
    }


    /**
     * Compiles the catalogue
     */
    public function compileCatalogue (array $messages) : string
    {
        if (!\is_file($this->compiler))
        {
            throw new TranslationsCompilationFailedException(\sprintf(
                "Translations compiler not found at '%s'. Did you `npm install @becklyn/translator` in your project?",
                $this->compiler
            ));
        }

        try
        {
            $process = new Process([
                $this->getNodeExecutable(),
                $this->compiler,
            ], null, null, \json_encode($messages));
            $process->mustRun();
            return \trim($process->getOutput());
        }
        catch (ProcessFailedException $exception)
        {
            throw new TranslationsCompilationFailedException(\sprintf(
                "Calling the compiler failed with error: {$exception->getMessage()}"
            ), $exception);
        }
    }


    /**
     */
    private function getNodeExecutable () : string
    {
        $possibilities = [
            "/usr/local/bin/node",
            "/usr/bin/node",
        ];

        foreach ($possibilities as $path)
        {
            if (\is_file($path) && \is_executable($path))
            {
                return $path;
            }
        }

        throw new TranslationsCompilationFailedException(\sprintf(
            "Could not find any node executable, looked in: %s",
            \implode(", ", $possibilities)
        ));
    }
}
