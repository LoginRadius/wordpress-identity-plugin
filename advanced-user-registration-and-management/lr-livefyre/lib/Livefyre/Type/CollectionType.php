<?php

namespace LRLivefyre\Type;


use LRLivefyre\Pattern\BasicEnum;

abstract class CollectionType extends BasicEnum {
    const REVIEWS = "reviews";
    const SIDENOTES = "sidenotes";
    const RATINGS = "ratings";
    const COUNTING = "counting";
    const BLOG = "liveblog";
    const CHAT = "livechat";
    const COMMENTS = "livecomments";
}
