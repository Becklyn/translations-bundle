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
     * @param string $locale
     */
    public function dump (
        TranslationsExtractor $extractor,
        ParameterBagInterface $parameters,
        Request $request,
        string $locale
    ) : Response
    {
        $isDebug = $parameters->get("kernel.debug");
        $catalogue = $extractor->fetchCatalogue($locale, !$isDebug);

        $response = new JsonResponse(
            // use JSON parse and a string here, as it is way faster than parsing JavaScript in the browser.
            "JSON.parse('{$catalogue->getCatalogueJson()}')",
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
