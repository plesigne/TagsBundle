<?php

declare(strict_types=1);

namespace Netgen\TagsBundle\API\Repository\Events\Tags;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\API\Repository\Values\Tags\TagCreateStruct;
use UnexpectedValueException;

use function sprintf;

final class BeforeCreateTagEvent extends BeforeEvent
{
    private TagCreateStruct $tagCreateStruct;

    private Tag $tag;

    public function __construct(TagCreateStruct $tagCreateStruct)
    {
        $this->tagCreateStruct = $tagCreateStruct;
    }

    public function getTagCreateStruct(): TagCreateStruct
    {
        return $this->tagCreateStruct;
    }

    public function getTag(): Tag
    {
        if (!isset($this->tag)) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not a type of %s. Check with hasTag() or set it with setTag() before you call the getter.', Tag::class));
        }

        return $this->tag;
    }

    public function setTag(Tag $tag): void
    {
        $this->tag = $tag;
    }

    public function hasTag(): bool
    {
        return isset($this->tag);
    }
}
