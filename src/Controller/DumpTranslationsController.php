<?php declare(strict_types=1);

namespace Becklyn\Translations\Controller;

use Becklyn\Translations\Extractor\TranslationsExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $catalogue = $extractor->fetchCatalogue($namespace, $locale, !$isDebug);
        // we are embedding it inside a string, so we must double escape backslashes
        $json = \addslashes($catalogue->getCatalogueJson());

        $response = new JsonResponse(
            // use JSON parse and a string here, as it is way faster than parsing JavaScript in the browser.
            "JSON.parse('{$json}')",
            200,
            [
                // prevent magic byte insertion
                "X-Content-Type-Options" => "nosniff",
            ],
            true
        );

        $response->setCallback("window.TranslatorInit.init");

        if (!$isDebug)
        {
            $response->setEtag($catalogue->getHash());
            $response->isNotModified($request);
        }

        return $response;
    }
}
