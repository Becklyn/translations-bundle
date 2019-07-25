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
        $catalogue->add("n", "a", [$pattern]);

        $actual = $catalogue->getPatterns("n")["a"][0];
        self::assertSame($expectedRegex, $actual);
    }


    /**
     * Test namespaced access
     */
    public function testNamespaces ()
    {
        $catalogue = new KeyCatalogue();
        $catalogue->addNamespaceGrouped([
            "a" => [
                "messages" => ["a"],
            ],
            "b" => [
                "messages" => ["b"],
            ],
        ]);

        self::assertSame(["messages" => ["/^a$/"]], $catalogue->getPatterns("a"));
        self::assertSame(["messages" => ["/^b$/"]], $catalogue->getPatterns("b"));
        self::assertSame([], $catalogue->getPatterns("missing"));

    }
}
