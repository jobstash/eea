// Import Swiper and required styles
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/swiper-bundle.css';
// Import Lottie
import lottie from 'lottie-web';

// Swiper configuration objects
const swiperConfigs = {
  products: {
    modules: [Pagination],
    slidesPerView: 1.1,
    spaceBetween: 20,
    pagination: {
      el: '.js-products-carousel-pagination',
      clickable: true,
    },
    breakpoints: {
      768: {
        slidesPerView: 1.6,
      },
      1024: {
        slidesPerView: 3,
      },
    },
  },
  news: {
    modules: [Pagination, Navigation],
    slidesPerView: 1,
    spaceBetween: 20,
    navigation: {
      prevEl: '.js-news-prev',
      nextEl: '.js-news-next',
    },
    pagination: {
      el: '.js-news-carousel-pagination',
      clickable: true,
    },
    breakpoints: {
      1024: {
        slidesPerView: 3,
      },
    },
  },
  accordion: {
    modules: [Pagination],
    slidesPerView: 1.05,
    spaceBetween: 10,
    breakpoints: {
      768: {
        slidesPerView: 2.1,
      },
    },
  },
  partners: {
    modules: [Autoplay],
    centeredSlides: true,
    allowTouchMove: false,
    freeMode: true,
    loopedSlides: 15,
    speed: 11000,
    spaceBetween: 50,
    loop: true,
    slidesPerView: 'auto',
    autoplay: {
      delay: 1,
      disableOnInteraction: false,
    },
    breakpoints: {
      1024: {
        spaceBetween: 200,
      },
    },
  },
  investors: {
    modules: [Pagination, Navigation],
    slidesPerView: 1.1,
    spaceBetween: 20,
    navigation: {
      prevEl: '.js-investors-prev',
      nextEl: '.js-investors-next',
    },
    pagination: {
      el: '.js-investors-carousel-pagination',
      clickable: true,
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
      },
      1240: {
        slidesPerView: 3,
      },
    },
  },
  members: {
    modules: [Pagination],
    slidesPerView: 1,
    spaceBetween: 32,
    pagination: {
      el: '.js-members-carousel-pagination',
      clickable: true,
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
      },
    },
  },
  relatedPosts: {
    modules: [Pagination],
    slidesPerView: 1,
    spaceBetween: 32,
    pagination: {
      el: '.js-related-carousel-pagination',
      clickable: true,
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      },
    },
  },
  quotes: {
    modules: [Pagination],
    slidesPerView: 1,
    spaceBetween: 32,
    pagination: {
      el: '.js-quotes-carousel-pagination',
      clickable: true,
    },
  },
};

// Initialize Swiper instances
function initializeSwipers() {
  initSwiperWithDelay('.js-products-carousel', swiperConfigs.products);
  initSwiperWithDelay('.js-news-carousel', swiperConfigs.news);
  initSwiperWithDelay('.js-accordion-carousel', swiperConfigs.accordion);
  initSwiperWithDelay('.js-partners-carousel', swiperConfigs.partners);
  initSwiperWithDelay('.js-investors-carousel', swiperConfigs.investors);
  initSwiperWithDelay('.js-members-carousel', swiperConfigs.members);
  initSwiperWithDelay('.js-related-carousel', swiperConfigs.relatedPosts);
  initSwiperWithDelay('.js-quotes-carousel', swiperConfigs.quotes);
}

// Function to initialize a specific Swiper with optional delay
function initSwiperWithDelay(selector, config, delay = 100) {
  setTimeout(() => {
    const element = document.querySelector(selector);
    if (element) {
      return new Swiper(selector, config);
    }
  }, delay);
}

// Initialize Intersection Observer for animations
function initLottieAnimations() {
  const lottieElements = document.querySelectorAll('[data-lottie-animation]');
  const hoverElements = document.querySelectorAll('[data-lottie-hover]');
  const lottieInstances = new Map();
  const playedSet = new Set();

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const animation = lottieInstances.get(entry.target);
      if (!animation) return;

      if (entry.isIntersecting) {
        if (!playedSet.has(entry.target)) {
          animation.play();
          playedSet.add(entry.target);
        }
      }
    });
  }, {
    threshold: 0.5,
    rootMargin: '0px',
  });

  lottieElements.forEach((element) => {
    const rawData = element.getAttribute('data-lottie-animation');
    const shouldLoop = element.getAttribute('data-lottie-loop') === "true";

    if (!rawData) {
      console.error("No animation data found for element:", element);
      return;
    }

    let animationData;
    try {
      animationData = JSON.parse(rawData);
    } catch (error) {
      console.error("Error parsing JSON data:", error, rawData);
      return;
    }

    if (!animationData || !animationData.layers || !animationData.v) {
      console.error("Invalid animation data format:", animationData);
      return;
    }

    const animation = lottie.loadAnimation({
      container: element,
      renderer: 'svg',
      loop: shouldLoop,
      autoplay: false,
      animationData: animationData,
      rendererSettings: {
        preserveAspectRatio: 'xMidYMid meet',
      },
    });

    animation.addEventListener('DOMLoaded', () => {
      animation.goToAndStop(0, true);
    });

    lottieInstances.set(element, animation);
    observer.observe(element);
  });

  hoverElements.forEach((element) => {
    const animation = lottieInstances.get(element);
    if (!animation) return;

    const parent = element.parentElement;
    if (parent) {
      parent.addEventListener('mouseenter', () => {
        if (playedSet.has(element)) {
          animation.goToAndPlay(0, true); // Replay from the start
        }
      });
    }
  });

  return () => {
    lottieElements.forEach((element) => {
      observer.unobserve(element);
      const animation = lottieInstances.get(element);
      if (animation) {
        animation.destroy();
        lottieInstances.delete(element);
      }
    });
  };
}




function initMenu() {
  const nav = document.querySelector('.js-nav');
  const menuToggle = document.querySelectorAll('.js-nav-toggle');

  menuToggle.forEach((item) => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopImmediatePropagation();
      nav.classList.toggle('nav-open');
    });
  });

  const menuItems = document.querySelectorAll('.menu-item-has-children');

  menuItems.forEach((item) => {
    const subMenu = item.querySelector('.sub-menu');
    const parentLink = item.querySelector('.js-subnav-trigger');
    parentLink.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopImmediatePropagation();

      menuItems.forEach((otherItem) => {
        if (otherItem !== item) {
          const otherSubMenu = otherItem.querySelector('.sub-menu');
          const otherParentLink = otherItem.querySelector('a');
          otherSubMenu.classList.remove('sub-menu-open');
          otherParentLink.classList.remove('is-active');
        }
      });

      subMenu.classList.toggle('sub-menu-open');
      parentLink.classList.toggle('is-active');
    });
  });
}

function initLangSwitcher() {
  const nav = document.querySelector('.js-lang-switcher');
  const menuToggle = document.querySelector('.js-lang-switcher-toggle');

  if (!nav || !menuToggle) {
    return; 
  }
  
  menuToggle.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopImmediatePropagation();
    menuToggle.classList.toggle('lang-toggle-active'); 
    nav.classList.toggle('nav-open'); 
  });
  document.addEventListener('click', (e) => {
    if (!nav.contains(e.target) && !e.target.matches('.js-lang-switcher-toggle')) {
      nav.classList.remove('nav-open');
    }
  });
}

function initAccordionHorz() {
  const accordionsHorz = document.querySelectorAll('.js-accordion-horz');
  
  accordionsHorz.forEach((element) => {
    const items = element.querySelectorAll('.js-accordion-horz-item');
    const baseWidth = 100 / (items.length + 1) + '%';
    const expandedWidth = 2 * (100 / (items.length + 1)) + '%';

    const updateElementWidths = () => {
      items.forEach((item) => {
        const paragraph = item.querySelector('p');
        const containerWidth = element.offsetWidth;
        const expandedPixelWidth = (containerWidth * 1.7) / (items.length + 1);
      
        if (paragraph) paragraph.style.width = `${expandedPixelWidth}px`;
      });
    };

    items.forEach((item) => {
      item.style.width = baseWidth;
      item.classList.remove('active');
      
      item.addEventListener('transitionend', (e) => {
        if (e.propertyName === 'width' && item.classList.contains('active')) {
          const h3 = item.querySelector('h3');
          if (h3) {
            h3.classList.add('text-4xl');
            h3.classList.remove('body-3');
          }
        }
      });
    });

    if (items.length > 0) {
      const firstItem = items[0];
      firstItem.style.width = expandedWidth;
      firstItem.classList.add('active');

      const firstH3 = firstItem.querySelector('h3');
      if (firstH3) {
        firstH3.classList.add('text-4xl');
        firstH3.classList.remove('body-3');
      }
    }

    items.forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        items.forEach((el) => {
          el.style.width = baseWidth;
          el.classList.remove('active');

          const h3 = el.querySelector('h3');
          if (h3) {
            h3.classList.remove('text-4xl');
            h3.classList.add('body-3');
          }
        });
        
        item.style.width = expandedWidth;
        item.classList.add('active');
      });
    });

    updateElementWidths();

    const handleResize = debounce(() => {
      updateElementWidths();
    }, 50);

    window.addEventListener('resize', handleResize);
  });
}



function debounce(func, wait) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(this, args), wait);
  };
}

function onResize() {
  initAccordionHorz();
}

// function initSplitExcerptText() {
//   const excerptElement = document.querySelector('.js-excerpt');

//   if (excerptElement) {
//     const text = excerptElement.textContent.trim();
//     const parts = text.split('.').filter(part => part.trim());
//     const paragraphs = parts.map(part => `<p>${part.trim()}.</p>`).join('');
//     excerptElement.innerHTML = paragraphs;
//   }
// }

function initFAQ() {
  const questions = document.querySelectorAll('.js-accordion-faq h3');
  let openAnswer = null;

  questions.forEach((question) => {
    question.addEventListener('click', () => {
      const answer = question.nextElementSibling;

      if (openAnswer && openAnswer !== answer) {
        openAnswer.classList.remove('active');
        openAnswer.previousElementSibling.classList.remove('active');
      }

      question.classList.toggle('active');
      answer.classList.toggle('active');
      openAnswer = answer.classList.contains('active') ? answer : null;
    });
  });
}

function toggleHeaderClassOnScroll() {
  const header = document.querySelector('.main-header');

  if (!header) return;

  const scrolledClass = 'header-scrolled';

  let timeout;

  // Function to handle scroll events
  const onScroll = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      if (window.scrollY >= 20) {
        header.classList.add(scrolledClass);
      } else {
        header.classList.remove(scrolledClass);
      }
    }, 50); // Adjust the timeout as needed
  };

  // Function to add/remove scroll listener based on window size
  const manageScrollListener = () => {
    const isLargeScreen = window.innerWidth > 1024;

    if (isLargeScreen) {
      window.addEventListener('scroll', onScroll);
      onScroll(); // Ensure correct state on resize
    } else {
      window.removeEventListener('scroll', onScroll);
      header.classList.remove(scrolledClass); // Clean up class if resizing to small screen
    }
  };

  // Initial setup and add resize listener
  manageScrollListener();
  window.addEventListener('resize', manageScrollListener);
}


window.addEventListener('load', () => {
  if (window.location.hash) {
    const targetId = window.location.hash.substring(1);
    const targetElement = document.getElementById(targetId);

    if (targetElement) {
      setTimeout(() => {
        targetElement.scrollIntoView({ behavior: 'smooth' });
      }, 700); 
    }
  }
});

document.addEventListener('DOMContentLoaded', () => {
  initializeSwipers();
  initMenu();
  initLottieAnimations();
  initAccordionHorz();
  initFAQ();
  // initSplitExcerptText();
  toggleHeaderClassOnScroll();
  initLangSwitcher();
  window.addEventListener('resize', debounce(onResize, 200));
});
