<?php

namespace Formwork\Files;

use Formwork\Data\AssociativeCollection;
use Formwork\Utils\FileSystem;

class Files extends AssociativeCollection
{
    /**
     * Filter files by a given type
     *
     * @param string $type
     *
     * @return self
     */
    public function filterByType($type)
    {
        $files = clone $this;
        $files->items = array_filter($files->items, static function ($item) use ($type) {
            return $item->type() === $type;
        });
        return $files;
    }

    /**
     * Create a collection getting files from a given path
     *
     * @param string     $path
     * @param array|null $filenames Array of file names to include (all files by default)
     *
     * @return self
     */
    public static function fromPath($path, array $filenames = null)
    {
        if (is_null($filenames)) {
            $filenames = FileSystem::listFiles($path);
        }

        $files = array();

        foreach ($filenames as $filename) {
            $files[$filename] = new File($path . $filename);
        }

        return new static($files);
    }
}
