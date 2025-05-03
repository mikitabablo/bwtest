<?php

namespace App\Services\FileReader;

use Exception;

class FileReader implements IFileReader
{
    private $filePath;
    private $file;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->file = fopen($filePath, 'r');
        if ($this->file === false) {
            throw new Exception(sprintf(
                'Failed to read the file: %s',
                $this->filePath
            ));
        }
    }

    public function readLine(): ?string
    {
        if ($this->file === null) {
            return null;
        }

        $line = fgets($this->file);
        if ($line !== false) {
            return $line;
        }

        if (!feof($this->file)) {
            $errorMessage = sprintf(
                "Falied to read a line in file: %s",
                $this->filePath,
            );

            $error = error_get_last();
            if ($error && isset($error['message'])) {
                $errorMessage = sprintf(
                    '%s; Error message: %s',
                    $errorMessage,
                    $error['message'],
                );
            }

            throw new Exception($errorMessage);
        }

        return null;
    }

    public function __destruct()
    {
        if ($this->file) {
            fclose($this->file);
            $this->file = null;
        }
    }
}