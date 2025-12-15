
import echo from './echo';
import toastr from 'toastr'; 
import 'toastr/build/toastr.min.css'; 

// Make Echo available globally
window.Echo = echo;

/**
 * 3. The Notification Function (Ensure this is available within the listener's scope)
 */
function showNotifyToast(productName, productUrl) {
    // Use Toastr instead of alert()
    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000",
    };
    
    // Create the message content with a link
    const message = `Product back in stock: ${productName}<br><a href="${productUrl}" target="_blank">View Product</a>`;
    
    // Display the success toaster
    toastr.success(message, 'Stock Alert'); 
}


// 2. The Listener Block (Modified to call the UI update function)
document.addEventListener('DOMContentLoaded', () => {
    if (window.Laravel?.userId && window.Echo) {
      window.Echo.private(`user-${window.Laravel.userId}`) // CHANGE IS HERE
         .listen('.ProductBackInStock', (e) => {
                
                console.log('Event received! Data:', e); 
                
                // 1. Call the existing toast notification function
                showNotifyToast(e.product_name, e.product_url); 
                
                // 2. CRITICAL FIX: Call the global UI update function defined in the Blade file
                // This updates the buttons on the product listing page in real-time.
                if (typeof window.handleRestockUpdate === 'function') {
                    window.handleRestockUpdate(e);
                } else {
                    console.error("UI update function handleRestockUpdate not found. Ensure it is defined globally in the Blade view.");
                }
            });
    }
});