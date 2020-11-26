<?php declare(strict_types=1);

namespace Becklyn\Translations\LinkedTranslation;

use Becklyn\IconLoader\Registry\IconRegistry;
use Becklyn\Rad\Route\LinkableHandlerInterface;

final class LinkedTranslationBuilderFactory
{
    private $linkableHandler;
    private $iconRegistry;


    public function __construct (
        LinkableHandlerInterface $linkableHandler,
        IconRegistry $iconRegistry
    )
    {
        $this->linkableHandler = $linkableHandler;
        $this->iconRegistry = $iconRegistry;
    }


    public function create () : LinkedTranslationBuilder
    {
        return new LinkedTranslationBuilder($this->linkableHandler, $this->iconRegistry);
    }
}
