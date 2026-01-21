/* 
   HOMEPAGE SLIDER
    */
if(document.body.classList.contains("home-page") || document.querySelector(".slider-container")) {
    let index = 0;
    const slides = document.getElementById("slideBox");
    const dots = document.querySelectorAll(".dot");
    const totalSlides = dots.length;

    function showSlide(i){
        index = (i + totalSlides) % totalSlides;
        slides.style.transform = `translateX(-${index * 100}%)`;

        dots.forEach(dot => dot.classList.remove("active-dot"));
        dots[index].classList.add("active-dot");
    }

    function moveSlide(step){
        showSlide(index + step);
    }

    function currentSlide(i){
        showSlide(i);
    }

    setInterval(() => {
        moveSlide(1);
    }, 4000);

    window.moveSlide = moveSlide;
    window.currentSlide = currentSlide;
}

/*
   PRODUCT PAGE SHOW/HIDE EXTRA
    */
if(document.body.classList.contains("product-page")) {
    let open = false;
    const showBtn = document.getElementById("showBtn");

    showBtn.addEventListener("click", function() {
        const extraItems = document.querySelectorAll(".extra");
        if (!open) {
            extraItems.forEach(product => product.style.display = "block");
            this.innerText = "Show Less";
        } else {
            extraItems.forEach(product => product.style.display = "none");
            this.innerText = "Show More";
        }
        open = !open;
    });
}

/* 
   SUBSCRIPTION PAGE
    */
if(document.body.classList.contains("subscription-page")) {
    const subscribeBtn = document.getElementById("subscribeBtn");
    if(subscribeBtn){
        subscribeBtn.addEventListener("click", function (e) {
            e.preventDefault(); // prevent default form submission to allow PHP
            const email = document.getElementById("emailInput").value.trim();
            const message = document.getElementById("message");
            const pattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

            if (email === "") {
                message.style.color = "red";
                message.innerText = "Email field cannot be empty!";
                return;
            }

            if (!pattern.test(email)) {
                message.style.color = "red";
                message.innerText = "Please enter a valid email address.";
                return;
            }

            document.getElementById("subscribeForm").submit();
        });
    }
}

/*
   PROMO PAGE
    */
if(document.body.classList.contains("promo-page")) {
    const promoForm = document.getElementById("promoForm");
    if(promoForm){
        promoForm.addEventListener("submit", function (e) {
            // JS validation optional here; PHP handles it
            // Prevent accidental empty submission
            const promoInput = document.getElementById("promoInput").value.trim();
            if(promoInput === ""){
                e.preventDefault();
                const msg = document.getElementById("promoMsg");
                msg.style.color = "red";
                msg.innerText = "Please enter a promo code.";
            }
        });
    }
}

/* 
   CONTACT PAGE (PROBLEM FORM)
    */
if(document.body.classList.contains("contact-page")) {
    // DO NOT prevent submission; PHP handles validation
    // You can optionally add gentle client-side hints without blocking submission
    const contactForm = document.getElementById("contactForm");
    if(contactForm){
        // optional: show hint but don't prevent submit
        contactForm.addEventListener("submit", function(e){
            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const message = document.getElementById("message").value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            const msg = document.getElementById("contactMessage");
            if(msg) msg.innerText = ""; // clear previous hints

            if(name === "" || !emailPattern.test(email) || message.length < 10){
                // show hint but allow PHP to process
                if(msg) msg.innerText = "Please ensure all fields are filled correctly.";
            }
        });
    }
}
