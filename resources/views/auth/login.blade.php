<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Histori Warehouse</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            background: #f1f5f9;
            color: #0f172a;
            font-family: Arial, sans-serif;
        }

        main {
            width: 100%;
            max-width: 420px;
            padding: 28px;
            background: white;
            border-radius: 12px;
        }

        h1 { margin-top: 0; }

        p {
            color: #475569;
            line-height: 1.5;
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 6px;
        }

        input, button {
            width: 100%;
            padding: 12px;
            font: inherit;
            border-radius: 6px;
        }

        input { border: 1px solid #64748b; }

        button {
            margin-top: 24px;
            border: 0;
            background: #1d4ed8;
            color: white;
            cursor: pointer;
        }

        .error {
            padding: 12px;
            background: #fef2f2;
            color: #991b1b;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <main>
        <h1>Histori Warehouse</h1>
        <p>Masuk menggunakan akun bersama warehouse.</p>

        @if ($errors->any())
            <div class="error" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}">
            @csrf

            <label for="email">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="username"
                maxlength="255"
                required
                autofocus
            >

            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                maxlength="255"
                required
            >

            <button type="submit">Masuk</button>
        </form>

        <p>
            Akun bersama tidak membuktikan identitas individual.
            Petugas aktivitas akan dicatat secara terpisah.
        </p>
    </main>
</body>
</html>