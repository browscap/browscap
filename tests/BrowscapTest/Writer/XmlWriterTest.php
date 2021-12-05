<?php

declare(strict_types=1);

namespace BrowscapTest\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\PropertyHolder;
use Browscap\Data\UserAgent;
use Browscap\Filter\FullFilter;
use Browscap\Filter\StandardFilter;
use Browscap\Formatter\XmlFormatter;
use Browscap\Writer\WriterInterface;
use Browscap\Writer\XmlWriter;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function assert;
use function date;
use function file_get_contents;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class XmlWriterTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    /** @var XmlWriter */
    private $object;

    /** @var string */
    private $file;

    protected function setUp(): void
    {
        vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.xml';

        $logger = $this->createMock(LoggerInterface::class);

        assert($logger instanceof LoggerInterface);
        $this->object = new XmlWriter($this->file, $logger);
    }

    protected function teardown(): void
    {
        $this->object->close();

        unlink($this->file);
    }

    /**
     * tests getting the writer type
     */
    public function testGetType(): void
    {
        static::assertSame(WriterInterface::TYPE_XML, $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     */
    public function testSetGetFormatter(): void
    {
        $mockFormatter = $this->createMock(XmlFormatter::class);

        assert($mockFormatter instanceof XmlFormatter);
        $this->object->setFormatter($mockFormatter);
        static::assertSame($mockFormatter, $this->object->getFormatter());
    }

    /**
     * tests setting and getting a filter
     */
    public function testSetGetFilter(): void
    {
        $mockFilter = $this->createMock(FullFilter::class);

        assert($mockFilter instanceof FullFilter);
        $this->object->setFilter($mockFilter);
        static::assertSame($mockFilter, $this->object->getFilter());
    }

    /**
     * tests setting a file into silent mode
     */
    public function testSetGetSilent(): void
    {
        $this->object->setSilent(true);
        static::assertTrue($this->object->isSilent());
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStartIfNotSilent(): void
    {
        $this->object->setSilent(false);

        $this->object->fileStart();
        static::assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<browsercaps>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStartIfSilent(): void
    {
        $this->object->setSilent(true);

        $this->object->fileStart();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEndIfNotSilent(): void
    {
        $this->object->setSilent(false);

        $this->object->fileEnd();
        static::assertSame('</browsercaps>' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEndIfSilent(): void
    {
        $this->object->setSilent(true);

        $this->object->fileEnd();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeaderIfSilent(): void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(true);

        $this->object->renderHeader($header);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeaderIfNotSilent(): void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(false);

        $this->object->renderHeader($header);
        static::assertSame(
            '<comments>' . PHP_EOL . '<comment><![CDATA[TestData to be renderd into the Header]]></comment>' . PHP_EOL
            . '</comments>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfSilent(): void
    {
        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',
        ];

        $this->object->setSilent(true);

        $this->object->renderVersion($version);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfNotSilent(): void
    {
        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',
        ];

        $this->object->setSilent(false);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();
        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyName')
            ->willReturn('test');

        assert($mockFormatter instanceof XmlFormatter);
        $this->object->setFormatter($mockFormatter);

        $this->object->renderVersion($version);
        static::assertSame(
            '<gjk_browscap_version>' . PHP_EOL . '<item name="Version" value="test"/>' . PHP_EOL
            . '<item name="Released" value="test"/>' . PHP_EOL . '</gjk_browscap_version>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfNotSilentButWithoutVersion(): void
    {
        $version = [];

        $this->object->setSilent(false);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyName')
            ->willReturn('test');

        assert($mockFormatter instanceof XmlFormatter);
        $this->object->setFormatter($mockFormatter);

        $this->object->renderVersion($version);
        static::assertSame(
            '<gjk_browscap_version>' . PHP_EOL . '<item name="Version" value="test"/>' . PHP_EOL
            . '<item name="Released" value="test"/>' . PHP_EOL . '</gjk_browscap_version>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the header for all division
     */
    public function testRenderAllDivisionsHeader(): void
    {
        $collection = $this->createMock(DataCollection::class);

        assert($collection instanceof DataCollection);
        $this->object->renderAllDivisionsHeader($collection);
        static::assertSame('<browsercapitems>' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     */
    public function testRenderDivisionHeader(): void
    {
        $this->object->setSilent(true);

        $this->object->renderDivisionHeader('test');
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeaderIfNotSilent(): void
    {
        $this->object->setSilent(false);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(static::once())
            ->method('formatPropertyName')
            ->willReturn('test');

        assert($mockFormatter instanceof XmlFormatter);
        $this->object->setFormatter($mockFormatter);

        $this->object->renderSectionHeader('test');
        static::assertSame('<browscapitem name="test">' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeaderIfSilent(): void
    {
        $this->object->setSilent(true);

        $this->object->renderSectionHeader('test');
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     */
    public function testRenderSectionBodyIfNotSilent(): void
    {
        $this->object->setSilent(false);

        $section = [
            'Test' => 1,
            'isTest' => true,
            'abc' => 'bcd',
        ];

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Test' => 'abc',
                'abc' => true,
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();
        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName', 'formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyName')
            ->willReturnArgument(0);
        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyValue')
            ->willReturnArgument(0);

        assert($mockFormatter instanceof XmlFormatter);
        $this->object->setFormatter($mockFormatter);

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $map = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
            ['abc', $this->object, true],
        ];

        $mockFilter
            ->expects(static::exactly(2))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        assert($mockFilter instanceof FullFilter);
        $this->object->setFilter($mockFilter);

        assert($collection instanceof DataCollection);
        $this->object->renderSectionBody($section, $collection);
        static::assertSame(
            '<item name="Test" value="1"/>' . PHP_EOL . '<item name="abc" value="bcd"/>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the body of one section
     */
    public function testRenderSectionBodyIfNotSilentWithParents(): void
    {
        $this->object->setSilent(false);

        $section = [
            'Parent' => 'X1',
            'Comment' => '1',
            'Win16' => true,
            'Platform' => 'bcd',
        ];

        $sections = [
            'X1' => [
                'Comment' => '12',
                'Win16' => false,
                'Platform' => 'bcd',
            ],
            'X2' => $section,
        ];

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Comment' => 1,
                'Win16' => true,
                'Platform' => 'bcd',
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        $propertyHolder = $this->createMock(PropertyHolder::class);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(3))
            ->method('formatPropertyName')
            ->willReturnArgument(0);

        assert($mockFormatter instanceof XmlFormatter);
        $this->object->setFormatter($mockFormatter);

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMockBuilder(StandardFilter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $mockFilter
            ->expects(static::exactly(4))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        assert($mockFilter instanceof StandardFilter);
        $this->object->setFilter($mockFilter);

        assert($collection instanceof DataCollection);
        $this->object->renderSectionBody($section, $collection, $sections);
        static::assertSame(
            '<item name="Parent" value="X1"/>' . PHP_EOL . '<item name="Comment" value="1"/>' . PHP_EOL
            . '<item name="Platform" value="bcd"/>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the body of one section
     */
    public function testRenderSectionBodyIfNotSilentWithDefaultPropertiesAsParent(): void
    {
        $this->object->setSilent(false);

        $section = [
            'Parent' => 'DefaultProperties',
            'Comment' => '1',
            'Win16' => true,
            'Platform' => 'bcd',
        ];

        $sections = ['X2' => $section];

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Comment' => '12',
                'Win16' => true,
                'Platform' => 'bcd',
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        $propertyHolder = $this->createMock(PropertyHolder::class);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(3))
            ->method('formatPropertyName')
            ->willReturnArgument(0);

        assert($mockFormatter instanceof XmlFormatter);
        $this->object->setFormatter($mockFormatter);

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMockBuilder(StandardFilter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $mockFilter
            ->expects(static::exactly(4))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        assert($mockFilter instanceof StandardFilter);
        $this->object->setFilter($mockFilter);

        assert($collection instanceof DataCollection);
        $this->object->renderSectionBody($section, $collection, $sections);
        static::assertSame(
            '<item name="Parent" value="DefaultProperties"/>' . PHP_EOL . '<item name="Comment" value="1"/>' . PHP_EOL
            . '<item name="Platform" value="bcd"/>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the body of one section
     */
    public function testRenderSectionBodyIfSilent(): void
    {
        $this->object->setSilent(true);

        $section = [
            'Test' => 1,
            'isTest' => true,
            'abc' => 'bcd',
        ];

        $collection = $this->createMock(DataCollection::class);

        assert($collection instanceof DataCollection);
        $this->object->renderSectionBody($section, $collection);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooterIfNotSilent(): void
    {
        $this->object->setSilent(false);

        $this->object->renderSectionFooter();
        static::assertSame('</browscapitem>' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooterIfSilent(): void
    {
        $this->object->setSilent(true);

        $this->object->renderSectionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one division
     */
    public function testRenderDivisionFooter(): void
    {
        $this->object->renderDivisionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer after all divisions
     */
    public function testRenderAllDivisionsFooter(): void
    {
        $this->object->renderAllDivisionsFooter();
        static::assertSame('</browsercapitems>' . PHP_EOL, file_get_contents($this->file));
    }
}
