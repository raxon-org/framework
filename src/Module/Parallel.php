<?php
namespace Raxon\Module;


use Spatie\Fork\Fork;
use Spatie\Fork\Task;

class Parallel extends Fork {

    public static function new(): self
    {
        return new self();
    }

    public function execute($callables = []): array
    {
        $tasks = [];
        foreach ($callables as $nr => $callable) {
            $tasks[] = Task::fromCallable($callable, $nr);
        }

        return $this->waitFor(...$tasks);
    }

}