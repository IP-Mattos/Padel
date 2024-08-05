document.addEventListener('DOMContentLoaded', () => {
  let items = document.querySelectorAll('.slider .list .item')
  let prevBtn = document.getElementById('prev')
  let nextBtn = document.getElementById('next')
  let lastPosition = items.length - 1
  let firstPosition = 0
  let active = 0
  let intervalId

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
