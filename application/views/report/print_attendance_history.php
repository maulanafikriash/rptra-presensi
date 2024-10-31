<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Presensi Pegawai</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        h3,
        h4 {
            margin: 0;
        }
    </style>
</head>

<body>

    <h3>Riwayat Presensi Pegawai</h3>
    <h4>Nama: <?= $employee['employee_name']; ?></h4>
    <h4>Bulan: <?= date('F', mktime(0, 0, 0, $month, 1)) . " $year"; ?></h4>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Status Presensi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendance as $date => $status) : ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($date)); ?></td>
                    <td><?= $status; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>