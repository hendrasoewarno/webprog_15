<?php
/*
*/
include_once __DIR__ . "/../library.php";

/*
Payload

{
	"JenisKamar":"DELUXE",
	"DariTanggal": "2024-01-19",
	"SampaiTanggal": "2024-01-20"
}

Response:

{
	"Status": 1,
	"Message": "Ok",
	"Data": {
		"JenisKamar":"DELUXE",
		"AVAILABLE":5
	}
}

*/

function vacancy($payload) {
	$con=openConnection();
	//ini belum dikurangi jumlah kamar yang di Booking, silakan dilengkapi oleh mahasiswa
	$sql=<<<EOD
SELECT x.JenisKamar, Count(*) Available From (	
	SELECT a.JenisKamar,
		a.RoomId,
		Sum(CASE WHEN ((:DariTanggal between b.DariTanggal and b.SampaiTanggal) or
			(:SampaiTanggal between b.DariTanggal and b.SampaiTanggal) or
			(:DariTanggal <= b.DariTanggal and :SampaiTanggal >= b.SampaiTanggal)) THEN 1 ELSE 0 END) as Occupied 
	FROM room a LEFT JOIN occupied b on a.RoomId = b.RoomId WHERE a.JenisKamar=:JenisKamar Group By a.JenisKamar, a.RoomId
	HAVING
		Sum(CASE WHEN ((:DariTanggal between b.DariTanggal and b.SampaiTanggal) or
			(:SampaiTanggal between b.DariTanggal and b.SampaiTanggal) or
			(:DariTanggal <= b.DariTanggal and :SampaiTanggal >= b.SampaiTanggal)) THEN 1 ELSE 0 END)=0
) x;
EOD;
	$result=queryArrayValue($con, $sql, $payload);
	return $result;
}

//unit test
//$result=vacancy(array("JenisKamar"=>"DELUXE", "DariTanggal"=>"2024-01-01", "SampaiTanggal"=>"2024-01-01"));
//print_r($result);
?>