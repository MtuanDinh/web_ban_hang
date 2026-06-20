<main class="main-content">
        <div class="slideshow-container">
                <div class="slide fade">
                        <div class="numbertext">
                                1 / 3
                        </div>
                        <img src="assets/image/iPhone17ProMax_slide.webp" style="width : 100%">

                </div>
                  <div class="slide fade">
                        <div class="numbertext">
                                2 / 3
                        </div>
                        <img src="assets/image/Oppofin x9 ultra_slide.webp" style="width : 100%">
                        
                </div>
                  <div class="slide fade">
                        <div class="numbertext">
                                3 / 3
                        </div>
                        <img src="assets/image/samsung-galaxy-slide.webp" style="width : 100%">
                        
                </div>
        
        </div>
        <br>
        
</main>
        <script>
 let slideIndex = 0;
showSlides();

function showSlides() {
  let i;
  let slides = document.getElementsByClassName("slide");
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slideIndex++;

  if (slideIndex > slides.length) {slideIndex = 1}
  slides[slideIndex-1].style.display = "block";
  setTimeout(showSlides, 2000); // Change image every 2 seconds
}
</script>