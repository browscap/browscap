<?php

declare(strict_types=1);

namespace BrowscapTest\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Data\UserAgent;
use Browscap\Filter\FullFilter;
use Browscap\Formatter\CsvFormatter;
use Browscap\Writer\CsvWriter;
use Browscap\Writer\WriterInterface;
use Exception;
use InvalidArgumentException;
use JsonException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function assert;
use function date;
use function file_get_contents;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class CsvWriterTest extends TestCase
{
    private const STORAGE_DIR = 'storage';

    private CsvWriter $object;

    private string $file;

    /**
     * @throws InvalidArgumentException
     */
    protected function setUp(): void
    {
        vfsStream::setup(self::STORAGE_DIR);
        $this->file = vfsStream::url(self::STORAGE_DIR) . DIRECTORY_SEPARATOR . 'test.csv';

        $logger = $this->createMock(LoggerInterface::class);

        assert($logger instanceof LoggerInterface);
        $this->object = new CsvWriter($this->file, $logger);
    }

    /**
     * @throws void
     */
    protected function teardown(): void
    {
        $this->object->close();

        unlink($this->file);
    }

    /**
     * tests getting the writer type
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testGetType(): void
    {
        static::assertSame(WriterInterface::TYPE_CSV, $this->object->getType());
    }

    /**
     * tests setting and getting a formatter
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testSetGetFormatter(): void
    {
        $mockFormatter = $this->createMock(CsvFormatter::class);

        assert($mockFormatter instanceof CsvFormatter);
        $this->object->setFormatter($mockFormatter);
        static::assertSame($mockFormatter, $this->object->getFormatter());
    }

    /**
     * tests setting and getting a filter
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
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
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testSetGetSilent(): void
    {
        $this->object->setSilent(true);
        static::assertTrue($this->object->isSilent());
    }

    /**
     * tests rendering the start of the file
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testFileStart(): void
    {
        $this->object->fileStart();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the end of the file
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testFileEnd(): void
    {
        $this->object->fileEnd();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header information
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRenderHeader(): void
    {
        $header = ['TestData to be renderd into the Header'];

        $this->object->renderHeader($header);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the version information
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
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
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
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

        $this->object->renderVersion($version);
        static::assertSame(
            '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"test","' . date('Y-m-d') . '"' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the version information
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRenderVersionIfNotSilentButWithoutVersion(): void
    {
        $version = [];

        $this->object->setSilent(false);

        $this->object->renderVersion($version);
        static::assertSame(
            '"GJK_Browscap_Version","GJK_Browscap_Version"' . PHP_EOL . '"0",""' . PHP_EOL,
            file_get_contents($this->file)
        );
    }

    /**
     * tests rendering the header for all division
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws JsonException
     */
    public function testRenderAllDivisionsHeader(): void
    {
        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(static::exactly(2))
            ->method('getProperties')
            ->willReturn([
                'Test' => 1,
                'isTest' => true,
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        $mockFormatter = $this->getMockBuilder(CsvFormatter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['formatPropertyName'])
            ->getMock();

        $mockFormatter
            ->expects(static::exactly(2))
            ->method('formatPropertyName')
            ->willReturnArgument(0);

        assert($mockFormatter instanceof CsvFormatter);
        $this->object->setFormatter($mockFormatter);

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $map = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
            ['abc', $this->object, true],
            ['PropertyName', $this->object, false],
            ['MasterParent', $this->object, false],
            ['LiteMode', $this->object, false],
            ['Parent', $this->object, true],
            ['alpha', $this->object, true],
        ];

        $mockFilter
            ->expects(static::exactly(6))
            ->method('isOutputProperty')
            ->willReturnMap($map);

        assert($mockFilter instanceof FullFilter);
        $this->object->setFilter($mockFilter);

        assert($collection instanceof DataCollection);
        $this->object->renderAllDivisionsHeader($collection);
        static::assertSame('Parent,Test' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the header for all division
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws JsonException
     */
    public function testRenderAllDivisionsHeaderWithoutProperties(): void
    {
        $useragent = $this->getMockBuilder(UserAgent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        assert($collection instanceof DataCollection);
        $this->object->renderAllDivisionsHeader($collection);
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one division
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRenderDivisionHeader(): void
    {
        $this->object->renderDivisionHeader('test');
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the header of one section
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRenderSectionHeader(): void
    {
        $this->object->renderSectionHeader('test');
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws JsonException
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
            ->onlyMethods(['getProperties'])
            ->getMock();

        $useragent
            ->expects(static::once())
            ->method('getProperties')
            ->willReturn([
                'Test' => 'abc',
                'abc' => true,
                'alpha' => true,
            ]);

        $division = $this->getMockBuilder(Division::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUserAgents'])
            ->getMock();

        $division
            ->expects(static::once())
            ->method('getUserAgents')
            ->willReturn([0 => $useragent]);

        $collection = $this->getMockBuilder(DataCollection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getDefaultProperties'])
            ->getMock();

        $collection
            ->expects(static::once())
            ->method('getDefaultProperties')
            ->willReturn($division);

        $mockFormatter = $this->getMockBuilder(CsvFormatter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['formatPropertyName', 'formatPropertyValue'])
            ->getMock();

        $map1 = [
            ['', 'Parent', ''],
            [1, 'Test', '1'],
            ['bcd', 'abc', 'bcd'],
            ['', 'alpha', ''],
        ];

        $mockFormatter
            ->expects(static::exactly(4))
            ->method('formatPropertyValue')
            ->willReturnMap($map1);
        $mockFormatter
            ->expects(static::never())
            ->method('formatPropertyName');

        assert($mockFormatter instanceof CsvFormatter);
        $this->object->setFormatter($mockFormatter);

        $mockFilter = $this->getMockBuilder(FullFilter::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isOutputProperty'])
            ->getMock();

        $map2 = [
            ['Test', $this->object, true],
            ['isTest', $this->object, false],
            ['abc', $this->object, true],
            ['PropertyName', $this->object, false],
            ['MasterParent', $this->object, false],
            ['LiteMode', $this->object, false],
            ['Parent', $this->object, true],
            ['alpha', $this->object, true],
        ];

        $mockFilter
            ->expects(static::exactly(7))
            ->method('isOutputProperty')
            ->willReturnMap($map2);

        assert($mockFilter instanceof FullFilter);
        $this->object->setFilter($mockFilter);

        assert($collection instanceof DataCollection);
        $this->object->renderSectionBody($section, $collection);
        static::assertSame(',1,bcd,' . PHP_EOL, file_get_contents($this->file));
    }

    /**
     * tests rendering the body of one section
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws JsonException
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
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRenderSectionFooter(): void
    {
        $this->object->renderSectionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer of one division
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRenderDivisionFooter(): void
    {
        $this->object->renderDivisionFooter();
        static::assertSame('', file_get_contents($this->file));
    }

    /**
     * tests rendering the footer after all divisions
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testRenderAllDivisionsFooter(): void
    {
        $this->object->renderAllDivisionsFooter();
        static::assertSame('', file_get_contents($this->file));
    }
}
