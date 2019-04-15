<?php
/*
 . trmBase.getString(Signature, 4))
echo "Min record           : " . trmBase.getShort(MinRecord));
echo "Max record           : " . trmBase.getShort(MaxRecord));
echo "Space code           : " . trmBase.getByte(SpaceCode));
echo "Number code          : " . trmBase.getByte(NumberCode));
echo "Key compression      : " . trmBase.getByte(KeyCompression));
echo "Compression          : " . trmBase.getByte(Compression));
echo "Key number code      : " . trmBase.getByte(KeyNumberCode));
echo "Block size           : " . trmBase.getShort(BlockSize));
echo "Block increment      : " . trmBase.getShort(BlockIncrement));
echo "Block contains       : " . trmBase.getByte(BlockContains));
echo "Index blocks         : " . trmBase.getShort(IndexBlocks));
echo "Number of Records    : " . trmBase.getShort(NoRecords) & 0xFFFF));
echo "Integrity flag       : " . trmBase.getByte(IntegrityFlag));
echo "AltKeyLength         : " . trmBase.getByte(AltKeyLength));
echo "Primary key offset   : " . trmBase.getShort(PriKeyOffSet));
echo "Allocation increment : " . trmBase.getShort(AllocationInc));
echo "AltKeyOffset         : " . trmBase.getByte(AltKeyOffset));
echo "Primary key length   : " . trmBase.getShort(PriKeyLength));
echo "Empty blocks         : " . trmBase.getShort(EmptyBlocks));
Signature		: RRRR
Min record		: 268
Max record		: 268
Space code		: 32
Number code		: 48
Key compression		: 2
Compression		: 2
Key number code		: 32
Block size		: 1024
Block increment		: 1024
Block contains		: 0
Index blocks		: 1784
Number of Records	: 19992
Integrity flag		: 0
AltKeyLength		: 0
Primary key offset	: 0
Allocation increment	: 0
AltKeyOffset		: 0
Primary key length	: 19
Empty blocks		: 3
		
*/
$headers = Array(
    "Signature"         => Array( "OffSet" => 6 , "Long" => 4, "Format" => "A*" ),
    "MinRecord"         => Array( "OffSet" => 16, "Long" => 2, "Format" => "n*" ),
    "MaxRecord"         => Array( "OffSet" => 18, "Long" => 2, "Format" => "n*" ),
    "SpaceCode"         => Array( "OffSet" => 21, "Long" => 1, "Format" => "C*" ),
    "NumberCode"        => Array( "OffSet" => 22, "Long" => 1, "Format" => "C*" ),
    "KeyCompression"    => Array( "OffSet" => 23, "Long" => 1, "Format" => "C" ),
    "Compression"       => Array( "OffSet" => 23, "Long" => 1, "Format" => "C" ),
    "KeyNumberCode"     => Array( "OffSet" => 24, "Long" => 1, "Format" => "C" ),
    "BlockSize"         => Array( "OffSet" => 26, "Long" => 2, "Format" => "n*" ),
    "BlockIncrement"    => Array( "OffSet" => 28, "Long" => 2, "Format" => "n*" ),
    "BlockContains"     => Array( "OffSet" => 30, "Long" => 1, "Format" => "C" ),
    "IndexBlocks"       => Array( "OffSet" => 44, "Long" => 2, "Format" => "n*" ),
    "NoRecords"         => Array( "OffSet" => 52, "Long" => 2, "Format" => "n*" ),
    "IntegrityFlag"     => Array( "OffSet" => 54, "Long" => 1, "Format" => "C" ),
    "AltKeyLength"      => Array( "OffSet" => 256, "Long" => 1, "Format" => "C" ),
    "PriKeyOffSet"      => Array( "OffSet" => 256, "Long" => 2, "Format" => "n*" ),
    "AllocationInc"     => Array( "OffSet" => 256, "Long" => 2, "Format" => "n*" ),
    "AltKeyOffset"      => Array( "OffSet" => 258, "Long" => 1, "Format" => "C" ),
    "PriKeyLength"      => Array( "OffSet" => 258, "Long" => 2, "Format" => "n*" ),
    "EmptyBlocks"       => Array( "OffSet" => 260, "Long" => 2, "Format" => "n*" )
);


$inFile = fopen('data\\STOK.dat','rb');
fseek($inFile, 52, SEEK_SET);
$data = fread($inFile, 1);
$header_format = 'C2NoRecords/';
$headers = unpack('C1', $data);
echo $headers[1];
var_dump($headers);

/*
foreach($headers as $key => $val) {
    fseek($inFile, $val["OffSet"], SEEK_SET);
    $value = unpack($val["Format"], fread($inFile,);
    if($key=='NoRecords')
        echo "$key $value[1]: " . ($value[1] & 0xFFFF) . "\n";
    else
        echo "$key \t : " . $value[1] . " \t\t [" . bin2hex( fgets($inFile, $val["Long"] + 1)) . "]\n";
}
*/
fclose($inFile);

 
?>