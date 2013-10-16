<?php

namespace Efficio\Dataset\Model;

/**
 * model annotations
 * naming convention: <what>_<job>_<for>_<label>
 *           example: FLAG_PROP_MODIFIER_VIRTUAL
 *                    this is a flag, it has no value
 *                         for a property
 *                              to modify the way it behaves
 *                                       labeled 'virtual'
 */
abstract class Annotations
{
    /**
     * virtual properties are not persisted, but may have getters and setters
     * and behave just like any other persited property.
     *
     * <code>
     * <?php
     *
     * class Book extends Model
     * {
     *    /**
     *      * this is *NOT* saved with the model
     *      * @virtual
     *      * /
     *     protected $backwards_title;
     *
     *    /**
     *      * this *IS* saved with the model
     *      * /
     *     protected $title;
     *
     *     protected getBackwardsTitle()
     *     {
     *         return strrev($this->title);
     *     }
     * }
     * </code>
     */
    const FLAG_PROP_MODIFIER_VIRTUAL = 'virtual';
}

