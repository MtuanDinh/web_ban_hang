//slideshow
let slideIndex = 0;
showSlides();

function showSlides() {
  let i;
  let slides = document.getElementsByClassName("slide");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slideIndex++;

  if (slideIndex > slides.length) {
    slideIndex = 1;
  }
  slides[slideIndex - 1].style.display = "block";
  setTimeout(showSlides, 2000); // Change image every 2 seconds
}

//modal_location
const btnOpenLocation = document.querySelector('nav ul li:nth-child(3) > a');
const modalLocation = document.querySelector('.modal-location');
const btnClose = document.querySelector('.close-btn');
const listProvinces = document.querySelectorAll('.location-list li');

btnOpenLocation.addEventListener('click', function(event) {
    event.preventDefault();
    modalLocation.style.display = 'flex';
});

btnClose.addEventListener('click', function(event) {
    event.preventDefault();
    modalLocation.style.display = 'none';
});

// 4. Xử lý khi bấm vào 1 Tỉnh/Thành phố bất kỳ
listProvinces.forEach(function(province) {
    province.addEventListener('click', function() {
        listProvinces.forEach(function(item) {
            item.classList.remove('active');

            const checkIcon = item.querySelector('.fa-circle-check');
            if (checkIcon) {
                checkIcon.remove();
            }
        });

        this.classList.add('active');
        this.innerHTML += ' <i class="fa-solid fa-circle-check"></i>';

        let selectedLocation = this.textContent.trim();

        btnOpenLocation.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> ' + selectedLocation + ' <i class="fa-solid fa-angle-down"></i>';

        const shippingTexts = document.querySelectorAll('.shipping-location');
        shippingTexts.forEach(function(shipText) {
            shipText.textContent = selectedLocation;
        });

        modalLocation.style.display = 'none';
    });
});
