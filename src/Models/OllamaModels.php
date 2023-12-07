<?php

namespace Mateodioev\OllamaBot\Models;

enum OllamaModels: int
{
    case codellama =  1;
    case codellamaCustom = 2;
    case llama2 = 3;

    public static function try(int $id): OllamaModels
    {
        return OllamaModels::tryFrom($id) ?? OllamaModels::codellamaCustom;
    }
}
