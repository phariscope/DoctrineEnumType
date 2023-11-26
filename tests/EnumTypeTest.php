<?php

namespace Phariscope\DoctrineEnumType\Tests;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Types\ConversionException;
use PHPUnit\Framework\TestCase;

class EnumTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('EnumExample', (new EnumExampleType())->getName());
    }

    public function testConvertToPHPValue(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);

        $type = new EnumExampleType();
        $example = $type->convertToPHPValue('EXAMPLE', $platform);
        $this->assertInstanceOf(EnumExample::class, $example);
    }

    public function testConvertToDatabase(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);

        $type = new EnumExampleType();
        $dbValue = $type->convertToDatabaseValue(EnumExample::FAKE, $platform);
        $this->assertEquals('FAKE', $dbValue);
    }

    public function testConvertToDatabaseConversionExceptionBadClass(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage("Bad class for 'bad'");

        $platform = new SqlitePlatform();

        $type = new EnumExampleType();
        $dbValue = $type->convertToDatabaseValue("bad", $platform);
    }

    public function testConvertToDatabaseConversionExceptionBadValue(): void
    {
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(
            "Not defined filed 'value' for 'Phariscope\DoctrineEnumType\Tests\NotEnumExample'"
        );

        $platform = new SqlitePlatform();

        $type = new NotEnumExampleType();
        $dbValue = $type->convertToDatabaseValue(new NotEnumExample(), $platform);
    }

    public function testSQLDeclarationMariaDB(): void
    {
        $platform = new MariaDBPlatform();

        $type = new EnumExampleType();
        $this->assertEquals("ENUM('EXAMPLE', 'FAKE', 'REAL')", $type->getSQLDeclaration([], $platform));
    }

    public function testSQLDeclarationMariaDBAndOther(): void
    {
        $platform = new SqlitePlatform();

        $type = new EnumExampleType();
        $this->assertEquals(
            "TEXT CHECK(example_column_name IN ('EXAMPLE', 'FAKE', 'REAL'))",
            $type->getSQLDeclaration(['name' => 'example_column_name'], $platform)
        );
    }

    public function testGetMappedDatabaseTypesWithPlatform(): void
    {
        $type = new EnumExampleType();
        $this->assertEquals([], $type->getMappedDatabaseTypes($this->createMock(AbstractPlatform::class)));
    }

    public function testGetMappedDatabaseTypesWithMariaDB(): void
    {
        $type = new EnumExampleType();
        $this->assertEquals(['enum'], $type->getMappedDatabaseTypes(new MariaDBPlatform()));
    }
}
