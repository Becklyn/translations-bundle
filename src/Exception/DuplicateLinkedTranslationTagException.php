<?php declare(strict_types=1);

namespace Becklyn\Translations\Exception;

class DuplicateLinkedTranslationTagException extends \InvalidArgumentException
{
    public function __construct (string $tag)
    {
        parent::__construct(\sprintf("The tag %s was already added to the LinkedTranslationBuilder", $tag));
    }
}
