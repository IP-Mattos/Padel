document.addEventListener('DOMContentLoaded', () => {
  let items = document.querySelectorAll('.slider .list .item')
  let prevBtn = document.getElementById('prev')
  let nextBtn = document.getElementById('next')
  let lastPosition = items.length - 1
  let firstPosition = 0
  let active = 0
  let intervalId

  // Get the modal
const modal = document.getElementById("myModal");

// Get the button that opens the modal
const openModalButton = document.getElementById("openModal");
const openModalButton2 = document.getElementById("openModal2");

// Get the <span> element that closes the modal
const span = document.getElementsByClassName("close")[0];

// Get the button and form elements
const formOneButton = document.getElementById("formOneButton");
const formTwoButton = document.getElementById("formTwoButton");
const formThreeButton = document.getElementById("formThreeButton")
const returnButton = document.getElementById("returnButton");
const returnButtonAction = document.getElementById("returnButtonAction");
const formOne = document.getElementById("formOne");
const formTwo = document.getElementById("formTwo");
const formThree = document.getElementById("formThree");
const buttonContainer = document.getElementById("buttonContainer");

// Function to open the modal
function openModal() {
    modal.style.display = "block";
    setInitialContent(); // Set initial content on open
    setTimeout(() => {
        modal.querySelector(".modal-content").classList.add("show"); // Add the show class for sliding effect
    }, 10); // Small timeout to ensure the display block is set
}

// Function to set initial content
function setInitialContent() {
    formOne.classList.add("hidden");
    formTwo.classList.add("hidden");
    formThree.classList.add("hidden")
    returnButton.classList.add("hidden"); // Hide the return button
    buttonContainer.style.display = "inline-block";
}

// Event listeners for the form switching buttons
formOneButton.onclick = function() {
    formOne.classList.remove("hidden"); // Show Form One
    formTwo.classList.add("hidden"); // Hide Form Two
    formThree.classList.add("hidden"); //Hide Form Three
    returnButton.classList.remove("hidden"); // Show Return button
    buttonContainer.style.display = "none";
}

formTwoButton.onclick = function() {
    formTwo.classList.remove("hidden"); // Show Form Two
    formOne.classList.add("hidden"); // Hide Form One
    formThree.classList.add("hidden"); //Hide Form Three
    returnButton.classList.remove("hidden"); // Show Return button
    buttonContainer.style.display = "none";
}

formThreeButton.onclick = function(){
    formThree.classList.remove("hidden"); //Show Form Three
    formOne.classList.add("hidden"); //Hide Form One
    formTwo.classList.add("hidden"); //Hide Form Two
    returnButton.classList.remove("hidden"); //Show Return button
    buttonContainer.style.display = "none";
}

// Return button action
returnButtonAction.onclick = function() {
    setInitialContent(); // Reset to initial content
}

// When the user clicks the button, open the modal 
openModalButton.onclick = openModal;
openModalButton2.onclick = openModal;

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.querySelector(".modal-content").classList.remove("show"); // Remove the show class
    setTimeout(() => {
        modal.style.display = "none"; // Hide after animation
    }, 500); // Match this duration with the CSS transition duration
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.querySelector(".modal-content").classList.remove("show");
        setTimeout(() => {
            modal.style.display = "none";
        }, 300);
    }
}

const input = document.getElementById('cedula');
const input2 = document.getElementById('phone');

input.addEventListener('input', function(){
  this.value = this.value.replace(/[^0-9]/g, '');
});
input2.addEventListener('input', function(){
  this.value = this.value.replace(/[^0-9]/g, '');
});

  const setSlider = () => {
    let oldActive = document.querySelector('.slider .list .item.active')
    if (oldActive) oldActive.classList.remove('active')
    items[active].classList.add('active')
    //
    nextBtn.classList.remove('d-none')
    prevBtn.classList.remove('d-none')
    if (active === lastPosition) nextBtn.classList.add('d-none')
    if (active === firstPosition) prevBtn.classList.add('d-none')
  }

  const startAutoSlide = () => {
    intervalId = setInterval(() => {
      active = (active + 1) % items.length
      setSlider()
    }, 4000) // Cambia la imagen cada 5 segundos
  }

  const stopAutoSlide = () => {
    clearInterval(intervalId)
  }

  nextBtn.onclick = () => {
    stopAutoSlide()
    active = active + 1
    if (active > lastPosition) active = firstPosition
    setSlider()
    startAutoSlide()
  }

  prevBtn.onclick = () => {
    stopAutoSlide()
    active = active - 1
    if (active < firstPosition) active = lastPosition
    setSlider()
    startAutoSlide()
  }

  setSlider()
  startAutoSlide()

  document.querySelectorAll('.accordion').forEach((button) => {
    button.addEventListener('click', () => {
      const panel = button.nextElementSibling
      const icon = button.querySelector('.accordion-icon')

      button.classList.toggle('active')

      if (panel.style.maxHeight) {
        panel.style.maxHeight = null
        panel.classList.remove('open')
      } else {
        panel.style.maxHeight = panel.scrollHeight + 'px'
        panel.classList.add('open')
      }

      icon.textContent = button.classList.contains('active') ? '−' : '+'
    })
  })
  // set diameter
  const setDiameter = () => {
    let slider = document.querySelector('.slider')
    let widthSlider = slider.offsetWidth
    let heightSlider = slider.offsetHeight
    let diameter = Math.sqrt(Math.pow(widthSlider, 2) + Math.pow(heightSlider, 2))
    document.documentElement.style.setProperty('--diameter', diameter + 'px')
  }
  setDiameter()
  window.addEventListener('resize', () => {
    setDiameter()
  })
})
