<?php namespace Cwd\Readme;

class SubSection extends BaseSection{

    public function __construct(array $data)
    {
        foreach($data as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

}