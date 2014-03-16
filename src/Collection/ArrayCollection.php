<?php

namespace Efficio\Dataset\Collection;

use ArrayObject;
use Efficio\Dataset\Collection;
use Efficio\Dataset\Access\CollectionAccess;

/**
 * base for other array collections
 */
abstract class ArrayCollection extends ArrayObject implements Collection
{
    use CollectionAccess;
}
