export function updateImageSource() {
    const imageSources = {
        hd: '../../assets/img/icons/bghd.jpg',
        fullHd: '../../assets/img/icons/bgfullhd.jpg',
        qhd: '../../assets/img/icons/bg1440.jpg',
        wqhd: '../../assets/img/icons/bgwqhd.jpg',
        uhd: '../../assets/img/icons/bg4k.jpg',
      };
    
    const windowWidth = window.innerWidth;

    if (windowWidth < 1920) {
      document.getElementById('bg').src = imageSources.hd;
    } else if (windowWidth < 2560) {
      document.getElementById('bg').src = imageSources.fullHd;
    } else if (windowWidth < 3440) {
      document.getElementById('bg').src = imageSources.qhd;
    } else if (windowWidth < 3840) {
      document.getElementById('bg').src = imageSources.wqhd;
    } else {
      document.getElementById('bg').src = imageSources.uhd;
    }
}
