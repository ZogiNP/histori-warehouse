<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Histori Warehouse')</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            line-height: 1.5;
        }

        nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
            padding: 16px 24px;
            background: white;
            border-bottom: 1px solid #cbd5e1;
        }

        nav form { margin-left: auto; }

        main {
            max-width: 960px;
            margin: 32px auto;
            padding: 24px;
            background: white;
            border-radius: 10px;
        }

        a { color: #1d4ed8; }

        button, .tombol {
            display: inline-block;
            padding: 10px 16px;
            border: 0;
            border-radius: 6px;
            background: #1d4ed8;
            color: white;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input, select  {
            width: 100%;
            max-width: 480px;
            padding: 12px;
            border: 1px solid #64748b;
            border-radius: 6px;
            font: inherit;
        }

        .aksi {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
            margin-top: 24px;
        }

        .pesan {
            padding: 12px;
            background: #dcfce7;
            color: #166534;
            border-radius: 6px;
        }

        .error { color: #991b1b; }

        .tabel { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }

        @media (max-width: 600px) {
            main {
                margin: 16px;
                padding: 16px;
            }

            h1 { font-size: 26px; }
        }
    </style>
</head>
<body>
    <nav aria-label="Navigasi utama">
        <a href="{{ route('home') }}">Histori Warehouse</a>
        <a href="{{ route('divisi.index') }}">Data Divisi</a>
        <a href="{{ route('petugas.index') }}">Data Petugas</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </nav>

    <main>
        @if (session('status'))
            <p class="pesan" role="status">{{ session('status') }}</p>
        @endif

        @yield('content')
    </main>
</body>
</html>