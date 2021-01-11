<?php
declare(strict_types = 1);
namespace BrowscapTest\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\Helper\TrimProperty;
use Browscap\Data\PropertyHolder;
use Browscap\Data\UserAgent;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\PhpFormatter;
use Browscap\Writer\IniWriter;
use Browscap\Writer\WriterInterface;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class IniWriterTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    /**
     * @var IniWriter
     */
    private $object;

    /**
     * @var string
     */
    private $file;

    protected function setUp() : void
    {
        vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . \DIRECTORY_SEPARATOR . 'test.ini';

        $logger = $this->createMock(LoggerInterface::class);

        /* @var LoggerInterface $logger */
        $this->object = new IniWriter($this->file, $logger);
    }

    protected function teardown() : void
    {
        $this->object->close();

        unlink($this->file);
    }

    /**
     * tests getting the writer type
     */
    public function testGetType() : void
    {
        static::assertSame(WriterInterface::TYPE_INI, $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     */
    public function testSetGetFormatter() : void
    {
        $mockFormatter = $this->createMock(PhpFormatter::class);

        /* @var PhpFormatter $mockFormatter */
        $this->object->setFormatter($mockFormatter);
        static::assertSame($mockFormatter, $this->object->getFormatter());
    }

    /**
     * tests setting and getting a filter
     */
    public function testSetGetFilter() : void
    {
        $mockFilter = $this->createMock(FullFilter::class);

        /* @var FullFilter $mockFilter */
        $this->object->setFilter($mockFilter);
        static::assertSame($mockFilter, $this->object->getFilter());
    }

    /**
     * tests setting a file into silent mode
     */
    public function testSetGetSilent() : void
    {
        $this->object->setSilent(true);
        static::assertTrue($this->object->isSilent());
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStart() : void
    {
        $this->object->fileStart();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEnd() : void
    {
        $this->object->fileEnd();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeaderIfSilent() : void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(true);

        $this->object->renderHeader($header);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeaderIfNotSilent() : void
    {
        $header = ['TestData to be rendered into the Header', 'more data to be rendered', 'much more data'];

        $this->object->setSilent(false);

        $this->object->renderHeader($header);
        static::assertSame(
            ';;; TestData to be rendered into the Header' . PHP_EOL
            . ';;; more data to be rendered' . PHP_EOL
            . ';;; much more data' . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfSilent() : void
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
    public function testRenderVersionIfNotSilent() : void
    {
        $version = [
            'version' => 'test',
            'released' => date('Y-m-d'),
            'format' => 'TEST',
            'type' => 'full',
        ];

        $this->object->setSilent(false);

        $this->object->renderVersion($version);
        static::assertSame(
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; Browscap Version' . PHP_EOL . PHP_EOL . '[GJK_Browscap_Version]'
            . PHP_EOL . 'Version=test' . PHP_EOL . 'Released=' . date('Y-m-d') . PHP_EOL . 'Format=TEST' . PHP_EOL
            . 'Type=full' . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     */
    public function testRenderVersionIfNotSilentButWithoutVersion() : void
    {
        $version = [];

        $this->object->setSilent(false);

        $this->object->renderVersion($version);
        static::assertSame(
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; Browscap Version' . PHP_EOL . PHP_EOL . '[GJK_Browscap_Version]'
            . PHP_EOL . 'Version=0' . PHP_EOL . 'Released=' . PHP_EOL . 'Format=' . PHP_EOL . 'Type='
            . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the header for all division
     */
    public function testRenderAllDivisionsHeader() : void
    {
        $collection = $this->createMock(DataCollection::class);

        /* @var DataCollection $collection */
        $this->object->renderAllDivisionsHeader($collection);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     */
    public function testRenderDivisionHeaderIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $this->object->renderDivisionHeader('test');
        static::assertSame(
            ';;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;; test' . PHP_EOL . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the header of one division
     */
    public function testRenderDivisionHeaderIfSilent() : void
    {
        $this->object->setSilent(true);

        $this->object->renderDivisionHeader('test');
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeaderIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $this->object->renderSectionHeader('test');
        static::assertSame('[test]' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeaderIfSilent() : void
    {
        $this->object->setSilent(true);

        $this->object->renderSectionHeader('test');
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @throws \ReflectionException
     */
    public function testRenderSectionBodyIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $section = [
            'Comment' => '1',
            'Win16' => true,
            'Platform' => 'bcd',
        ];

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Comment' => '1',
                'Win16' => true,
            ]);

        $mockExpander = $this->getMockBuilder(TrimProperty::class)
            ->disableOriginalConstructor()
            ->setMethods(['trimProperty'])
            ->getMock();

        $mockExpander
            ->expects(static::once())
            ->method('trimProperty')
            ->willReturnArgument(0);

        $property = new \ReflectionProperty($this->object, 'trimProperty');
        $property->setAccessible(true);
        $property->setValue($this->object, $mockExpander);

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

        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPropertyType'])
            ->getMock();
        $propertyHolder
            ->expects(static::once())
            ->method('getPropertyType')
            ->willReturn(PropertyHolder::TYPE_STRING);

        $mockFormatter = $this->getMockBuilder(PhpFormatter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['formatPropertyName'])
            ->getMock();
        $mockFormatter
            ->expects(static::once())
            ->method('formatPropertyName')
            ->willReturnArgument(0);

        /* @var PhpFormatter $mockFormatter */
        $this->object->setFormatter($mockFormatter);

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
        ];

        $mockFilter
            ->expects(static::exactly(2))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        /* @var FullFilter $mockFilter */
        $this->object->setFilter($mockFilter);

        /* @var DataCollection $collection */
        $this->object->renderSectionBody($section, $collection);
        static::assertSame('Comment="1"' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @throws \ReflectionException
     */
    public function testRenderSectionBodyIfNotSilentWithParents() : void
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

        $mockExpander = $this->getMockBuilder(TrimProperty::class)
            ->disableOriginalConstructor()
            ->setMethods(['trimProperty'])
            ->getMock();

        $mockExpander
            ->expects(static::exactly(2))
            ->method('trimProperty')
            ->willReturnArgument(0);

        $property = new \ReflectionProperty($this->object, 'trimProperty');
        $property->setAccessible(true);
        $property->setValue($this->object, $mockExpander);

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

        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPropertyType'])
            ->getMock();
        $propertyHolder
            ->expects(static::exactly(2))
            ->method('getPropertyType')
            ->willReturn(PropertyHolder::TYPE_STRING);

        $mockFormatter = $this->getMockBuilder(PhpFormatter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['formatPropertyName'])
            ->getMock();
        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyName')
            ->willReturnArgument(0);

        /* @var PhpFormatter $mockFormatter */
        $this->object->setFormatter($mockFormatter);

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $mockFilter
            ->expects(static::exactly(4))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        /* @var FullFilter $mockFilter */
        $this->object->setFilter($mockFilter);

        /* @var DataCollection $collection */
        $this->object->renderSectionBody($section, $collection, $sections);
        static::assertSame('Parent="X1"' . PHP_EOL . 'Comment="1"' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @throws \ReflectionException
     */
    public function testRenderSectionBodyIfNotSilentWithDefaultPropertiesAsParent() : void
    {
        $this->object->setSilent(false);

        $section = [
            'Parent' => 'DefaultProperties',
            'Comment' => '1',
            'Win16' => true,
            'Platform' => 'bcd',
        ];

        $sections = [
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
                'Comment' => '12',
                'Win16' => true,
                'Platform' => 'bcd',
            ]);

        $mockExpander = $this->getMockBuilder(TrimProperty::class)
            ->disableOriginalConstructor()
            ->setMethods(['trimProperty'])
            ->getMock();

        $mockExpander
            ->expects(static::exactly(2))
            ->method('trimProperty')
            ->willReturnArgument(0);

        $property = new \ReflectionProperty($this->object, 'trimProperty');
        $property->setAccessible(true);
        $property->setValue($this->object, $mockExpander);

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

        $propertyHolder = $this->getMockBuilder(PropertyHolder::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPropertyType'])
            ->getMock();
        $propertyHolder
            ->expects(static::exactly(2))
            ->method('getPropertyType')
            ->willReturn(PropertyHolder::TYPE_STRING);

        $mockFormatter = $this->getMockBuilder(PhpFormatter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyName')
            ->willReturnArgument(0);

        /* @var PhpFormatter $mockFormatter */
        $this->object->setFormatter($mockFormatter);

        $map = [
            ['Comment', $this->object, true],
            ['Win16', $this->object, false],
            ['Platform', $this->object, true],
            ['Parent', $this->object, true],
        ];

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['isOutputProperty'])
            ->getMock();

        $mockFilter
            ->expects(static::exactly(4))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        /* @var FullFilter $mockFilter */
        $this->object->setFilter($mockFilter);

        /* @var DataCollection $collection */
        $this->object->renderSectionBody($section, $collection, $sections);
        static::assertSame(
            'Parent="DefaultProperties"' . PHP_EOL . 'Comment="1"' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the body of one section
     */
    public function testRenderSectionBodyIfSilent() : void
    {
        $this->object->setSilent(true);

        $section = [
            'Test' => 1,
            'isTest' => true,
            'abc' => 'bcd',
        ];

        $collection = $this->createMock(DataCollection::class);

        /* @var DataCollection $collection */
        $this->object->renderSectionBody($section, $collection);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooterIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $this->object->renderSectionFooter();
        static::assertSame(PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooterIfSilent() : void
    {
        $this->object->setSilent(true);

        $this->object->renderSectionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one division
     */
    public function testRenderDivisionFooter() : void
    {
        $this->object->renderDivisionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer after all divisions
     */
    public function testRenderAllDivisionsFooter() : void
    {
        $this->object->renderAllDivisionsFooter();
        static::assertSame('', file_get_contents($this->file));
    }
}
