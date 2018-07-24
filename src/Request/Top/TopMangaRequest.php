<?php

namespace Jikan\Request\Top;

use Jikan\Request\RequestInterface;
use Jikan\Helper\Constants;

class TopMangaRequest implements RequestInterface
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var string|null
     */
    private $type;

    /**
     * TopAnimeRequest constructor.
     *
     * @param int $page
     */
    public function __construct(int $page = 1, $type = null)
    {
        $this->page = $page;

        if (!is_null($type)) {
            if (!\in_array($type, [
                Constants::TOP_MANGA,
                Constants::TOP_NOVEL,
                Constants::TOP_ONE_SHOT,
                Constants::TOP_DOUJINSHI,
                Constants::TOP_MANHWA,
                Constants::TOP_MANHUA,
                Constants::TOP_BY_POPULARITY,
                Constants::TOP_BY_FAVORITES,
            ])) {
                throw new \InvalidArgumentException(sprintf('Type %s is not valid', $type));
            }

            $this->type = $type;
        }
    }

    /**
     * Get the path to request
     *
     * @return string
     */
    public function getPath(): string
    {
        return 'https://myanimelist.net/topmanga.php?'.http_build_query(
            [
                    'limit' => 50 * ($this->page-1),
                    'type' => $this->type
            ]
        );
    }
}
