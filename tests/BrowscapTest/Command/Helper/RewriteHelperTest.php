<?php

declare(strict_types=1);

namespace BrowscapTest\Command\Helper;

use Browscap\Command\Helper\RewriteHelper;
use Browscap\Command\Helper\Sorter;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentSize;
use Ergebnis\Json\Normalizer\Exception\InvalidIndentStyle;
use Ergebnis\Json\Normalizer\Exception\InvalidJsonEncodeOptions;
use Ergebnis\Json\Normalizer\Exception\InvalidNewLineString;
use JsonException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use Symfony\Component\Console\Helper\HelperSet;
use Throwable;

use function realpath;
use function sprintf;

use const DIRECTORY_SEPARATOR;

class RewriteHelperTest extends TestCase
{
    private const STORAGE_DIR = 'storage';
    private RewriteHelper $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new RewriteHelper();
    }

    /**
     * @throws InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetName(): void
    {
        static::assertSame('rewrite', $this->object->getName());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidNewLineString
     * @throws InvalidJsonEncodeOptions
     * @throws InvalidIndentStyle
     * @throws InvalidIndentSize
     */
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
        $logger->expects(self::exactly(2))
            ->method('debug')
            ->willReturnMap(
                [
                    ['initialize rewrite helper', [], null],
                    ['initialize file finder', [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('log');

        $resources = 'test-resource';
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        $this->object->rewrite($logger, $resources, $schemaUri);
    }

    /**
     * @throws InvalidNewLineString
     * @throws InvalidJsonEncodeOptions
     * @throws InvalidIndentStyle
     * @throws InvalidIndentSize
     */
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
                    [sprintf('normalizing File "%s" failed, because it had invalid JSON.', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), [], null],
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
        $logger->expects(self::exactly(3))
            ->method('debug')
            ->willReturnMap(
                [
                    ['initialize rewrite helper', [], null],
                    ['initialize file finder', [], null],
                    [sprintf('source file %s: normalize content', $root->url() . DIRECTORY_SEPARATOR . 'test.json'), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('log');

        $resources = $root->url();
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        $this->object->rewrite($logger, $resources, $schemaUri);
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidNewLineString
     * @throws InvalidJsonEncodeOptions
     * @throws InvalidIndentStyle
     * @throws InvalidIndentSize
     */
    public function testResourceDirFoundButWithUnreadableFileAndSorting(): void
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

        $exception = new JsonException('test');

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
                    [sprintf('sorting File "%s" failed, because it had invalid JSON.', $root->url() . DIRECTORY_SEPARATOR . $file3->getName()), ['Exception' => $exception], null],
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
        $logger->expects(self::exactly(3))
            ->method('debug')
            ->willReturnMap(
                [
                    ['initialize rewrite helper', [], null],
                    ['initialize file finder', [], null],
                    [sprintf('source file %s: sort content', $root->url() . DIRECTORY_SEPARATOR . 'test.json'), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('log');

        $sortHelper = $this->getMockBuilder(Sorter::class)
            ->getMock();
        $sortHelper->expects(self::once())
            ->method('sort')
            ->willThrowException($exception);

        $helperSet = $this->getMockBuilder(HelperSet::class)
            ->getMock();
        $helperSet->expects(self::once())
            ->method('get')
            ->with('sorter')
            ->willReturn($sortHelper);

        $resources = $root->url();
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        $this->object->setHelperSet($helperSet);

        $this->object->rewrite($logger, $resources, $schemaUri, true);
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidNewLineString
     * @throws InvalidJsonEncodeOptions
     * @throws InvalidIndentStyle
     * @throws InvalidIndentSize
     */
    public function testResourceDirFoundButWithWritingFile(): void
    {
        $root = vfsStream::setup(self::STORAGE_DIR);

        $dir1 = vfsStream::newDirectory('b', 0755);
        $dir2 = vfsStream::newDirectory('d', 0755);

        $file1 = vfsStream::newFile('foo.txt', 0644);
        $file1->setContent('some text');

        $file2 = vfsStream::newFile('foo.json', 0044);
        $file2->setContent('[]');

        $file3 = vfsStream::newFile('test.json', 0644);
        $file3->setContent("{\n\"access\": {\n\"type\": \"application\",\n\"properties\": {\n\"Browser\": \"Access\",\n\"Browser_Maker\": \"Microsoft Corporation\"\n},\n\"lite\": false,\n\"standard\": true\n},\n\"1password\": {\n\"type\": \"application\",\n\"properties\": {\n\"Browser\": \"1Password\",\n\"Browser_Maker\": \"AgileBits, Inc.\"\n},\n\"lite\": false,\n\"standard\": true\n}\n}");

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
        $logger->expects(self::once())
            ->method('critical')
            ->with(sprintf('File "%s" is not readable', $root->url() . DIRECTORY_SEPARATOR . $dir2->getName() . DIRECTORY_SEPARATOR . $file2->getName()));
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
        $logger->expects(self::exactly(4))
            ->method('debug')
            ->willReturnMap(
                [
                    ['initialize rewrite helper', [], null],
                    ['initialize file finder', [], null],
                    [sprintf('source file %s: normalize content', $root->url() . DIRECTORY_SEPARATOR . 'test.json'), [], null],
                    [sprintf('source file %s: write content', $root->url() . DIRECTORY_SEPARATOR . 'test.json'), [], null],
                ],
            );
        $logger->expects(self::never())
            ->method('log');

        $resources = $root->url();
        $schemaUri = 'file://' . realpath(__DIR__ . '/../../../../schema/browsers.json');

        $this->object->rewrite($logger, $resources, $schemaUri);

        self::assertSame("{\n  \"1password\": {\n    \"lite\": false,\n    \"properties\": {\n      \"Browser\": \"1Password\",\n      \"Browser_Maker\": \"AgileBits, Inc.\"\n    },\n    \"standard\": true,\n    \"type\": \"application\"\n  },\n  \"access\": {\n    \"lite\": false,\n    \"properties\": {\n      \"Browser\": \"Access\",\n      \"Browser_Maker\": \"Microsoft Corporation\"\n    },\n    \"standard\": true,\n    \"type\": \"application\"\n  }\n}\n", $file3->getContent());
    }
}
