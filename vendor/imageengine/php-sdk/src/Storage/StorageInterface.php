<?php

namespace ImageEngine\PhpSdk\Storage;

interface StorageInterface
{
    /**
     *
     * @return string|false
     *
     * @throws \Exception
     *
     */
    public function get(string $key);

    /**
     *
     * @return int|bool
     *
     * @throws \Exception
     *
     */
    public function set(string $key, string $value);

    /**
     *
     * @return bool
     *
     * @throws \Exception
     *
     */
    public function delete(string $key);
}
