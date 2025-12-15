@extends('layouts.app')

@section('content')


{{-- HEADER --}}
<div class="header">
    <div class="container">

        <div class="logo">
            <img src="{{ asset('storage/uploads/images/p.jpg') }}" alt="رادار الدواء Logo">
        </div>

        <div class="hotline">
            📞 الخط الساخن: <strong>19550</strong>
        </div>

       <!-- Search Form -->
  <!-- Search Form -->
<form action="{{ route('products.filter') }}" method="GET" class="search-bar" id="searchForm">
    <input type="text" name="q" id="search" placeholder="ابحث عن منتج..." autocomplete="off">

    <!-- صندوق الاقتراحات المباشرة -->
    <div id="suggestions" class="live-box"></div>

    <!-- صندوق "هل تقصد" بعد الضغط على بحث -->
    <div id="didYouMean" class="smart-box"></div> 

    <button type="submit">🔍 بحث</button>

    <select name="location" class="location-filter" onchange="this.form.submit()">
        <option value="" {{ session('last_location') ? '' : 'selected' }}>الكل</option>
        @foreach($locations as $loc)
            <option value="{{ $loc }}" {{ session('last_location') == $loc ? 'selected' : '' }} >{{ $loc }}</option>
        @endforeach
    </select>
</form>



<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
const searchInput = document.getElementById("search");
const liveBox = document.getElementById("suggestions");
const smartBox = document.getElementById("didYouMean");
const form = document.getElementById("searchForm");



// 🔹 LIVE SEARCH
// 🔹 LIVE SEARCH
searchInput.addEventListener("keyup", () => {
    const q = searchInput.value.trim();

    smartBox.style.display = "none"; // اخفاء هل تقصد

    // 🚀 Fix: Number instead of <script>
    if (q.length < 1) {
        liveBox.style.display = "none";
        return;
    }

    fetch(`/live-search?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            liveBox.innerHTML = "";

            if (data.length === 0) {
                liveBox.style.display = "none";
                return;
            }

            data.forEach(item => {
                const div = document.createElement("div");
                div.classList.add("suggest-item");

                div.innerHTML = `
                    <img src="/storage/uploads/images/${item.image}" alt="${item.name}" 
                         style="width:40px;height:40px;object-fit:cover;margin-right:10px;border-radius:5px;">
                    <span>${item.name}</span>
                `;

                div.onclick = () => {
                    searchInput.value = item.name;
                    liveBox.style.display = "none";
                    form.submit(); // 🚀 يعمل بحث تلقائي
                };

                liveBox.appendChild(div);
            });

            liveBox.style.display = "block";
        });
});

// 🔹 SMART SEARCH
// Track if a suggestion is currently visible
let suggestionVisible = false;

form.addEventListener("submit", function(e) {
    const q = searchInput.value.trim();
    if (!q) return; // allow empty submit if needed

    if (!suggestionVisible) {
        // First Enter → check smart search
        e.preventDefault(); // prevent submitting yet

        fetch(`/smart-search?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                if (data.did_you_mean && data.did_you_mean.toLowerCase() !== q.toLowerCase()) {
                    // Show suggestion
                   smartBox.innerHTML = `
    <p>هل تقصد: <strong id="suggestion" style="cursor:pointer;">${data.did_you_mean}</strong>؟</p>
`;

                    smartBox.style.display = "block";
                    suggestionVisible = true;

                    // Click suggestion → search suggested word
                    document.getElementById("suggestion").onclick = () => {
                        searchInput.value = data.did_you_mean;
                        suggestionVisible = false;
                        smartBox.style.display = "none";
                        form.submit(); // submit smart word
                    };
                } else {
                    // No suggestion → submit normally
                    suggestionVisible = false;
                    smartBox.style.display = "none";
                    form.submit();
                }
            })
            .catch(() => {
                // On error → submit normally
                suggestionVisible = false;
                smartBox.style.display = "none";
                form.submit();
            });

    } else {
        // Second Enter → user ignored suggestion → submit original word
        suggestionVisible = false;
        smartBox.style.display = "none";
        // allow normal submit, no preventDefault
    }
});

</script>



<script>
@if(Auth::check())
    window.Laravel = { userId: {{ Auth::id() }} };
@endif
</script>

@vite('resources/js/app.js')





   <script>
    // -----------------------------------------------------------
    // 1. TOASTR CONFIGURATION AND UTILITY FUNCTIONS
    // -----------------------------------------------------------
    
    // Configure Toastr options
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
    };

    function showLoginRequiredToast() {
        toastr.warning('You must sign in first to subscribe to restock notifications.', 'Sign In Required 🔒', { timeOut: 5000, extendedTimeOut: 1000 });
    }
    
    /**
     * Handles the user subscription (AJAX call).
     */
    function notifyMe(productId) {
        const button = document.getElementById(`notify-btn-${productId}`);
        button.disabled = true;
        // toastr.info("Subscribing you to restock alerts...", "Processing Request");

        fetch("{{ url('/notify-me') }}/" + productId, {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json", "Content-Type": "application/json" },
            body: JSON.stringify({})
        })
        .then(res => {
            if (!res.ok) {
                button.disabled = false;
                throw new Error("Server response was not successful.");
            }
            return res.json();
        })
        .then(data => {
            toastr.success(data.message || 'You have successfully subscribed to restock notifications.', "Subscribed!");
            button.innerHTML = 'Already Notified 🎉';
            button.classList.add('btn-notified');
        })
        .catch(err => {
            console.error(err);
            toastr.error('Could not complete your request. Please try again.', "Error!");
            button.disabled = false;
        });
    }

    function addToCart(productId) {
        toastr.success(`Product ${productId} added to cart!`, "Cart Updated");
    }


    // -----------------------------------------------------------
    // 2. REAL-TIME UI UPDATE HANDLER (Called from app.js)
    // -----------------------------------------------------------
    
    /**
     * This function executes the DOM replacement when the restock event is received.
     * It is made globally available (window.handleRestockUpdate) so the Echo listener
     * in your separate app.js file can call it.
     */
    window.handleRestockUpdate = function(e) { 
        // Debugging logs are omitted in the final version but confirmed success previously.
        
        const productId = e.product_id; 
        const productName = e.product_name || 'A product';
        const productUrl = e.product_url || '#'; 
        
        const actionsContainer = document.getElementById(`actions-${productId}`);
        const stockLabel = document.getElementById(`stock-label-${productId}`);

        if (actionsContainer) {
            
            // 1. HTML generation for the new "In Stock" buttons
            const inStockHtml = `
                <a href="${productUrl}" class="btn-details">عرض التفاصيل</a>
                <button class="btn-add-to-cart" onclick="addToCart(${productId})">
                    Add to Cart 🛒
                </button>
            `;
            
            // 2. Remove the "Out of Stock" label
            if (stockLabel) {
                stockLabel.remove();
            }
            
            // 3. Replace the entire actions block
            actionsContainer.innerHTML = inStockHtml;
            
            // 4. Final toast confirmation
            if (actionsContainer.querySelector('.btn-add-to-cart')) {
                toastr.success(`${productName} is now back in stock!`, "Restock Alert");
            }
        }
    }
</script>

        <div class="actions">
            <a href="#"><i class="fas fa-shopping-cart"></i> السلة</a>
            @if(!Auth::check())
            <a href="{{ url('/login') }}">تسجيل الدخول</a>
            @endif
             @auth
            <a href="{{ url('/logout') }}">تسجيل الخروج</a>
            @endauth
            <a href="{{ url('/register') }}">إنشاء حساب</a>
            <a href="#">🌐 العربية</a>
        </div>
    </div>
</div>


{{-- CATEGORIES --}}
<!-- Categories section -->
<div class="categories">
    <!-- <h2>التصنيفات</h2> -->
    <ul>
        @foreach($categories as $category)
            <li>
                <a href="">
                    {{ $category->name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>


<!-- Banners carousel -->
<div class="banner-carousel">

    <div class="banner-item">
        <img src="{{ asset('storage/uploads/banners/banner1.jpg') }}" alt="Banner 1">
        <div class="banner-text">
            <h3>تخفيضات كبيرة على المكملات الطبيعية</h3>
            <p>احصل على أفضل العروض والخصومات على جميع المنتجات الصحية</p>
        </div>
    </div>

    <div class="banner-item">
        <img src="{{ asset('storage/uploads/banners/banner2.jpg') }}" alt="Banner 2">
        <div class="banner-text">
            <h3>منتجات طبيعية وعضوية</h3>
            <p>استمتع بمجموعة واسعة من المكملات الطبيعية والمنتجات العضوية</p>
        </div>
    </div>

    <div class="banner-item">
        <img src="{{ asset('storage/uploads/banners/banner3.jpg') }}" alt="Banner 3">
        <div class="banner-text">
            <h3>خدمة توصيل سريعة</h3>
            <p>استلم طلباتك من الصيدلية مباشرة إلى باب منزلك بأسرع وقت</p>
        </div>
    </div>

</div>



{{-- FEATURED PRODUCTS --}}
<div class="featured">
    <h2 class="featured-title">
    <img src="{{ asset('storage/uploads/images/pp.jpg') }}" alt="icon">
    المنتجات المميزة
</h2>
<div class="products">
    @foreach($products as $product)
        @php
            // PHP Logic for Persistent Status Check
            $isSubscribedAndAwaitingNotification = false;
            if (Auth::check()) {

                $isSubscribedAndAwaitingNotification = $product->notifiedUsers()
                    ->wherePivot('user_id', Auth::id())
                    ->wherePivot('notified', false) 
                    ->exists(); 
            }
        @endphp

        <div class="product" id="product-card-{{ $product->id }}">

            {{-- Out of stock label (ID is crucial for real-time removal) --}}
            @if($product->quantity <= 0)
                <div class="out-of-stock-label" id="stock-label-{{ $product->id }}">Out of Stock 🚨</div>
            @endif

            {{-- Product Image, Name, Price, etc. --}}
            <img src="{{ asset('storage/uploads/images/' . $product->image) }}" alt="{{ $product->name }}">
            <h4>{{ $product->name }}</h4>
            <span class="price">{{ number_format($product->price, 2) }} جنيه</span>
            <p class="pharmacy">💼 {{ $product->pharmacy->name ?? '—' }}</p>
            <p class="location">📍 موقع الصيدلية: {{ $product->pharmacy->location ?? '—' }}</p>
            <p class="desc">
                {{ \Illuminate\Support\Str::limit($product->description, 50, '...') }}
            </p>

            {{-- Action Container (Target for Real-Time Update) --}}
            <div class="product-actions" id="actions-{{ $product->id }}">
                
                @if($product->quantity <= 0)
                    {{-- Product is Out of Stock --}}
                    @auth
                        @if($isSubscribedAndAwaitingNotification)
                            {{-- State 1: Already Notified (Persists on refresh) --}}
                            <button class="btn-notify btn-notified" disabled>
                                Already Notified 🎉
                            </button>
                        @else
                            {{-- State 2: Ready to Notify (New Subscription) --}}
                            <button 
                                id="notify-btn-{{ $product->id }}"
                                class="btn-notify" 
                                onclick="notifyMe({{ $product->id }})"
                            > 
                                Notify US 🔔
                            </button>
                        @endif
                    @else
                        {{-- State 3: Guest User (Calls Toastr function) --}}
                        <button class="btn-notify btn-login-required" onclick="showLoginRequiredToast()">
                            Notify US 🔔
                        </button>
                    @endauth
                @else
                    {{-- Product is In Stock --}}
                    <a href="" class="btn-details">عرض التفاصيل</a>
                    <button class="btn-add-to-cart" onclick="addToCart({{ $product->id }})">
                        Add to Cart 🛒
                    </button>
                @endif
            
            </div>
        </div>
    @endforeach
</div>

</div>



{{-- FOOTER --}}
<div class="footer">
    © 2025 رادار الدواء | جميع الحقوق محفوظة  
</div>
@endsection
