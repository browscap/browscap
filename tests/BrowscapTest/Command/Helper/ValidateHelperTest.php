<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\ValidateHelper;
use JsonSchema\SchemaStorage;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Throwable;

use function realpath;
use function sprintf;

use const DIRECTORY_SEPARATOR;

class ValidateHelperTest extends TestCase
{
    private const STORAGE_DIR = 'storage';
    private ValidateHelper $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new ValidateHelper();
    }

    /** @throws ExpectationFailedException */
    public function testGetName(): void
    {
        static::assertSame('validate', $this->object->getName());
    }

    /** @throws ExpectationFailedException */
    public function testInvalidJsonSchema(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::once())
            ->method('critical')
            ->with('the given json schema is invalid', []);
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');
        $logger->expects(self::never())
            ->method('log');

        $resources = 'test-resource';
        $schemaUri = SchemaStorage::INTERNAL_PROVIDED_SCHEMA_URI;

        self::assertTrue($this->object->validate($logger, $resources, $schemaUri));
    }

    /** @throws ExpectationFailedException */
    public function testResourceDirNotFound(): void
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::once())
            ->method('critical')
            ->with(new IsInstanceOf(Throwable::class), []);
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::never())
            ->method('info');
        $logger->expects(self::never())
            ->method('debug');
        $logger->expects(self::never())
            ->method('log');

        $resources = 'test-resource';
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        self::assertTrue($this->object->validate($logger, $resources, $schemaUri));
    }

    /** @throws ExpectationFailedException */
    public function testResourceDirFoundButWithUnreadableFile(): void
    {
        $root = vfsStream::setup(self::STORAGE_DIR);

        $dir1 = vfsStream::newDirectory('b', 0755);
        $dir2 = vfsStream::newDirectory('d', 0755);

        $file1 = vfsStream::newFile('foo.txt', 0644);
        $file1->setContent('some text');

        $file2 = vfsStream::newFile('foo.json', 0044);
        $file2->setContent('[]');

        $file3 = vfsStream::newFile('test.json', 0644);
        $file3->setContent('{}');

        $dir1->addChild($file1);
        $dir2->addChild($file2);

        $root->addChild($dir1);
        $root->addChild($dir2);
        $root->addChild($file3);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::exactly(2))
            ->method('critical')
            ->willReturnMap(
                [
                    [sprintf('File "%s" is not readable', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file2->getName()), [], null],
                    [sprintf('File "%s" is not valid', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::exactly(2))
            ->method('info')
            ->willReturnMap(
                [
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file2->getName()), [], null],
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . 'test.json'), [], null],
                ],
            );
        $logger->expects(self::exactly(2))
            ->method('debug')
            ->willReturnMap(
                [
                    [sprintf('source file %s: validate', $root->url() . DIRECTORY_SEPARATOR . 'test.json'), [], null],
                    [sprintf('source file %s: parse with json parser', $root->url() . DIRECTORY_SEPARATOR . 'test.json'), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('log');

        $resources = $root->url();
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        self::assertTrue($this->object->validate($logger, $resources, $schemaUri));
    }

    /** @throws ExpectationFailedException */
    public function testResourceDirFoundButWithInvalidFile(): void
    {
        $root = vfsStream::setup(self::STORAGE_DIR);

        $dir1 = vfsStream::newDirectory('b', 0755);
        $dir2 = vfsStream::newDirectory('d', 0755);

        $file1 = vfsStream::newFile('foo.txt', 0644);
        $file1->setContent('some text');

        $file2 = vfsStream::newFile('foo.json', 0044);
        $file2->setContent('[]');

        $file3 = vfsStream::newFile('test.json', 0644);
        $file3->setContent('{}');

        $file4 = vfsStream::newFile('foo2.json', 0644);
        $file4->setContent('[\'\']');

        $dir1->addChild($file1);
        $dir2->addChild($file2);
        $dir2->addChild($file4);

        $root->addChild($dir1);
        $root->addChild($dir2);
        $root->addChild($file3);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::exactly(3))
            ->method('critical')
            ->willReturnMap(
                [
                    [sprintf('File "%s" is not readable', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file2->getName()), [], null],
                    [sprintf('validating File "%s" failed, because it had invalid JSON.', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file4->getName()), [], null],
                    [sprintf('File "%s" is not valid', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::exactly(3))
            ->method('info')
            ->willReturnMap(
                [
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file2->getName()), [], null],
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file4->getName()), [], null],
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                ],
            );
        $logger->expects(self::exactly(3))
            ->method('debug')
            ->willReturnMap(
                [
                    [sprintf('source file %s: validate', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file4->getName()), [], null],
                    [sprintf('source file %s: validate', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                    [sprintf('source file %s: parse with json parser', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('log');

        $resources = $root->url();
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        self::assertTrue($this->object->validate($logger, $resources, $schemaUri));
    }

    /** @throws ExpectationFailedException */
    public function testResourceDirFoundButWithDuplicateKeysInFile(): void
    {
        $root = vfsStream::setup(self::STORAGE_DIR);

        $dir1 = vfsStream::newDirectory('b', 0755);
        $dir2 = vfsStream::newDirectory('d', 0755);

        $file1 = vfsStream::newFile('foo.txt', 0644);
        $file1->setContent('some text');

        $file2 = vfsStream::newFile('foo.json', 0044);
        $file2->setContent('[]');

        $file3 = vfsStream::newFile('test.json', 0644);
        $file3->setContent('{"foo": "bar", "foo": "baz"}');

        $file4 = vfsStream::newFile('foo2.json', 0644);
        $file4->setContent('[\'\']');

        $dir1->addChild($file1);
        $dir2->addChild($file2);
        $dir2->addChild($file4);

        $root->addChild($dir1);
        $root->addChild($dir2);
        $root->addChild($file3);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $logger->expects(self::never())
            ->method('emergency');
        $logger->expects(self::never())
            ->method('alert');
        $logger->expects(self::exactly(4))
            ->method('critical')
            ->willReturnMap(
                [
                    [sprintf('File "%s" is not readable', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file2->getName()), [], null],
                    [sprintf('validating File "%s" failed, because it had invalid JSON.', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file4->getName()), [], null],
                    [sprintf('File "%s" is not valid', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                    [sprintf('parsing File "%s" failed, because it had invalid JSON.', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('error');
        $logger->expects(self::never())
            ->method('warning');
        $logger->expects(self::never())
            ->method('notice');
        $logger->expects(self::exactly(3))
            ->method('info')
            ->willReturnMap(
                [
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file2->getName()), [], null],
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file4->getName()), [], null],
                    [sprintf('source file %s: read', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                ],
            );
        $logger->expects(self::exactly(3))
            ->method('debug')
            ->willReturnMap(
                [
                    [sprintf('source file %s: validate', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file4->getName()), [], null],
                    [sprintf('source file %s: validate', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                    [sprintf('source file %s: parse with json parser', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('log');

        $resources = $root->url();
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        self::assertTrue($this->object->validate($logger, $resources, $schemaUri, $dir1->getName()));
    }
}
