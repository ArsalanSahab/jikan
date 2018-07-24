<?php

namespace Jikan\Request\Top;

use Jikan\Request\RequestInterface;
use Jikan\Helper\Constants;

class TopAnimeRequest implements RequestInterface
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
                Constants::TOP_AIRING,
                Constants::TOP_UPCOMING,
                Constants::TOP_TV,
                Constants::TOP_MOVIE,
                Constants::TOP_OVA,
                Constants::TOP_SPECIAL,
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
        return 'https://myanimelist.net/topanime.php?'.http_build_query(
            [
                'limit' => 50 * ($this->page-1),
                'type' => $this->type
            ]
        );
    }
}
