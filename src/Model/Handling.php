<?php

namespace Efficio\Dataset\Model;

interface Handling
{
    /**
     * saves a model. returns save success
     * @return boolean
     */
    public function save();

    /**
     * deletes a model. returns delete success
     * @return boolean
     */
    public function delete();

    /**
     * shortcut method. updates properties and saves a model
     * @param array $updates
     * @param boolean $save
     */
    public function update(array $updates, $save = false);
}
