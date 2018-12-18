<?php declare(strict_types=1);

namespace hollodotme\Xml2Csv\Tests\Unit;

use hollodotme\Xml2Csv\Converter;
use PHPUnit\Framework\TestCase;
use function sys_get_temp_dir;
use function tempnam;

final class ConverterTest extends TestCase
{

	/**
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \RuntimeException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanConvertXmlFileToCsvFile() : void
	{
		$converter = new Converter( 'product' );

		$resultFile = tempnam( sys_get_temp_dir(), 'Xml2Csv-' );
		$converter->convertXmlFileToCsvFile(
			__DIR__ . '/_files/source.xml',
			$resultFile
		);

		$this->assertFileEquals( __DIR__ . '/_files/target.csv', $resultFile );
	}
}
