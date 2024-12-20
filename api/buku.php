<?php

/**
 * File buku.php adalah file yang digunakan sebagai endpoint pemanggilan api buku
 */

/**
 * di bawah ini terdapat beberapa fungsi header yang digunakan untuk mengatur kebijakan Cross-Origin Resource Sharing (CORS) pada server sebelum mengirim konten ke client (browser) sehingga memungkinkan pembatasan berbagai aspek respons HTTP, misalnya jenis konten, kode status, dan lainnya
 */

// header ini mengizinkan semua domain untuk mengakses sumber daya dari server
header("Access-Control-Allow-Origin:*");

//header ini menetapkan tipe konten respones sebagai JSON dengan pengkodean karakter UTF-8
header("Content-Type: application/json;charset=UTF-8");

// header ini menentukan beberapa metode HTTP yang diizinkan untuk digunakan saat mengakses sumber daya
header("Access-Control-Allow-Methods:POST,GET,PUT,DELETE");

// header ini mengatur penggunaan header tertentu dalam permintaan CORS]
header("Access-Control-Allow-Headers:Content-Type,Access-Control-Allow-Headers,Authorization,X-Requested-With");

require_once "../config/Database.php";
require_once "../model/Buku.php";

$database = new Database();
$db = $database->getConnection();

$buku = new Buku($db);

$request = $_SERVER['REQUEST_METHOD'];

// pemeriksaan jenis request method client
switch ($request) {
        // Request GET
    case 'GET':
        // GET all data
        if (!isset($_GET['id'])) {
            $statement = $buku->read();
            $num = $statement->rowCount();
            if ($num > 0) {
                $listBuku = array();
                $listBuku["records"] = array();
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $itemBuku = array(
                        "id" => $id,
                        "judul" => $judul,
                        "pengarang" => $pengarang,
                        "tahun_terbit" => $tahun_terbit,
                        "stok" => $stok
                    );
                    array_push($listBuku["records"], $itemBuku);
                }
                http_response_code(200);
                echo json_encode($listBuku);
            } else {
                http_response_code(404);
                echo json_encode(
                    array("message" => "Buku tidak ditemukan.")
                );
            }
        }
        // GET data dengan id null
        elseif ($_GET['id'] == null) {
            echo json_encode(
                array("message" => "Parameter id tidak boleh kosong!")
            );
        }
        // GET data by id tertentu
        else {
            $buku->id = $_GET['id'];
            $buku->readById();
            if ($buku->id != null) {
                $itemBuku = array(
                    "id" => $buku->id,
                    "judul" => $buku->judul,
                    "pengarang" => $buku->pengarang,
                    "tahun_terbit" => $buku->tahunTerbit,
                    "stok" => $buku->stok
                );
                http_response_code(200);
                echo json_encode($itemBuku);
            } else {
                http_response_code(404);
                echo json_encode(
                    array(
                        "message" => "Buku tidak ditemukan."
                    )
                );
            }
        }
        break;

    case 'POST':
        if (
            isset($_POST['judul']) && isset($_POST['pengarang']) && isset($_POST['tahun_terbit']) && isset($_POST['stok'])
        ) {
            $buku->judul = $_POST['judul'];
            $buku->pengarang = $_POST['pengarang'];
            $buku->tahunTerbit = $_POST['tahun_terbit'];
            $buku->stok = $_POST['stok'];

            if ($buku->create()) {
                http_response_code(201);
                echo json_encode(
                    array(
                        "message" => "Buku berhasil ditambahkan."
                    )
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array(
                        "code status" => 503,
                        "message" => "Gagal menambahkan produk."
                    )
                );
            }
        } else {
            http_response_code(400);
            $hasil = array(
                "code status" => 400,
                "message" => "Tidak dapat menambahkan produk."
            );
            echo json_encode($hasil);
        }
        break;

    case 'PUT':

        $data = json_decode(file_get_contents("php://input"));
        $id = $data->id;

        if ($id == "" || $id == null) {
            echo json_encode(
                array(
                    "message" => "Parameter id tidak boleh kosong."
                )
            );
        } else {
            $buku->id = $data->id;
            $buku->judul = $data->judul;
            $buku->pengarang = $data->pengarang;
            $buku->tahunTerbit = $data->tahun_terbit;
            $buku->stok = $data->stok;

            if ($buku->update()) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "message" => "Buku berhasil diperbarui."
                    )
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array(
                        "code status" => 503,
                        "message" => "Bad request, gagal memperbarui buku"
                    )
                );
            }
        }

        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(
                array(
                    "message" => "Parameter id tidak ada."
                )
            );
        } elseif ($_GET['id'] == null) {
            echo json_encode(
                array(
                    "message" => "Parameter id tidak boleh kosong."
                )
            );
        } else {
            $buku->id = $_GET['id'];
            if ($buku->delete()) {
                http_response_code(200);
                echo json_encode(
                    array(
                        "message" => "Buku berhasil dihapus."
                    )
                );
            } else {
                http_response_code(503);
                echo json_encode(
                    array(
                        "code status" => 503,
                        "message" => "Gagal menghapus buku."
                    )
                );
            }
        }
        break;

    default:
        http_response_code(404);
        echo "Request tidak ditemukan";
        break;
}
