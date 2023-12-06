<?php

namespace Mateodioev\OllamaBot\Ollama;

final class OllamaParameters
{
    public array $opts = [];

    public static function fromArray(array $parameters): OllamaParameters
    {
        $obj       = new self();
        $obj->opts = $parameters;

        return $obj;
    }

    public static function default(): OllamaParameters
    {
        return new self();
    }

    public function toArray(): array
    {
        return $this->opts;
    }

    public function modifyOpt(string $key, mixed $value): OllamaParameters
    {
        $this->opts[$key] = $value;
        return $this;
    }
    public function setMirostat(int $mirostat = 0): OllamaParameters
    {
        return $this->modifyOpt('mirostat', $mirostat);
    }

    public function setMirostatEta(float $mirostatEta = 0.1): OllamaParameters
    {
        return $this->modifyOpt('mirostat_eta', $mirostatEta);
    }

    public function setMirostatTau(float $mirostatTau = 5.0): OllamaParameters
    {
        return $this->modifyOpt('mirostat_tau', $mirostatTau);
    }

    public function setNumCtx(int $numCtx = 2048): OllamaParameters
    {
        return $this->modifyOpt('num_ctx', $numCtx);
    }

    public function setNumGqa(int $numGqa): OllamaParameters
    {
        return $this->modifyOpt('num_gqa', $numGqa);
    }

    public function setNumGpu(int $numGpu = 1): OllamaParameters
    {
        return $this->modifyOpt('num_gpu', $numGpu);
    }

    public function setNumThread(int $numThread): OllamaParameters
    {
        return $this->modifyOpt('num_thread', $numThread);
    }

    public function setRepeatLastN(int $repeatLastN = 64): OllamaParameters
    {
        return $this->modifyOpt('repeat_last_n', $repeatLastN);
    }

    public function setRepeatPenalty(int $repeatPenalty): OllamaParameters
    {
        return $this->modifyOpt('repeat_penalty', $repeatPenalty);
    }

    public function setTemperature(float $temperature = 0.8): OllamaParameters
    {
        return $this->modifyOpt('temperature', $temperature);
    }

    public function setSeed(int $seed = 0): OllamaParameters
    {
        return $this->modifyOpt('seed', $seed);
    }

    public function setStop(string $stop): OllamaParameters
    {
        return $this->modifyOpt('stop', $stop);
    }

    public function setTfsZ(float $tfsZ = 1.0): OllamaParameters
    {
        return $this->modifyOpt('tfs_z', $tfsZ);
    }

    public function setNumPredict(int $numPredict = 128): OllamaParameters
    {
        return $this->modifyOpt('num_predict', $numPredict);
    }

    public function setTopK(int $topK = 40): OllamaParameters
    {
        return $this->modifyOpt('top_k', $topK);
    }

    public function setTopP(float $topP = 0.9): OllamaParameters
    {
        return $this->modifyOpt('top_p', $topP);
    }
}
