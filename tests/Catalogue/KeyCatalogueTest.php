<?php declare(strict_types=1);

namespace Tests\Becklyn\Translations\Catalogue;

use Becklyn\Translations\Catalogue\KeyCatalogue;
use PHPUnit\Framework\TestCase;

class KeyCatalogueTest extends TestCase
{
    /**
     * @return array
     */
    public function provideRegexify () : array
    {
        return [
            ["test", "/^test$/"],
            ["te*st", "/^te.*?st$/"],
            ["t*e*st", "/^t.*?e.*?st$/"],
            ["te****st", "/^te.*?st$/"],
        ];
    }


    /**
     * @dataProvider provideRegexify
     *
     * @param string $pattern
     * @param string $expectedRegex
     */
    public function testRegexify (string $pattern, string $expectedRegex) : void
    {
        $catalogue = new KeyCatalogue();
        $catalogue->add("a", [$pattern]);

        $actual = $catalogue->getPatterns()["a"][0];
        self::assertSame($expectedRegex, $actual);
    }
}
