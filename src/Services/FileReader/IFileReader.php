<?php

namespace App\Services\FileReader;

interface IFileReader
{
    public function readLine(): ?string;
}