<?php

namespace App\Traits;

trait SingletonTrait
{
    private static $instance;

    public static function getInstance(...$args): self
    {
        if(is_null(self::$instance)) {
            self::$instance = new static(...$args);
        };

        return self::$instance;
    }

    protected function __construct(...$args)
    {
        $parent = get_parent_class($this);

        if($parent && method_exists($parent, '__construct')) {
            parent::__construct(...$args);
        }
    }
}
