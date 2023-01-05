<?php

declare(strict_types=1);

namespace BrowscapTest\Writer;

use Browscap\Data\DataCollection;
use Browscap\Data\Division;
use Browscap\Filter\FilterInterface;
use Browscap\Formatter\FormatterInterface;
use Browscap\Writer\WriterCollection;
use Browscap\Writer\WriterInterface;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use JsonException;
use PHPUnit\Framework\MockObject\MethodCannotBeConfiguredException;
use PHPUnit\Framework\MockObject\MethodNameAlreadyConfiguredException;
use PHPUnit\Framework\TestCase;

use function assert;

class WriterCollectionTest extends TestCase
{
    private WriterCollection $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new WriterCollection();
    }

    /**
     * tests setting and getting a writer
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testAddWriterAndSetSilent(): void
    {
        $division = $this->createMock(Division::class);

        $mockFilter = $this->createMock(FilterInterface::class);
        $mockFilter
            ->expects(static::once())
            ->method('isOutput')
            ->with($division)
            ->willReturn(true);

        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('getFilter')
            ->willReturn($mockFilter);
        $mockWriter
            ->expects(static::once())
            ->method('setSilent')
            ->with(false);

        $this->object->addWriter($mockWriter);

        $this->object->setSilent($division);
    }

    /**
     * tests setting a file into silent mode
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testSetSilentSection(): void
    {
        $section = [];

        $mockFilter = $this->createMock(FilterInterface::class);
        $mockFilter
            ->expects(static::once())
            ->method('isOutputSection')
            ->with($section)
            ->willReturn(true);

        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('getFilter')
            ->willReturn($mockFilter);
        $mockWriter
            ->expects(static::once())
            ->method('setSilent')
            ->with(false);

        $this->object->addWriter($mockWriter);
        $this->object->setSilentSection($section);
    }

    /**
     * tests rendering the start of the file
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testFileStart(): void
    {
        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('fileStart');

        $this->object->addWriter($mockWriter);
        $this->object->fileStart();
    }

    /**
     * tests rendering the end of the file
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testFileEnd(): void
    {
        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('fileEnd');

        $this->object->addWriter($mockWriter);
        $this->object->fileEnd();
    }

    /**
     * tests rendering the header information
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws JsonException
     */
    public function testRenderHeader(): void
    {
        $header = ['TestData to be renderd into the Header'];

        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderHeader')
            ->with($header);

        $this->object->addWriter($mockWriter);
        $this->object->renderHeader($header);
    }

    /**
     * tests rendering the version information
     *
     * @throws Exception
     * @throws JsonException
     */
    public function testRenderVersion(): void
    {
        $version       = 'test';
        $formatterType = 'test';
        $filterType    = 'Test';
        $date          = new DateTimeImmutable();

        $collection = $this->createMock(DataCollection::class);

        $mockFilter = $this->createMock(FilterInterface::class);
        $mockFilter
            ->expects(static::never())
            ->method('isOutput')
            ->willReturn(true);
        $mockFilter
            ->expects(static::once())
            ->method('getType')
            ->willReturn($filterType);

        $mockFormatter = $this->createMock(FormatterInterface::class);
        $mockFormatter
            ->expects(static::once())
            ->method('getType')
            ->willReturn($formatterType);

        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('getFilter')
            ->willReturn($mockFilter);
        $mockWriter
            ->expects(static::once())
            ->method('getFormatter')
            ->willReturn($mockFormatter);
        $mockWriter
            ->expects(static::once())
            ->method('renderVersion')
            ->with(
                [
                    'version' => $version,
                    'released' => $date->format('r'),
                    'format' => $formatterType,
                    'type' => $filterType,
                ],
            );

        $this->object->addWriter($mockWriter);

        assert($collection instanceof DataCollection);
        $this->object->renderVersion($version, $date, $collection);
        $this->object->close();
    }

    /**
     * tests rendering the header for all division
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testRenderAllDivisionsHeader(): void
    {
        $collection = $this->createMock(DataCollection::class);
        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderAllDivisionsHeader')
            ->with($collection);

        $this->object->addWriter($mockWriter);

        $this->object->renderAllDivisionsHeader($collection);
    }

    /**
     * tests rendering the header of one division
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testRenderDivisionHeader(): void
    {
        $division = 'test';
        $parent   = 'test-parent';

        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderDivisionHeader')
            ->with($division, $parent);

        $this->object->addWriter($mockWriter);
        $this->object->renderDivisionHeader($division, $parent);
    }

    /**
     * tests rendering the header of one section
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testRenderSectionHeader(): void
    {
        $section = 'test';

        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderSectionHeader')
            ->with($section);

        $this->object->addWriter($mockWriter);
        $this->object->renderSectionHeader($section);
    }

    /**
     * tests rendering the body of one section
     *
     * @throws InvalidArgumentException
     */
    public function testRenderSectionBody(): void
    {
        $section = [
            'Comment' => 1,
            'Win16' => true,
            'Platform' => 'bcd',
        ];

        $collection = $this->createMock(DataCollection::class);
        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderSectionBody')
            ->with($section, $collection);

        $this->object->addWriter($mockWriter);

        $this->object->renderSectionBody($section, $collection);
    }

    /**
     * tests rendering the footer of one section
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testRenderSectionFooter(): void
    {
        $sectionName = 'test';

        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderSectionFooter')
            ->with($sectionName);

        $this->object->addWriter($mockWriter);
        $this->object->renderSectionFooter($sectionName);
    }

    /**
     * tests rendering the footer of one division
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     */
    public function testRenderDivisionFooter(): void
    {
        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderDivisionFooter');

        $this->object->addWriter($mockWriter);
        $this->object->renderDivisionFooter();
    }

    /**
     * tests rendering the footer after all divisions
     *
     * @throws MethodNameAlreadyConfiguredException
     * @throws MethodCannotBeConfiguredException
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    public function testRenderAllDivisionsFooter(): void
    {
        $mockWriter = $this->createMock(WriterInterface::class);
        $mockWriter
            ->expects(static::once())
            ->method('renderAllDivisionsFooter');

        $this->object->addWriter($mockWriter);
        $this->object->renderAllDivisionsFooter();
    }
}
