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

.unfound-container {
    margin: 80px auto;
    max-width: 600px;
    background: #ffffff;
    padding: 30px 35px;
    border-radius: 14px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    direction: rtl;
    text-align: right;
    font-family: "Tajawal", sans-serif;
}

.unfound-container h2 {
    font-size: 22px;
    color: #444;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.7;
}

.suggest-box {
    margin-top: 15px;
    padding: 15px 20px;
    background: #f8f9fa;
    border: 1px solid #dcdcdc;
    border-radius: 10px;
    font-size: 18px;
    color: #555;
}

.suggest-box a {
    color: #007bff;
    font-weight: bold;
    text-decoration: none;
    cursor: pointer;
}

.suggest-box a:hover {
    text-decoration: underline;
}

.sad-icon {
    font-size: 45px;
    text-align: center;
    margin-bottom: 15px;
    opacity: 0.8;
}

</style>


<div class="unfound-container">

    <div class="sad-icon">😕</div>

    <h2>
        لم يتم العثور على نتائج لـ:
        <strong style="color:#c0392b;">"{{ $query }}"</strong>
    </h2>

    @if($suggest)
        <div class="suggest-box">
            هل تقصد:
            <a href="{{ route('products.filter', ['q' => $suggest]) }}">
                {{ $suggest }}
            </a>
            ؟
              <button 
                                id="notify-btn-{{ $product->id }}"
                                class="btn-notify" 
                            > 
                                Notify US 🔔
                            </button>
        </div>
    @endif

</div>
