<?php declare(strict_types=1);

namespace Becklyn\Translations\LinkedTranslation;

use Becklyn\HtmlBuilder\Node\SafeMarkup;
use Becklyn\IconLoader\Registry\IconRegistry;
use Becklyn\Rad\Route\LinkableHandlerInterface;
use Becklyn\Rad\Route\LinkableInterface;
use Becklyn\Translations\Exception\DuplicateLinkedTranslationTagException;
use Becklyn\Translations\LinkedTranslation\Data\LinkData;

class LinkedTranslationBuilder
{
    private $linkableHandler;
    private $iconRegistry;
    /** @var LinkData[] */
    private $linkData = [];
    /** @var string[] */
    private $usedTags = [];


    public function __construct (
        LinkableHandlerInterface $linkableHandler,
        IconRegistry $iconRegistry
    )
    {

        $this->linkableHandler = $linkableHandler;
        $this->iconRegistry = $iconRegistry;
    }


    /**
     * @param LinkableInterface|string|null $href
     * @param SafeMarkup|string|null        $iconBefore
     * @param SafeMarkup|string|null        $iconAfter
     */
    public function link (
        string $beforeTag,
        string $afterTag,
        $href,
        ?string $class = null,
        $iconBefore = null,
        $iconAfter = null
    ) : self
    {
        if (\in_array($beforeTag, $this->usedTags, true))
        {
            throw new DuplicateLinkedTranslationTagException($beforeTag);
        }

        $this->usedTags[] = $beforeTag;

        if (\in_array($afterTag, $this->usedTags, true))
        {
            throw new DuplicateLinkedTranslationTagException($afterTag);
        }

        $this->usedTags[] = $afterTag;
        $this->linkData[] = new LinkData($beforeTag, $afterTag, $href, $class, $iconBefore, $iconAfter);

        return $this;
    }


    /**
     * Generates the parameters that fill the translation with data.
     */
    public function generateTranslationParameters () : array
    {
        $parameters = [];

        foreach ($this->linkData as $linkData)
        {
            [$before, $after] = $linkData->renderParts($this->linkableHandler, $this->iconRegistry);

            $parameters[$linkData->getBeforeTag()] = $before;
            $parameters[$linkData->getAfterTag()] = $after;
        }

        return $parameters;
    }
}
