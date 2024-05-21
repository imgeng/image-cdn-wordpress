<?php

declare(strict_types=1);

namespace ImageEngine\PhpSdk\Storage;

class FileStorage implements StorageInterface
{
    private string $dir = __DIR__ . '/../../storage/';

    /**
     * @throws \Exception
     */
    public function __construct(string $dir = null)
    {
        if ($dir && $this->checkDir($dir)) {
            $this->dir = $dir;
        }
    }

    /**
     *
     *
     * @throws \Exception
     *
     */
    private function checkDir(string $dir): bool
    {
        return is_dir($dir);
    }

    /**
     *
     * @return void
     *
     * @throws \Exception
     *
     */
    private function checkFileName(string $key)
    {
        if (!preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $key)) {
            throw new \Exception("Bad key/filename");
        }
    }

    /**
     *
     * @return string|false
     *
     * @throws \Exception
     *
     */
    public function get(string $key)
    {
        $this->checkFileName($key);

        return file_get_contents($this->dir . $key);
    }

    /**
     *
     * @return int|bool
     *
     * @throws \Exception
     *
     */
    public function set(string $key, string $value)
    {
        $this->checkFileName($key);
        return file_put_contents($this->dir . $key, $value);
    }

    /**
     *
     * @return bool
     *
     * @throws \Exception
     *
     */
    public function delete(string $key)
    {
        $this->checkFileName($key);
        return unlink($this->dir . $key);
    }
}
