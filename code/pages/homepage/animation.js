//používá se na zobrazení animace když je element .info na obrazovce 

document.addEventListener("DOMContentLoaded", function() {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        const info = entry.target;
  
        if (entry.isIntersecting) {
            info.classList.remove('info-hide-animation');
            info.classList.add('info-show-animation');
        } else {
            info.classList.add('info-hide-animation');
            info.classList.remove('info-show-animation');
        }
      });
    }, { threshold: 0.5 });
  
    const infoElements = document.querySelectorAll('.info');
  
    infoElements.forEach(infoElement => {
      observer.observe(infoElement);
    });
  });