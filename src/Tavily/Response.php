<?php

declare(strict_types=1);

namespace Mateodioev\OllamaBot\Tavily;

/**
 * @property string $query
 * @property Result[] $results
 * @property mixed $follow_up_questions
 * @property mixed $answer
 * @property mixed $images
 */
class Response extends BaseObj
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->data['results'] = Result::mapArray($this->data['results']);
    }
}
