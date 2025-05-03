// Import Swiper and required styles
import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import 'swiper/swiper-bundle.css';
import { Calendar } from '@fullcalendar/core'; // Make sure you import Calendar
import dayGridPlugin from '@fullcalendar/daygrid'; // Make sure you import the plugin
// Assuming Swiper is globally available or imported appropriately

document.addEventListener('DOMContentLoaded', function () {
  let calendarEl = document.getElementById('calendar');
  let eventDetailsEl = document.getElementById('event-details');
  if (!calendarEl || !eventDetailsEl) {
    console.error("Calendar or Event Details element not found!");
    return;
  }

  // Assuming window.eventsData is populated correctly beforehand
  const eventsData = window.eventsData || [];

  // Helper function to format event date as YYYY-MM-DD
  const formatEventDate = (dateInput) => {
    // Handles both Date objects and string dates that Date constructor can parse
    const eventDate = new Date(dateInput);
    if (isNaN(eventDate.getTime())) {
        // Handle invalid date input if necessary
        console.error("Invalid date encountered:", dateInput);
        return null; // Or return a default/error string
    }
    return eventDate.getFullYear() + '-' +
           String(eventDate.getMonth() + 1).padStart(2, '0') + '-' +
           String(eventDate.getDate()).padStart(2, '0');
  };

  // Create Swiper for displaying events (ensure Swiper library is loaded)
  const createSwiperForEvents = (events) => {
    // Clear previous content first
    eventDetailsEl.innerHTML = '';

    const swiperContainer = document.createElement('div');
    swiperContainer.classList.add('swiper', 'event-swiper'); // Add a specific class

    const swiperWrapper = document.createElement('div');
    swiperWrapper.classList.add('swiper-wrapper');

    events.forEach(event => {
      const swiperSlide = document.createElement('div');
      swiperSlide.classList.add('swiper-slide');
    
      const article = document.createElement('article');
      article.className = `tease h-full border border-white/30 p-6 tease-${event.post_type || 'default'}`;
      article.id = `tease-${event.id || ''}`;
    
      article.innerHTML = `
        <div class="flex flex-col h-full">
          <div class="flex flex-col space-y-5 grow">
            <h3 class="pb-6 border-b h5 border-white/50">
              <a href="${event.link || '#'}">${event.event_date || formatEventDate(event.date)}</a>
            </h3>
            <h3 class="h5">
              <a href="${event.link || '#'}">${event.title || 'Untitled'}</a>
            </h3>
            ${event.excerpt ? `
              <p class="text-base leading-[200%] text-white/80">
                ${event.excerpt}
              </p>` : ''}
            <a href="${event.link || '#'}" class="mt-5 btn-secondary">
              <span>Learn More</span>
              <svg class="ml-2" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.75 0.25L16 0.25C16.1989 0.25 16.3897 0.329018 16.5303 0.46967C16.671 0.610322 16.75 0.801088 16.75 1V12.25C16.75 12.6642 16.4142 13 16 13C15.5858 13 15.25 12.6642 15.25 12.25V2.81066L1.53033 16.5303C1.23744 16.8232 0.762563 16.8232 0.46967 16.5303C0.176777 16.2374 0.176777 15.7626 0.46967 15.4697L14.1893 1.75L4.75 1.75C4.33579 1.75 4 1.41421 4 1C4 0.585787 4.33579 0.25 4.75 0.25Z" fill="white"/>
              </svg>
            </a>
          </div>
        </div>
      `;
    
      swiperSlide.appendChild(article);
      swiperWrapper.appendChild(swiperSlide);
    });
    

    swiperContainer.appendChild(swiperWrapper);

    // Add navigation buttons if desired
    // const prevButton = document.createElement('div');
    // prevButton.className = 'swiper-button-prev';
    // swiperContainer.appendChild(prevButton);

    // const nextButton = document.createElement('div');
    // nextButton.className = 'swiper-button-next';
    // swiperContainer.appendChild(nextButton);

    eventDetailsEl.appendChild(swiperContainer);

    // Initialize Swiper
    // Ensure the Swiper instance isn't duplicated if called rapidly
    if (swiperContainer.swiper) {
        swiperContainer.swiper.destroy(true, true);
    }
    new Swiper(swiperContainer, {
        loop: false,
        spaceBetween: 30,
        slidesPerView: 1.1, // Adjust as needed
        // Add breakpoints if needed for responsiveness
        breakpoints: {
          // when window width is >= 640px
          1024: {
            spaceBetween: 60,
            slidesPerView: 1.6,
          },
        }
    });
  };


  // --- Prepare Events Data ---
  // We still need to group events by date for the button logic
  const eventsByDate = {};
  eventsData.forEach(event => {
    const dateStr = formatEventDate(event.date);
    if(dateStr) { // Only process if date is valid
        if (!eventsByDate[dateStr]) {
          eventsByDate[dateStr] = [];
        }
        // Store the original event object
        eventsByDate[dateStr].push(event);
    }
  });

  // --- Initialize FullCalendar ---
  let calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin],
    initialView: 'dayGridMonth',
    height: 'auto',
    // events: formattedEvents, // REMOVE this - we are not rendering events directly
    // eventColor: 'transparent', // REMOVE - No longer needed
    showNonCurrentDates: false,
    fixedWeekCount: false,
    dayHeaders: false,
    // eventBorderColor: 'white', // REMOVE - No longer needed
    // eventTextColor: 'white', // REMOVE - No longer needed
    headerToolbar: {
      left: 'prev',
      center: 'title',
      right: 'next'
    },

    // Use dayCellDidMount to add the button *after* the cell is rendered
    dayCellDidMount: function(info) {
      // Skip adding buttons to "other month" cells if they are visible
      if (info.isOther) {
          return;
      }

      const dateStr = formatEventDate(info.date);
      const eventsForDay = eventsByDate[dateStr] || [];

      // Only add a button if there are events for this day
      if (eventsForDay.length > 0) {
        // Check if button already exists (extra safety, though dayCellDidMount should be reliable)
        if (!info.el.querySelector('.view-events-button')) {
            const buttonEl = document.createElement('button');
            buttonEl.textContent = ``;
            buttonEl.className = 'view-events-button'; // Add your styling class

            // Button click event
            buttonEl.addEventListener('click', function(e) {
              e.stopPropagation(); // Prevent potential calendar dateClick triggers
              eventDetailsEl.innerHTML = ''; // Clear previous content
              createSwiperForEvents(eventsForDay); // Show events in swiper
            });

            // Append the button to the day cell's content area
            // Finding the right place might need inspection of FullCalendar's generated HTML
            // '.fc-daygrid-day-events' or '.fc-daygrid-day-top' might be suitable targets
            // Appending directly to info.el (the <td>) is also an option
            const dayTopContainer = info.el.querySelector('.fc-daygrid-day-top');
            if (dayTopContainer) {
                 // Append near the date number
                 dayTopContainer.appendChild(buttonEl);
            } else {
                 // Fallback: append directly to the cell
                 info.el.appendChild(buttonEl);
            }

            // Optionally add a class to the cell itself for styling
            info.el.classList.add('has-events-button');
        }
      }
    },
  });

  calendar.render();

  // --- Auto show today's events ---
  const today = new Date();
  const todayString = formatEventDate(today);
  const todayEvents = eventsByDate[todayString] || [];
  if (todayEvents.length > 0) {
    createSwiperForEvents(todayEvents);
  } else {
    eventDetailsEl.innerHTML = '<p>No events today</p>';
  }
});

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
        spaceBetween: 70,
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
    slidesPerView: 1.2,
    spaceBetween: 24,
    pagination: {
      el: '.js-quotes-carousel-pagination',
      clickable: true,
    },
    breakpoints: {
      768: {
        slidesPerView: 2.4,
      },
      1024: {
        slidesPerView: 2,
      },
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
  const nav = document.querySelector('.js-nav-mobile');
  const menuToggle = document.querySelectorAll('.js-nav-toggle');
  const body = document.html;

  function isMobile() {
    return window.matchMedia('(max-width: 767px)').matches; // Adjust breakpoint as needed
  }

  menuToggle.forEach((item) => {
    item.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopImmediatePropagation();
      
      nav.classList.toggle('nav-open');
      
      if (isMobile()) {
        body.classList.toggle('overflow-hidden', nav.classList.contains('nav-open'));
      }
    });
  });

  const menuItems = document.querySelectorAll('.menu-item-has-children');

  menuItems.forEach((item) => {
    const subMenu = item.querySelector('.sub-menu');
    const parentLink = item.querySelector('.js-subnav-trigger');

    if (parentLink) {
      parentLink.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopImmediatePropagation();

        // Close all other open submenus first
        menuItems.forEach((otherItem) => {
          if (otherItem !== item) {
            const otherSubMenu = otherItem.querySelector('.sub-menu');
            const otherParentLink = otherItem.querySelector('.js-subnav-trigger');
            if (otherSubMenu.classList.contains('sub-menu-open')) {
              otherSubMenu.classList.remove('sub-menu-open');
              otherParentLink.classList.remove('is-active');
            }
          }
        });

        // Toggle the current submenu
        subMenu.classList.toggle('sub-menu-open');
        parentLink.classList.toggle('is-active');
      });
    }
  });

    // Close menu when clicking outside
  document.addEventListener('click', (e) => {
    // Check if nav exists and contains the target
    if (!nav || !nav.contains(e.target)) return;

    // Ensure menuItems exists and is not empty
    if (!menuItems || menuItems.length === 0) return;

    menuItems.forEach((item) => {
      // Skip iteration if item is null
      if (!item) return;

      const subMenu = item.querySelector('.sub-menu');
      const parentLink = item.querySelector('.js-subnav-trigger');

      // Only remove classes if elements exist
      if (subMenu) {
        subMenu.classList.remove('sub-menu-open');
      }

      if (parentLink) {
        parentLink.classList.remove('is-active');
      }
    });

    // Check if isMobile function exists and body exists
    if (typeof isMobile === 'function' && isMobile() && body) {
      body.classList.remove('overflow-hidden');
    }
  });
}

document.addEventListener('DOMContentLoaded', initMenu);


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
            // h3.classList.add('text-4xl');
            // h3.classList.remove('body-3');
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
        // firstH3.classList.add('text-4xl');
        // firstH3.classList.remove('body-3');
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
            // h3.classList.remove('text-4xl');
            // h3.classList.add('body-3');
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
