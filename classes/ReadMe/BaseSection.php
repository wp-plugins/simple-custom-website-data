<?php namespace Cwd\Readme;

abstract class BaseSection{

    public function md($varName)
    {
        if(!property_exists($this, $varName))
        {
            throw new \Exception("Property name provided doesn't exist");
        }
        return (new \Michelf\Markdown)->transform($this->{$varName});
    }

}