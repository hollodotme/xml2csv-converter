<?php declare(strict_types=1);

namespace hollodotme\Xml2Csv\Bin;

use hollodotme\Xml2Csv\Converter;
use Throwable;

require __DIR__ . '/../vendor/autoload.php';

$xmlFile = $argv[1];
$csvFile = $argv[2] ?? '';

try
{
	$converter = new Converter( 'Product' );

	if ( '' !== $csvFile )
	{
		echo "√ Converting {$xmlFile} to {$csvFile}.\n";

		$converter->convertXmlFileToCsvFile( $xmlFile, $csvFile );

		echo "√ Done.\n";

		return;
	}

	echo "√ Converting {$xmlFile} to CSV output.\n";

	$converter->convertXmlFileToCsvOutput( $xmlFile );

	echo "√ Done.\n";
}
catch ( Throwable $e )
{
	echo $e->getMessage(), "\n";
}

