<?php declare(strict_types=1);

namespace Becklyn\Translations\Controller;

use Becklyn\Translations\Extractor\TranslationsExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DumpTranslationsCommand extends AbstractController
{
    /**
     * @param string $locale
     */
    public function dump (
        TranslationsExtractor $extractor,
        ParameterBagInterface $parameters,
        Request $request,
        string $locale
    )
    {
        $isDebug = $parameters->get("kernel.debug");
        $catalogue = $extractor->fetchCatalogue($locale, !$isDebug);

        $response = new Response(
            "window.TranslatorInit.init(JSON.parse('{$catalogue->getCatalogueJson()}'));",
            200,
            [
                "Content-Type" => "application/javascript; charset=utf-8",
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
