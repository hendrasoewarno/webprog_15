<?php
# atur zona waktu sender server ke Jakarta (WIB / GMT+7)
date_default_timezone_set("Asia/Jakarta");

# ambil waktu server pengirim (sender)
$curr_unix_time = time(); # ambil waktu saat ini dalam bentuk Unix Timestamp UTC (tidak terpengaruh zona waktu)

$url = "http://localhost/hotel/api/v1/booking/add";

$api_key = 'key-live:h3oiu8b54que';
$secret_key = 'secret-live:dTvCnLh58A';

# atur request header, kirimkan API Key, Unix Timestamp dan Token hasil hash (secret key tidak dikirimkan dalam bentuk plain text agar lebih secure)
$headers = [
	'Content-Type:application/json',
	'Accept:application/json',
	'API-Key:'.$api_key,
	'X-Request-Time:'.$curr_unix_time,
	'API-Token:'.hash('sha256', $api_key.$secret_key.$curr_unix_time) # hash token agar tidak bisa terbaca (one way), token ini digunakan untuk keperluan validasi
];

$payload = json_encode(array("JenisId"=>"KTP", "NomorId"=>"1234567890123456","Nama"=>"Hendra Soewarno", "JenisKamar"=>"DELUXE", "DariTanggal"=>"2024-01-01", "SampaiTanggal"=>"2024-01-01", "NamaTamu"=>"Hendra Soewarno", "ContactNo"=>"081xxxxxxxxxx"));

# Inisiasi CURL request
$ch = curl_init();

# atur CURL Options
curl_setopt_array($ch, array(
	CURLOPT_URL => $url, # URL endpoint
	CURLOPT_HTTPHEADER => $headers, # HTTP Headers
	CURLOPT_RETURNTRANSFER => 1, # return hasil curl_exec ke variabel, tidak langsung dicetak
	CURLOPT_FOLLOWLOCATION => 1, # atur flag followlocation untuk mengikuti bila ada url redirect di server penerima tetap difollow
	CURLOPT_CONNECTTIMEOUT => 60, # set connection timeout ke 60 detik, untuk mencegah request gantung
	CURLOPT_POST => 1, # set flag request method POST
	CURLOPT_POSTFIELDS => $payload # attached post data dalam bentuk JSON String
));

curl_setopt($ch, CURLOPT_PROXY, '');

# eksekusi CURL request dan tampung hasil responsenya ke variabel $resp
$resp = curl_exec($ch);

# validasi curl request tidak error
if (curl_errno($ch) == false) {
	# jika curl berhasil
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($http_code == 200) {
		# http code === 200 berarti request sukses (harap pastikan server penerima mengirimkan http_code 200 jika berhasil)
		echo $resp;
	} else {
		# selain itu request gagal (contoh: error 404 page not found)
		echo 'Error HTTP Code : '.$http_code."\n";
		echo $resp;
	}
} else {
	# jika curl error (contoh: request timeout)
	# Daftar kode error : https://curl.haxx.se/libcurl/c/libcurl-errors.html
	echo "Error while sending request, reason:".curl_error($ch);
}

# tutup CURL
curl_close($ch);
?>