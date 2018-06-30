<?php

namespace Jikan\Parser\Person;

use Jikan\Helper\JString;
use Jikan\Model;
use Jikan\Parser\ParserInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class PersonParser
 *
 * @package Jikan\Parser
 */
class PersonParser implements ParserInterface
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * PersonParser constructor.
     *
     * @param Crawler $crawler
     */
    public function __construct(Crawler $crawler)
    {
        $this->crawler = $crawler;
    }

    /**
     * Return the model
     */
    public function getModel(): Model\Person
    {
        return Model\Person::fromParser($this);
    }


    /**
     * @return int
     * @throws \RuntimeException
     */
    public function getPersonId(): int
    {
        preg_match('#https?://myanimelist.net/people/(\d+)#', $this->getPersonURL(), $matches);

        return (int)$matches[1];
    }

    /**
     * @return string
     */
    public function getPersonURL(): string
    {
        return $this->crawler->filterXPath('//meta[@property=\'og:url\']')->attr('content');
    }

    /**
     * @return string
     */
    public function getPersonName(): string
    {
        return $this->crawler->filterXPath('//meta[@property=\'og:title\']')->attr('content');
    }

    /**
     * @return string
     */
    public function getPersonImageURL(): string
    {
        return $this->crawler->filterXPath('//meta[@property=\'og:image\']')->attr('content');
    }

    /**
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function getPersonGivenName(): ?string
    {
        $node = $this->crawler
            ->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]')
            ->filterXPath('//span[text()="Given name:"]');
            
        if (!$node->count()) {
            return null;
        }

        return JString::cleanse(
            str_replace($node->text(), '', $node->parents()->text())
        );
    }

    /**
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function getPersonFamilyName(): ?string
    {
        $node = $this->crawler
            ->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]')
            ->filterXPath('//span[text()="Family name:"]');

        if (!$node->count()) {
            return null;
        }

        // MAL screwed up the HTML here
        preg_match('~Family name:(.*?)(Alternate names|Birthday|Website|Member Favorites|More)~', $node->parents()->text(), $matches);

        if (empty($matches)) {
            return null;
        }

        $familyName = JString::cleanse($matches[1]);

        if (empty($familyName)) { // MAL has it empty at some places
            return null;
        }

        return $familyName;
    }

    /**
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function getPersonAlternateNames(): ?string
    {
        $node = $this->crawler
            ->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]')
            ->filterXPath('//span[text()="Alternate names:"]');

        if (!$node->count()) {
            return null;
        }

        return JString::cleanse(
            str_replace($node->text(), '', $node->parents()->text())
        );
    }

    /**
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function getPersonWebsite(): ?string
    {
        $node = $this->crawler
            ->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]')
            ->filterXPath('//span[text()="Website:"]');


        $website = $node->nextAll()->filter('a');

        if (!$website->count()) {
            return null;
        }

        // MAL returns an empty `<a href="http://"></a>` when there's no website
        if (empty($website->text())) {
            return null;
        }


        return $website->attr('href');
    }

    /**
     * @return int|null
     * @throws \InvalidArgumentException
     */
    public function getPersonFavorites(): ?int
    {
        $node = $this->crawler
            ->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]')
            ->filterXPath('//span[text()="Member Favorites:"]');


        if (!$node->count()) {
            return null;
        }

        return (int) JString::cleanse(
            str_replace([$node->text(), ','], '', $node->parents()->text())
        );
    }

    /**
     * @return string|null
     * @throws \InvalidArgumentException
     */
    public function getPersonAbout(): ?string
    {
        $node = $this->crawler
            ->filterXPath('//div[@id="content"]/table/tr/td[@class="borderClass"]')
            ->filter('.people-informantion-more');

        if (!$node->count()) {
            return null;
        }

        if (empty($node->text())) {
            return null;
        }

        return JString::cleanse(
            $node->html()
        );
    }

    // /**
    //  * @return Model\Animeography[]
    //  * @throws \InvalidArgumentException
    //  */
    // public function getPersonVoiceActingRole(): array
    // {
    //     return $this->crawler
    //         ->filterXPath('//div[contains(text(), \'Voice Acting Roles\')]/../table[1]/tr')
    //         ->each(
    //             function (Crawler $c) {
    //                 return (new VoiceActingRole($c))->getModel();
    //             }
    //         );
    // }

    // /**
    //  * @return Model\Mangaography[]
    //  * @throws \InvalidArgumentException
    //  */
    // public function getPersonAnimeStaffPositions(): array
    // {
    //     return $this->crawler
    //         ->filterXPath('//div[contains(text(), \'Animeography\')]/../table[2]/tr')
    //         ->each(
    //             function (Crawler $c) {
    //                 return (new Mangaography($c))->getModel();
    //             }
    //         );
    // }

    // /**
    //  * @return Model\VoiceActor[]
    //  * @throws \InvalidArgumentException
    //  */
    // public function getPersonPublishedManga(): array
    // {
    //     return $this->crawler
    //         ->filterXPath('//div[contains(text(), \'Voice Actors\')]/../table/tr')
    //         ->each(
    //             function (Crawler $c) {
    //                 return (new VoiceActor($c))->getModel();
    //             }
    //         );
    // }
}