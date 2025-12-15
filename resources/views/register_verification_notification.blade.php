@extends('layouts.app')

@section('content')
<style>
body {
    background: url('{{ asset("storage/uploads/images/pharmacy-bg.jpg") }}') no-repeat center center/cover;
    color: #ffffff;
    font-family: 'Cairo', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.card {
    background-color: rgba(0, 60, 80, 0.85);
    padding: 2rem;
    border-radius: 20px;
    text-align: center;
    box-shadow: 0 0 25px rgba(0,0,0,0.5);
    width: 90%;
    max-width: 460px;
    position: relative;
    backdrop-filter: blur(6px);
}

.card img {
    width: 90px;
    height: 90px;
    object-fit: contain;
    margin-bottom: 1rem;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    padding: 10px;
}

.card h1 {
    font-size: 1.8rem;
    color: #34bfa3;
    font-weight: 800;
    margin-bottom: 1rem;
}

.card h3 {
    color: #aef1d5;
    margin-bottom: 1rem;
    font-weight: bold;
}

.card p {
    font-size: 1.1rem;
    color: #e6f4f1;
    line-height: 1.8;
}

.btn-custom {
    display: inline-block;
    background-color: #34bfa3;
    color: #fff;
    padding: 0.7rem 1.5rem;
    border-radius: 10px;
    text-decoration: none;
    transition: background-color 0.3s ease-in-out;
    margin-top: 1rem;
    font-weight: bold;
}

.btn-custom:hover {
    background-color: #2ca189;
}
</style>

<div class="card">
    {{-- شعار أو أيقونة المشروع --}}
    <img src="{{ asset('storage/uploads/images/p.jpg') }}" alt="Pharmacy Icon">

    {{-- اسم المشروع --}}
    <h1>💊 رادار الدواء</h1>

    <h3>✅ تم تسجيلك بنجاح!</h3>
    <p>
        شكرًا لانضمامك إلى نظام <strong>رادار الدواء</strong>.<br>
        @if(isset($user) && $user->role === 'vendor')
            لقد تم استلام <strong>رخصة الصيدلية</strong> الخاصة بك، وسيتم مراجعتها من قبل الإدارة قريبًا.<br>
            سنرسل إليك إشعارًا عند تفعيل حسابك لبدء إضافة الأدوية.
        @else
            يمكنك الآن تسجيل الدخول والبدء في البحث عن الأدوية بسهولة.
        @endif
    </p>

    <a href="{{ url('/login') }}" class="btn-custom">تسجيل الدخول</a>
</div>
@endsection
