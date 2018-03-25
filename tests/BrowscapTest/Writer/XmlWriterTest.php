<?php
declare(strict_types = 1);
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
use Monolog\Logger;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class XmlWriterTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    /**
     * @var XmlWriter
     */
    private $object;

    /**
     * @var string
     */
    private $file;

    /**
     * @throws \ReflectionException
     */
    public function setUp() : void
    {
        vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.xml';

        $logger = $this->createMock(Logger::class);

        $this->object = new XmlWriter($this->file, $logger);
    }

    public function teardown() : void
    {
        $this->object->close();

        unlink($this->file);
    }

    /**
     * tests getting the writer type
     */
    public function testGetType() : void
    {
        self::assertSame(WriterInterface::TYPE_XML, $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     *
     * @throws \ReflectionException
     */
    public function testSetGetFormatter() : void
    {
        $mockFormatter = $this->createMock(XmlFormatter::class);

        $this->object->setFormatter($mockFormatter);
        self::assertSame($mockFormatter, $this->object->getFormatter());
    }

    /**
     * tests setting and getting a filter
     *
     * @throws \ReflectionException
     */
    public function testSetGetFilter() : void
    {
        $mockFilter = $this->createMock(FullFilter::class);

        $this->object->setFilter($mockFilter);
        self::assertSame($mockFilter, $this->object->getFilter());
    }

    /**
     * tests setting a file into silent mode
     */
    public function testSetGetSilent() : void
    {
        $silent = true;

        $this->object->setSilent($silent);
        self::assertSame($silent, $this->object->isSilent());
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStartIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $this->object->fileStart();
        self::assertSame(
            '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<browsercaps>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the start of the file
     */
    public function testFileStartIfSilent() : void
    {
        $this->object->setSilent(true);

        $this->object->fileStart();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEndIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $this->object->fileEnd();
        self::assertSame('</browsercaps>' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     */
    public function testFileEndIfSilent() : void
    {
        $this->object->setSilent(true);

        $this->object->fileEnd();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeaderIfSilent() : void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(true);

        $this->object->renderHeader($header);
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     */
    public function testRenderHeaderIfNotSilent() : void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->setSilent(false);

        $this->object->renderHeader($header);
        self::assertSame(
            '<comments>' . PHP_EOL . '<comment><![CDATA[TestData to be renderd into the Header]]></comment>' . PHP_EOL
            . '</comments>' . PHP_EOL,
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
        self::assertSame('', file_get_contents($this->file));
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

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnValue('test'));

        $this->object->setFormatter($mockFormatter);

        $this->object->renderVersion($version);
        self::assertSame(
            '<gjk_browscap_version>' . PHP_EOL . '<item name="Version" value="test"/>' . PHP_EOL
            . '<item name="Released" value="test"/>' . PHP_EOL . '</gjk_browscap_version>' . PHP_EOL,
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

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnValue('test'));

        $this->object->setFormatter($mockFormatter);

        $this->object->renderVersion($version);
        self::assertSame(
            '<gjk_browscap_version>' . PHP_EOL . '<item name="Version" value="test"/>' . PHP_EOL
            . '<item name="Released" value="test"/>' . PHP_EOL . '</gjk_browscap_version>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the header for all division
     *
     * @throws \ReflectionException
     */
    public function testRenderAllDivisionsHeader() : void
    {
        $collection = $this->createMock(DataCollection::class);

        $this->object->renderAllDivisionsHeader($collection);
        self::assertSame('<browsercapitems>' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     */
    public function testRenderDivisionHeader() : void
    {
        $this->object->setSilent(true);

        $this->object->renderDivisionHeader('test');
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeaderIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(self::once())
            ->method('formatPropertyName')
            ->will(self::returnValue('test'));

        $this->object->setFormatter($mockFormatter);

        $this->object->renderSectionHeader('test');
        self::assertSame('<browscapitem name="test">' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     */
    public function testRenderSectionHeaderIfSilent() : void
    {
        $this->object->setSilent(true);

        $this->object->renderSectionHeader('test');
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     */
    public function testRenderSectionBodyIfNotSilent() : void
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
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Test' => 'abc',
                'abc' => true,
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();
        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->disableOriginalConstructor()
            ->setMethods(['formatPropertyName', 'formatPropertyValue'])
            ->getMock();

        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));
        $mockFormatter
            ->expects(self::exactly(2))
            ->method('formatPropertyValue')
            ->will(self::returnArgument(0));

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
            ->expects(self::exactly(2))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        $this->object->setFilter($mockFilter);

        $this->object->renderSectionBody($section, $collection);
        self::assertSame(
            '<item name="Test" value="1"/>' . PHP_EOL . '<item name="abc" value="bcd"/>' . PHP_EOL,
            file_get_contents($this->file)
        );
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

        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Comment' => 1,
                'Win16' => true,
                'Platform' => 'bcd',
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $propertyHolder = $this->createMock(PropertyHolder::class);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(self::exactly(3))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));

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
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        $this->object->setFilter($mockFilter);

        $this->object->renderSectionBody($section, $collection, $sections);
        self::assertSame(
            '<item name="Parent" value="X1"/>' . PHP_EOL . '<item name="Comment" value="1"/>' . PHP_EOL
            . '<item name="Platform" value="bcd"/>' . PHP_EOL,
            file_get_contents($this->file)
        );
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
            ->expects(self::once())
            ->method('getProperties')
            ->will(self::returnValue([
                'Comment' => '12',
                'Win16' => true,
                'Platform' => 'bcd',
            ]));

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(self::once())
            ->method('getUserAgents')
            ->will(self::returnValue([0 => $useragent]));

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(self::once())
            ->method('getDefaultProperties')
            ->will(self::returnValue($division));

        $propertyHolder = $this->createMock(PropertyHolder::class);

        $mockFormatter = $this->getMockBuilder(XmlFormatter::class)
            ->setConstructorArgs([$propertyHolder])
            ->setMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(self::exactly(3))
            ->method('formatPropertyName')
            ->will(self::returnArgument(0));

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
            ->expects(self::exactly(4))
            ->method('isOutputProperty')
            ->will(self::returnValueMap($map));

        $this->object->setFilter($mockFilter);
        $this->object->renderSectionBody($section, $collection, $sections);
        self::assertSame(
            '<item name="Parent" value="DefaultProperties"/>' . PHP_EOL . '<item name="Comment" value="1"/>' . PHP_EOL
            . '<item name="Platform" value="bcd"/>' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the body of one section
     *
     * @throws \ReflectionException
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

        $this->object->renderSectionBody($section, $collection);
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooterIfNotSilent() : void
    {
        $this->object->setSilent(false);

        $this->object->renderSectionFooter();
        self::assertSame('</browscapitem>' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one section
     */
    public function testRenderSectionFooterIfSilent() : void
    {
        $this->object->setSilent(true);

        $this->object->renderSectionFooter();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one division
     */
    public function testRenderDivisionFooter() : void
    {
        $this->object->renderDivisionFooter();
        self::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer after all divisions
     */
    public function testRenderAllDivisionsFooter() : void
    {
        $this->object->renderAllDivisionsFooter();
        self::assertSame('</browsercapitems>' . PHP_EOL, file_get_contents($this->file));
    }
}
