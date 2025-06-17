<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - ບໍ່ມີສິດເຂົ້າໃຊ້</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Lao', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 32rem;
            width: 90%;
        }
        .error-icon {
            font-size: 4rem;
            color: #ef4444;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #ef4444;
        }
        .error-message {
            font-size: 1rem;
            margin-bottom: 1.5rem;
            color: #64748b;
        }
        .back-button {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #ef4444;
            color: white;
            text-decoration: none;
            border-radius: 0.25rem;
            transition: background-color 0.2s;
        }
        .back-button:hover {
            background-color: #dc2626;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">403 - ບໍ່ມີສິດເຂົ້າໃຊ້</h1>
        <p class="error-message">{{ $exception->getMessage() ?? 'ທ່ານບໍ່ມີສິດໃນການເຂົ້າໃຊ້ລະບົບນີ້' }}</p>
        <a href="{{ url()->previous() }}" class="back-button">ກັບຄືນໜ້າກ່ອນ</a>
    </div>
</body>
</html> 