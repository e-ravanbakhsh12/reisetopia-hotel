document.addEventListener("DOMContentLoaded", function() {  
    const minPriceInput = document.getElementById("hotel-min-price");  
    const maxPriceInput = document.getElementById("hotel-max-price");  
    
    const minPriceSign = document.querySelectorAll('.sign')[0].querySelector('span');  
    const maxPriceSign = document.querySelectorAll('.sign')[1].querySelector('span');  

    function updateMinPrice() {  
        minPriceInput.value = Math.min(minPriceInput.value, maxPriceInput.value - 1);  
        const value = (100 / (parseInt(minPriceInput.max) - parseInt(minPriceInput.min))) * parseInt(minPriceInput.value) -   
                      (100 / (parseInt(minPriceInput.max) - parseInt(minPriceInput.min))) * parseInt(minPriceInput.min);  

        const children = minPriceInput.parentNode.childNodes[1].childNodes;  
        children[1].style.width = value + '%';  
        children[5].style.left = value + '%';  
        children[7].style.left = value + '%';  
        children[11].style.left = value + '%';  
        children[11].childNodes[1].innerHTML = minPriceInput.value;  
    }  

    function updateMaxPrice() {  
        maxPriceInput.value = Math.max(maxPriceInput.value, minPriceInput.value - (-1));  
        const value = (100 / (parseInt(maxPriceInput.max) - parseInt(maxPriceInput.min))) * parseInt(maxPriceInput.value) -   
                      (100 / (parseInt(maxPriceInput.max) - parseInt(maxPriceInput.min))) * parseInt(maxPriceInput.min);  

        const children = maxPriceInput.parentNode.childNodes[1].childNodes;  
        children[3].style.width = (100 - value) + '%';  
        children[5].style.right = (100 - value) + '%';  
        children[9].style.left = value + '%';  
        children[13].style.left = value + '%';  
        children[13].childNodes[1].innerHTML = maxPriceInput.value;  
    }  

    minPriceInput.addEventListener('input', updateMinPrice);  
    maxPriceInput.addEventListener('input', updateMaxPrice);  

    // Initialize the values on page load  
    updateMinPrice();  
    updateMaxPrice();  
});  