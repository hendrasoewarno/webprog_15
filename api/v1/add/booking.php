<?php
/*
Payload

{
	"JenisId": "KTP",
	"NomorId": "1234567890123456",
	"Nama": "Hendra Soewarno",
	"JenisKamar": "DELUXE",
	"DariTanggal": "2024-01-01",
	"SampaiTanggal": "2024-01-02",
	"NamaTamu": "Hendra Soewarno",
	"ContactNo" : "081xxxxxxxxxx"
}

Response Berhasil:

{
	"Status": 1,
	"Message": "Ok",
	"Data": {
		"BookingId":"12345"
	}
}

Response Gagal:

{
	"Status": 0,
	"Message": "Invalid Authentication Key",
	"Data": null
}
*/
include_once __DIR__ . "/../library.php";

function booking($payload) {
	$con=openConnection();	
	$sql=<<<EOD
INSERT INTO booking (JenisId, NomorId, Nama, JenisKamar, DariTanggal, SampaiTanggal, NamaTamu, ContactNo) VALUES (:JenisId, :NomorId, :Nama, :JenisKamar, :DariTanggal, :SampaiTanggal, :NamaTamu, :ContactNo);
EOD;
	$result=createRow($con, $sql, $payload);
	if ($result < 1)
		throw new Exception("1:Update Booking Failed!");
	else
		$bookingId = $con->lastInsertId();

	return array("BookingId"=>$result);
}

//unit test
//$result=booking(array("JenisId"=>"KTP", "NomorId"=>"1234567890123456","Nama"=>"Hendra Soewarno", "JenisKamar"=>"DELUXE", "DariTanggal"=>"2024-01-01", "SampaiTanggal"=>"2024-01-01", "NamaTamu"=>"Hendra Soewarno", "ContactNo"=>"081xxxxxxxxxx"));
//print_r($result);
?>