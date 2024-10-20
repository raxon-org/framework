<?php
namespace Raxon\Attribute;

#[\Attribute]
class Argument
{
    public function __construct(
        public string $apply,
        public int | string $count=1,
        public int | array $index=0
    ) {
    }
}