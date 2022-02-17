<?php declare(strict_types=1);

namespace Becklyn\Translations\Twig;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TranslationsTwigExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private ContainerInterface $locator;
    private RequestStack $requestStack;


    public function __construct (ContainerInterface $locator, RequestStack $requestStack)
    {
        $this->locator = $locator;
        $this->requestStack = $requestStack;
    }


    public function renderInit (string $namespace, ?string $locale = null) : string
    {
        if (null === $locale)
        {
            $locale = $this->getLocale();

            if (null === $locale)
            {
                return "<-- Can't embed, because no locale given -->";
            }
        }

        return $this->locator->get(Environment::class)->render("@BecklynTranslations/init.html.twig", [
            "locale" => $locale,
            "namespace" => $namespace,
        ]);
    }


    private function getLocale () : ?string
    {
        $locale = null;

        if (null !== ($request = $this->requestStack->getCurrentRequest()))
        {
            $locale = $request->getLocale();
        }

        if (null === $locale && null !== ($request = $this->requestStack->getMainRequest()))
        {
            $locale = $request->getLocale();
        }

        return $locale;
    }


    /**
     * @inheritDoc
     */
    public function getFunctions () : array
    {
        return [
            new TwigFunction("javascript_translations_init", [$this, "renderInit"], ["is_safe" => ["html"]]),
        ];
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedServices () : array
    {
        return [
            Environment::class,
        ];
    }
}
