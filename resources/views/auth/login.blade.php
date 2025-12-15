<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
     body {
    font-family: 'Cairo', sans-serif;
    color: #fff;
    height: 100vh;
    }
    body::before {
        content: "";
        position: absolute;
        top:0; left:0;
        width:100%; height:100%;
        background-color: rgba(0,0,0,0.4);
        z-index: -1;
    background: url('{{ asset("storage/uploads/images/pharmacy-bg.jpg") }}') no-repeat center center/cover;

    }

        .card {
            background-color: rgba(255, 255, 255, 0.9);
            color: #0b3d2e;
            border-radius: 20px;
        }
        .btn-primary {
            background-color: #2a9d8f;
            border: none;
        }
        .btn-primary:hover {
            background-color: #21867a;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">


<!-- Display success message -->
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Display error message -->
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

    <div class="col-md-5">
        <div class="card shadow p-4">
            <h3 class="text-center mb-4">تسجيل الدخول</h3>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                </div>

                <div class="mb-3">
                    <label for="password">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" placeholder="********" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">دخول</button>

                <div class="text-center mt-3">
                    <p>ليس لديك حساب؟ <a href="{{ url('/register') }}">أنشئ حسابًا جديدًا</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
