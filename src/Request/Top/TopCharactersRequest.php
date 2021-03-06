<?php

namespace Jikan\Request\Top;

use Jikan\Request\RequestInterface;

/**
 * Class TopCharactersRequest
 *
 * @package Jikan\Request\Top
 */
class TopCharactersRequest implements RequestInterface
{
    /**
     * @var int
     */
    private $page;

    /**
     * TopAnimeRequest constructor.
     *
     * @param int $page
     */
    public function __construct(int $page = null)
    {
        $this->page = $page;
    }

    /**
     * Get the path to request
     *
     * @return string
     */
    public function getPath(): string
    {
        return 'https://myanimelist.net/character.php?'.http_build_query(['limit' => 50 * $this->page]);
    }
}
