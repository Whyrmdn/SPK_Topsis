<?php
require_once 'config/db.php';

function hitungTOPSIS() {
    global $koneksi;

    // 1. Ambil data kriteria
    $query_kriteria = "SELECT * FROM kriteria ORDER BY kode_kriteria";
    $result_kriteria = mysqli_query($koneksi, $query_kriteria);
    $kriteria = [];
    while ($row = mysqli_fetch_assoc($result_kriteria)) {
        $kriteria[$row['id']] = $row;
    }

    // 2. Ambil data alternatif
    $query_alternatif = "SELECT * FROM alternatif ORDER BY kode_alternatif";
    $result_alternatif = mysqli_query($koneksi, $query_alternatif);
    $alternatif = [];
    while ($row = mysqli_fetch_assoc($result_alternatif)) {
        $alternatif[$row['id']] = $row;
    }

    // 3. Ambil data nilai
    $query_nilai = "SELECT * FROM nilai_alternatif";
    $result_nilai = mysqli_query($koneksi, $query_nilai);
    $nilai = [];
    while ($row = mysqli_fetch_assoc($result_nilai)) {
        $nilai[$row['id_alternatif']][$row['id_kriteria']] = $row['nilai'];
    }

    // Cek apakah semua alternatif memiliki nilai untuk semua kriteria
    foreach ($alternatif as $id_alt => $alt) {
        foreach ($kriteria as $id_krit => $krit) {
            if (!isset($nilai[$id_alt][$id_krit])) {
                return ['error' => "Alternatif {$alt['nama_alternatif']} belum memiliki nilai untuk kriteria {$krit['nama_kriteria']}"];
            }
        }
    }

    // 4. Normalisasi Matrix (R)
    $matrix_r = [];
    foreach ($kriteria as $id_krit => $krit) {
        $sum_squares = 0;
        foreach ($alternatif as $id_alt => $alt) {
            $sum_squares += pow($nilai[$id_alt][$id_krit], 2);
        }
        $sqrt_sum = sqrt($sum_squares);

        foreach ($alternatif as $id_alt => $alt) {
            $matrix_r[$id_alt][$id_krit] = $nilai[$id_alt][$id_krit] / $sqrt_sum;
        }
    }

    // 5. Matrix Ternormalisasi Terbobot (Y)
    $matrix_y = [];
    foreach ($alternatif as $id_alt => $alt) {
        foreach ($kriteria as $id_krit => $krit) {
            $matrix_y[$id_alt][$id_krit] = $matrix_r[$id_alt][$id_krit] * $krit['bobot'];
        }
    }

    // 6. Solusi Ideal Positif (A+) dan Negatif (A-)
    $a_plus = [];
    $a_minus = [];

    foreach ($kriteria as $id_krit => $krit) {
        $values = [];
        foreach ($alternatif as $id_alt => $alt) {
            $values[] = $matrix_y[$id_alt][$id_krit];
        }

        if ($krit['tipe'] == 'Benefit') {
            $a_plus[$id_krit] = max($values);
            $a_minus[$id_krit] = min($values);
        } else { // Cost
            $a_plus[$id_krit] = min($values);
            $a_minus[$id_krit] = max($values);
        }
    }

    // 7. Jarak ke Solusi Ideal
    $d_plus = [];
    $d_minus = [];

    foreach ($alternatif as $id_alt => $alt) {
        $sum_plus = 0;
        $sum_minus = 0;

        foreach ($kriteria as $id_krit => $krit) {
            $sum_plus += pow($matrix_y[$id_alt][$id_krit] - $a_plus[$id_krit], 2);
            $sum_minus += pow($matrix_y[$id_alt][$id_krit] - $a_minus[$id_krit], 2);
        }

        $d_plus[$id_alt] = sqrt($sum_plus);
        $d_minus[$id_alt] = sqrt($sum_minus);
    }

    // 8. Nilai Preferensi (V)
    $preferensi = [];
    foreach ($alternatif as $id_alt => $alt) {
        $denominator = $d_plus[$id_alt] + $d_minus[$id_alt];
        if ($denominator != 0) {
            $preferensi[$id_alt] = $d_minus[$id_alt] / $denominator;
        } else {
            $preferensi[$id_alt] = 0;
        }
    }

    // 9. Ranking
    arsort($preferensi);
    $ranking = [];
    $rank = 1;
    foreach ($preferensi as $id_alt => $nilai_pref) {
        $ranking[$id_alt] = [
            'ranking' => $rank++,
            'nilai_preferensi' => $nilai_pref,
            'alternatif' => $alternatif[$id_alt]
        ];
    }

    // 10. Simpan hasil ke database
    // Hapus hasil sebelumnya
    mysqli_query($koneksi, "DELETE FROM hasil_topsis");

    // Simpan hasil baru
    foreach ($ranking as $id_alt => $data) {
        $query_insert = "INSERT INTO hasil_topsis (id_alternatif, nilai_preferensi, ranking) VALUES ($id_alt, {$data['nilai_preferensi']}, {$data['ranking']})";
        mysqli_query($koneksi, $query_insert);
    }

    return [
        'success' => true,
        'matrix_awal' => $nilai,
        'matrix_r' => $matrix_r,
        'matrix_y' => $matrix_y,
        'a_plus' => $a_plus,
        'a_minus' => $a_minus,
        'd_plus' => $d_plus,
        'd_minus' => $d_minus,
        'preferensi' => $preferensi,
        'ranking' => $ranking,
        'kriteria' => $kriteria,
        'alternatif' => $alternatif
    ];
}

// Jika dipanggil langsung untuk perhitungan
if (isset($_GET['hitung'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit();
    }

    $hasil = hitungTOPSIS();
    if (isset($hasil['error'])) {
        $_SESSION['error_message'] = $hasil['error'];
    } else {
        $_SESSION['success_message'] = "Perhitungan TOPSIS berhasil dilakukan!";
    }

    header("Location: hasil.php");
    exit();
}
?>