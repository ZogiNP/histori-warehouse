<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Warehouse</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }

        main {
            padding: 32px;
            text-align: center;
        }

        h1 {
            font-size: clamp(28px, 5vw, 44px);
        }

        p {
            color: #475569;
        }
    </style>
</head>
<body>
    <main>
        <h1>Selamat Datang di Histori Warehouse</h1>
        <p>Aplikasi histori aktivitas barang warehouse.</p>
    </main>
</body>
</html>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Keluar</button>
</form>