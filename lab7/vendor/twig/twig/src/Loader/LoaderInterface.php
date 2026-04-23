<?php

namespace Twig\Loader;

interface LoaderInterface {
    public function getSource($name);
    public function exists($name);
}
