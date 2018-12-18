<?php declare(strict_types=1);

namespace hollodotme\Xml2Csv;

use DOMDocument;
use Generator;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use SimpleXMLElement;
use SplFileObject;
use XMLReader;
use function file_exists;
use function mkdir;
use function realpath;
use function touch;

final class Converter
{
	/** @var string */
	private $expandNodeName;

	/** @var XMLReader */
	private $xmlReader;

	/** @var SplFileObject */
	private $csvWriter;

	/**
	 * @param string $expandNodeName
	 *
	 * @throws RuntimeException
	 * @throws LogicException
	 */
	public function __construct( string $expandNodeName )
	{
		$this->expandNodeName = $expandNodeName;
		$this->xmlReader      = new XMLReader();
		$this->csvWriter      = new SplFileObject( 'php://output', 'wb' );
	}

	/**
	 * @param string $xmlFile
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function convertXmlFileToCsvOutput( string $xmlFile ) : void
	{
		$this->guardXmlFileExists( $xmlFile );

		$this->openFile( $xmlFile );

		$this->gotoSection( $this->expandNodeName );

		foreach ( $this->getExpandedNodes() as $index => $node )
		{
			printf( "--> FOUND %s %d\n", $this->expandNodeName, $index + 1 );


		}

		$this->closeFile();
	}

	/**
	 * @param string $xmlFile
	 * @param string $csvFile
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function convertXmlFileToCsvFile( string $xmlFile, string $csvFile ) : void
	{
		$this->guardXmlFileExists( $xmlFile );
		$this->createCsvFile( $csvFile );

		$this->openFile( $xmlFile );
		$this->closeFile();
	}

	/**
	 * @param string $xmlFile
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardXmlFileExists( string $xmlFile ) : void
	{
		if ( !file_exists( $xmlFile ) )
		{
			throw new InvalidArgumentException( 'XML file does not exist.' );
		}
	}

	/**
	 * @param string $csvFile
	 *
	 * @throws RuntimeException
	 */
	private function createCsvFile( string $csvFile ) : void
	{
		$path = dirname( $csvFile );

		if ( !@mkdir( $path, 0755, true ) && !is_dir( $path ) )
		{
			throw new RuntimeException(
				sprintf( 'Directory "%s" was not created', $path )
			);
		}

		if ( !touch( $csvFile ) )
		{
			throw new RuntimeException( 'Could not create CSV file.' );
		}
	}

	/**
	 * @param string $filePath
	 *
	 * @throws RuntimeException
	 */
	private function openFile( string $filePath ) : void
	{
		if ( !$this->xmlReader->open( 'file://' . realpath( $filePath ) ) )
		{
			throw new RuntimeException( 'Could not open XML file for reading.' );
		}
	}

	/**
	 * @param string $section
	 *
	 * @throws RuntimeException
	 */
	private function gotoSection( string $section ) : void
	{
		while ( $this->xmlReader->read() )
		{
			if ( $this->xmlReader->nodeType === XMLReader::ELEMENT && $this->xmlReader->name === $section )
			{
				break;
			}
		}

		if ( $this->xmlReader->name !== $section )
		{
			throw new RuntimeException( 'Section not found in XML file.' );
		}
	}

	/**
	 * @return Generator|SimpleXMLElement[]
	 */
	private function getExpandedNodes() : Generator
	{
		do
		{
			$node         = (new DOMDocument())->importNode( $this->xmlReader->expand(), true );
			$expandedNode = simplexml_import_dom( $node );

			yield $expandedNode;
		}
		while ( $this->xmlReader->next( $this->expandNodeName ) );
	}

	private function closeFile() : void
	{
		$this->xmlReader->close();
	}
}