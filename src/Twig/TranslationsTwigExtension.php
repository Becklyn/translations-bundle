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
    /**
     * @var ContainerInterface
     */
    private $locator;


    /**
     * @var RequestStack
     */
    private $requestStack;


    /**
     * @param RequestStack $requestStack
     */
    public function __construct (ContainerInterface $locator, RequestStack $requestStack)
    {
        $this->locator = $locator;
        $this->requestStack = $requestStack;
    }


    /**
     * @param string|null $locale
     *
     * @return string
     */
    public function renderInit (string $namespace, ?string $locale = null) : string
    {
        if (null === $locale)
        {
            $request = $this->requestStack->getMasterRequest();

            if (null === $request)
            {
                return "<-- Can't embed, because no locale given -->";
            }

            $locale = $request->getLocale();
        }

        return $this->locator->get(Environment::class)->render("@BecklynTranslations/init.html.twig", [
            "locale" => $locale,
            "namespace" => $namespace,
        ]);
    }


    /**
     * @inheritDoc
     */
    public function getFunctions ()
    {
        return [
            new TwigFunction("javascript_translations_init", [$this, "renderInit"], ["is_safe" => ["html"]]),
        ];
    }


    /**
     * @inheritDoc
     */
    public static function getSubscribedServices ()
    {
        return [
            Environment::class,
        ];
    }
}
