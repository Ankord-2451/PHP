<?php

namespace Twig;

class TwigFilter {
    private $name;
    private $callback;

    public function __construct($name, $callback) {
        $this->name = $name;
        $this->callback = $callback;
    }

    public function getName() {
        return $this->name;
    }

    public function getCallback() {
        return $this->callback;
    }
}
