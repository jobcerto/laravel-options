<?php

use Jobcerto\Options\OptionsFactory;

if ( ! function_exists('options')) {
    function options(...$args)
    {

        $factory = new OptionsFactory(config('options.model'));

        if (count($args) == 1) {
            [$key] = $args;

            if (str_contains($key, '.')) {
                return $factory->search($key);
            }

            return $factory->get($key);
        }

        if (count($args) == 2) {
            [$key, $value] = $args;

            return $factory->set($key, $value);
        }

        if (count($args) > 2) {
            throw new \Exception('You can\'t pass more then two arguments');
        }

        return $factory;
    }
}
