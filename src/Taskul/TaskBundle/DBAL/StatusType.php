<?php

namespace Taskul\TaskBundle\DBAL;

use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class StatusType extends StringType
{
    public function getName()
    {
        return 'status_enum';
    }



    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return EnumStatusType::create($value);
    }
}