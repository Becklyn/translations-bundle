<?php declare(strict_types=1);

namespace Becklyn\Translations\Controller;

use Becklyn\Translations\Extractor\TranslationsExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DumpTranslationsController extends AbstractController
{
    /**
     */
    public function dump (
        TranslationsExtractor $extractor,
        ParameterBagInterface $parameters,
        Request $request,
        string $namespace,
        string $locale
    ) : Response
    {
        $isDebug = $parameters->get("kernel.debug");
        $catalogue = $extractor->fetchCatalogue($namespace, $locale);

        $response = new Response(
            \sprintf(
                "/**/%s(%s);",
                "window.TranslatorInit.init",
                $catalogue->getCompiledCatalogue()
            ),
            200,
            [
                // prevent magic byte insertion
                "X-Content-Type-Options" => "nosniff",
                "Content-Type" => "text/javascript",
            ]
        );

        if (!$isDebug)
        {
            $response->setEtag($catalogue->getHash());
            $response->isNotModified($request);
        }

        return $response;
    }
}
