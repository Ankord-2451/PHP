<?php

namespace Twig\Loader;

class FilesystemLoader implements LoaderInterface {
    private $paths = [];

    public function __construct($path) {
        $this->paths[] = $path;
    }

    public function getSource($name) {
        foreach ($this->paths as $path) {
            $file = $path . '/' . $name;
            if (file_exists($file)) {
                $content = file_get_contents($file);
                // Strip UTF-8 BOM if present
                $content = preg_replace('/^\x{EF}\x{BB}\x{BF}/', '', $content);
                return $content;
            }
        }
        throw new \Exception("Template not found: $name");
    }

    public function exists($name) {
        foreach ($this->paths as $path) {
            if (file_exists($path . '/' . $name)) {
                return true;
            }
        }
        return false;
    }
}
