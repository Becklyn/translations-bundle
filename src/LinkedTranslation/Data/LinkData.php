<?php declare(strict_types=1);

namespace Becklyn\Translations\LinkedTranslation\Data;

use Becklyn\HtmlBuilder\Builder\HtmlBuilder;
use Becklyn\HtmlBuilder\Node\HtmlElement;
use Becklyn\HtmlBuilder\Node\SafeMarkup;
use Becklyn\IconLoader\Registry\IconRegistry;
use Becklyn\Rad\Exception\UnexpectedTypeException;
use Becklyn\Rad\Route\LinkableHandlerInterface;
use Becklyn\Rad\Route\LinkableInterface;

/**
 * @internal
 */
final class LinkData
{
    /** @var string */
    private $beforeTag;
    /** @var string */
    private $afterTag;
    /** @var LinkableInterface|string|null */
    private $href;
    /** @var string */
    private $class;
    /** @var SafeMarkup|string|null */
    private $beforeIcon;
    /** @var SafeMarkup|string|null */
    private $afterIcon;


    public function __construct (
        string $beforeTag,
        string $afterTag,
        $href,
        ?string $class = null,
        $beforeIcon = null,
        $afterIcon = null
    )
    {
        if (null !== $href && !$href instanceof LinkableInterface && !\is_string($href))
        {
            throw new UnexpectedTypeException($href, LinkableInterface::class . " or string or null");
        }

        if (null !== $beforeIcon && !$beforeIcon instanceof SafeMarkup && !\is_string($beforeIcon))
        {
            throw new UnexpectedTypeException($beforeIcon, SafeMarkup::class . " or string or null");
        }

        if (null !== $afterIcon && !$afterIcon instanceof SafeMarkup && !\is_string($afterIcon))
        {
            throw new UnexpectedTypeException($afterIcon, SafeMarkup::class . " or string or null");
        }

        $this->beforeTag = $beforeTag;
        $this->afterTag = $afterTag;
        $this->href = $href;
        $this->class = $class;
        $this->beforeIcon = $beforeIcon;
        $this->afterIcon = $afterIcon;
    }


    public function getBeforeTag () : string
    {
        return $this->beforeTag;
    }


    public function getAfterTag () : string
    {
        return $this->afterTag;
    }


    /**
     * Render the opening tag of the link and a optional icon.
     */
    public function renderParts (LinkableHandlerInterface $linkableHandler, IconRegistry $iconRegistry) : array
    {
        if (!$linkableHandler->isValidLinkTarget($this->href))
        {
            return [null, null];
        }

        $href = $this->href instanceof LinkableInterface
            ? $linkableHandler->generateUrl($this->href)
            : $this->href;

        if (null === $href)
        {
            return [null, null];
        }

        $attributes = [
            "href" => $href,
        ];

        if (null !== $this->class)
        {
            $attributes["class"] = $this->class;
        }

        $anchor = new HtmlElement("a", $attributes);

        if (null !== $this->beforeIcon)
        {
            $anchor->addContent($this->beforeIcon instanceof SafeMarkup
                ? $this->beforeIcon
                : new SafeMarkup($iconRegistry->get($this->beforeIcon)));
        }

        $anchor->addContent("##SPLIT##");

        if (null !== $this->afterIcon)
        {
            $anchor->addContent($this->afterIcon instanceof SafeMarkup
                ? $this->afterIcon
                : new SafeMarkup($iconRegistry->get($this->afterIcon)));
        }

        $anchorString = (new HtmlBuilder())->buildElement($anchor);

        return \explode("##SPLIT##", $anchorString);
    }
}
