document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.getElementById('menuToggle')
  const mobileMenu = document.getElementById('mobileMenu')
  const closeMenu = document.getElementById('closeMenu')
  const banner = document.getElementById('banner')
  const prevBtn = document.getElementById('prev')
  const nextBtn = document.getElementById('next')

  const images = [
    'https://plus.unsplash.com/premium_photo-1708692920701-19a470ecd667?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://images.unsplash.com/photo-1622163642998-1ea32b0bbc67?q=80&w=1935&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
    'https://images.unsplash.com/photo-1510846699902-9211b99dac11?q=80&w=1936&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
  ]

  let currentIndex = 0
  let intervalId

  // Función para pre-cargar las imágenes
  const preloadImages = (imageUrls) => {
    imageUrls.forEach((url) => {
      const img = new Image()
      img.src = url
    })
  }

  // Pre-cargar las imágenes
  preloadImages(images)

  const updateBannerImage = () => {
    banner.classList.add('fade-out')
    setTimeout(() => {
      banner.style.backgroundImage = `url(${images[currentIndex]})`
      banner.classList.remove('fade-out')
      banner.classList.add('fade-in')
      setTimeout(() => {
        banner.classList.remove('fade-in')
      }, 500) // Tiempo para asegurar la animación de opacidad
    }, 500) // Tiempo para la animación de opacidad de salida
  }

  const startAutoSlide = () => {
    intervalId = setInterval(() => {
      currentIndex = (currentIndex + 1) % images.length
      updateBannerImage()
    }, 5000) // Cambia la imagen cada 5 segundos
  }

  const stopAutoSlide = () => {
    clearInterval(intervalId)
  }

  menuToggle.addEventListener('click', () => {
    mobileMenu.classList.add('show')
  })

  closeMenu.addEventListener('click', () => {
    mobileMenu.classList.remove('show')
  })

  prevBtn.addEventListener('click', () => {
    stopAutoSlide()
    currentIndex = (currentIndex - 1 + images.length) % images.length
    updateBannerImage()
    startAutoSlide()
  })

  nextBtn.addEventListener('click', () => {
    stopAutoSlide()
    currentIndex = (currentIndex + 1) % images.length
    updateBannerImage()
    startAutoSlide()
  })

  startAutoSlide()

  window.addEventListener('scroll', () => {
    const header = document.querySelector('header')
    if (window.scrollY > 50) {
      header.classList.add('scrolled')
    } else {
      header.classList.remove('scrolled')
    }
  })
})
