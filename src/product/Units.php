<?php

trait Units {
    abstract public function set_units() : array; // Method set_units should define rules to append units to the specified fields (if necessary) and return an array with values w/ units
}