<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Card Lookup</title>
    <style>
        :root {
            --bg: #f6f1e8;
            --ink: #17141e;
            --muted: #6e6a78;
            --line: #e5dccf;
            --panel: rgba(255, 255, 255, 0.94);
            --brand: #1240ab;
            --accent: #0f766e;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 18px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(18, 64, 171, 0.08), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15, 118, 110, 0.08), transparent 24%),
                var(--bg);
            color: var(--ink);
        }

        .lookup-card {
            width: min(100%, 620px);
            padding: 30px;
            border-radius: 30px;
            background: var(--panel);
            border: 1px solid rgba(229, 220, 207, 0.9);
            box-shadow: 0 20px 60px rgba(23, 20, 30, 0.08);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #eef3ff;
            color: var(--brand);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        h1 {
            margin: 18px 0 10px;
            font-size: clamp(32px, 5vw, 54px);
            line-height: 0.95;
            letter-spacing: -0.05em;
        }

        p {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .field {
            width: 100%;
            padding: 18px 20px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 28px;
            letter-spacing: 0.18em;
            font-family: Consolas, Monaco, monospace;
            text-align: center;
            outline: none;
        }

        .field:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(18, 64, 171, 0.12);
        }

        .meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
            font-size: 13px;
            color: var(--muted);
        }

        .meta.ready {
            color: var(--accent);
            font-weight: 700;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 26px;
        }

        .btn {
            flex: 1 1 220px;
            padding: 16px 18px;
            border-radius: 18px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1444b8, #2f63de);
            color: #fff;
        }

        .btn-secondary {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--line);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .error {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff1ef;
            color: #b42318;
            border: 1px solid #f5b1a8;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <form class="lookup-card" method="POST" action="{{ route('user.gps-card-lookup.search') }}" id="lookupForm">
        @csrf
        <span class="pill">Direct Device Lookup</span>
        <h1>Find Every Linked Detail</h1>
        <p>Enter the 16 digit GPS card number to see customer, vehicle, product, activation, KYC, recharge, complaint and deletion audit details in one printable report.</p>

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <label for="cardInput">GPS Card Number</label>
        <input
            id="cardInput"
            name="card_number"
            type="text"
            value="{{ old('card_number') }}"
            placeholder="0000 0000 0000 0000"
            maxlength="19"
            inputmode="numeric"
            autocomplete="off"
            class="field"
        >

        <div class="meta" id="digitMeta">
            <span>Only digits are accepted.</span>
            <span id="digitCount">0 / 16</span>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit">Open Full Device Report</button>
            <a class="btn btn-secondary" href="{{ url('/login') }}">Admin Login</a>
        </div>
    </form>

    <script>
        const input = document.getElementById('cardInput');
        const count = document.getElementById('digitCount');
        const meta = document.getElementById('digitMeta');

        const updateCardValue = () => {
            const raw = input.value.replace(/\D/g, '').slice(0, 16);
            input.value = raw.match(/.{1,4}/g)?.join(' ') ?? '';
            count.textContent = raw.length + ' / 16';
            meta.classList.toggle('ready', raw.length === 16);
        };

        updateCardValue();
        input.addEventListener('input', updateCardValue);

        document.getElementById('lookupForm').addEventListener('submit', function () {
            input.value = input.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>
