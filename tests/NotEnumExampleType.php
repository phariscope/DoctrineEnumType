<?php

namespace Phariscope\DoctrineEnumType\Tests;

use Phariscope\DoctrineEnumType\EnumType;

class NotEnumExampleType extends EnumType
{
    protected string $className = NotEnumExample::class;
}
