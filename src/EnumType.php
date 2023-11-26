<?php

namespace Phariscope\DoctrineEnumType;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Infection\Mutator\Boolean\InstanceOf_;

use function SafePHP\strval;

abstract class EnumType extends Type
{
    protected string $name = '';
    protected string $className;

    public function getName()
    {
        $name = $this->name == '' ? $this->getClassName() : $this->name;
        return $name;
    }

    private function getClassName(): string
    {
        return substr($this->className, strrpos($this->className, '\\') + 1);
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $cases = $this->className::cases();
        $values = array_map(function ($val) {
            return "'" . $val->value . "'";
        }, $cases);
        if ($platform instanceof MariaDBPlatform) {
            return "ENUM(" . implode(", ", $values) . ")";
        }
        return "TEXT CHECK(" . $column['name'] . " IN (" . implode(", ", $values) . "))";
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param string            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $this->className::tryFrom($value);
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!($value instanceof $this->className)) {
            throw new ConversionException(sprintf("Bad class for '%s'", strval($value)));
        }
        if (isset($value->value)) {
            return $value->value;
        }

        throw new ConversionException(sprintf("Not defined filed 'value' for '%s'", get_class($value)));
    }

    /**
     * Gets an array of database types that map to this Doctrine type.
     *
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        if ($platform instanceof MariaDBPlatform) {
            return ['enum'];
        }
        return [];
    }
}
