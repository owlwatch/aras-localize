////Maintain Query Parameters
document.addEventListener("DOMContentLoaded", function () {
    var currentUrl = new URL(window.location.href);
    var params = new URLSearchParams(currentUrl.search);
    var utm = {};
    var raid = {};
    var gclid = '';
    var found = false;
    var raidFound = false;

    // look for UTM parameters
    params.forEach((value, key) => {
        if (key.match(/^utm_/) && value) {
            var shortKey = key.replace(/^utm_/, '');
            utm[shortKey] = value;
            found = true;
        }
        if( key.match(/^(gb|wb)raid$/) && value ) {
            raid[key] = value;
            raidFound = true;
        };
        if( key.match(/gclid/) && value ) {
            gclid = value;
        }
    });
    // save our updated UTM object to localStorage
    if (found) {
        window.localStorage.setItem('aras_utm', JSON.stringify(utm))
    }
    if( raidFound ){
        window.localStorage.setItem('aras_raid', JSON.stringify(raid))
    }

    // look for gclid
    if( gclid ){
        window.localStorage.setItem('aras_gclid', gclid);
    }
});

////Eyebrow Logic
document.addEventListener("DOMContentLoaded", function () {
    const eyebrow = document.querySelector('.eyebrow');
    if (!eyebrow) {
        return false;
    }
    const close = eyebrow.querySelector('.eyebrow__close');

    function closeEyebrow() {
        eyebrow.classList.add('is-closed');
        sessionStorage.setItem('eyebrow-closed', '1');
    }
    if (close) {
        close.addEventListener('click', closeEyebrow);
    }
});

//// aras-header-height
document.addEventListener("DOMContentLoaded", function () {
    // const header = document.querySelector('header.header');
    // if (!header) {
    //     return false;
    // }
    // const updateCssVariable = () => {
    //     const rect = header.getBoundingClientRect();
    //     console.log( rect.height );
    //     document.documentElement.style.setProperty("--aras-header-height", rect.height + 'px' );
    // }

    // const observer = new ResizeObserver( updateCssVariable );
    // observer.observe( header );
});

////Smooth Scrolling
jQuery(document).ready(function () {
    // Check window height
    if (jQuery(window).height() >= 600) {
        // Function to handle scrolling to target element
        function scrollToTarget(target) {
            if (target.length) {
                // Offset scroll position to account for fixed header
                var offsetTop = target.offset().top - jQuery(".header").outerHeight();
                //   jQuery('html, body').stop().animate({
                //       scrollTop: offsetTop
                //   }, 600);
            }
        }
        // Check if there's an anchor link in the URL
        var hash = window.location.hash;
        if (hash) {
            //   scrollToTarget(jQuery(hash));
        }
        //   // Handle click events on anchor links by delegating to a parent element
        //   jQuery(document).on('click', 'a[href*="#"]', function(event) {
        //       var href = jQuery(this).attr('href');
        //       var hashIndex = href.indexOf('#');
        //       if (hashIndex !== -1) {
        //           var hash = href.slice(hashIndex);
        //           var target = jQuery(hash);
        //           console.log('Hash:', hash);
        //           console.log('Target:', target);
        //           scrollToTarget(target);
        //           // Prevent default behavior of anchor links
        //           event.preventDefault();
        //       }
        //   });
    }
});


////Copy function for <pre>
window.addEventListener("DOMContentLoaded", function () {
    var preElements = document.getElementsByTagName("pre");
    if (preElements && preElements.length > 0) {
        for (var i = 0; i < preElements.length; i++) {
            var preElement = preElements[i];
            var spanElement = document.createElement("span");
            spanElement.classList.add("copy-container");

            var buttonElement = document.createElement("button");
            buttonElement.textContent = "Copy Snippet";
            buttonElement.classList.add("copy-button");
            buttonElement.addEventListener("click", createCopyTextHandler(preElement));

            spanElement.appendChild(preElement.cloneNode(true));
            spanElement.appendChild(buttonElement);
            preElement.parentNode.replaceChild(spanElement, preElement);
        }
    }
});
function createCopyTextHandler(element) {
    return function () {
        var text = element.textContent;
        var tempInput = document.createElement("textarea");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
    };
}


//// Language dropdown functionality
var buttons = document.querySelectorAll('.language-dropdown-button');
var dropdownPanes = document.querySelectorAll('.language-dropdown-container');
buttons.forEach(function (button, index) {
    button.addEventListener('click', function () {
        if (dropdownPanes[index].classList.contains('is-open')) {
            dropdownPanes[index].classList.remove('is-open');
        } else {
            dropdownPanes.forEach(function (pane) {
                pane.classList.remove('is-open');
            });
            dropdownPanes[index].classList.add('is-open');
        }
    });
});

//// Skip link functionality for ADA compliancy
document.addEventListener("DOMContentLoaded", function () {
    var skipLink = document.getElementById("skip-link");
    var targetSection = document.getElementById(skipLink.getAttribute("href").substring(1)); // Remove '#' from href to get section ID

    skipLink.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default behavior of the link

        // Change focus to the target section
        targetSection.tabIndex = -1;
        targetSection.focus();
        targetSection.tabIndex = 0; // Restore tabindex to make section focusable by default

        // Optionally, you can visually highlight the target section for better accessibility
        targetSection.style.outline = "2px solid blue";
        setTimeout(function () {
            targetSection.style.outline = ""; // Remove outline after a short delay
        }, 2000); // Adjust the delay as needed
    });
});

document.addEventListener("DOMContentLoaded", function () {

    function get_gacid() {
        try {
            return ga.getAll()[0].get('clientId');
        }
        catch (e) {
            return 'n/a';
        }
    }

    const forms = document.querySelectorAll('form[data-marketo-id]');
    if (forms) forms.forEach(form => {
        form.addEventListener('submit', e => {

            // we are going to handle some submission...
            e.preventDefault();

            // populate the form
            const input = form.querySelector('[data-field-name="gacid"] input');
            if (input) {
                input.value = get_gacid();
            }

            const form_id = form.getAttribute('data-marketo-id');
            const url = form.getAttribute('data-validation-endpoint');
            
            let valid = false;
            fetch(url, {
                method: 'POST',
                body: new FormData(form),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.is_valid) {
                        valid = true;
                        try {

                            dataLayer.push({
                                event: 'mktoLead',
                                mktoFormId: form_id
                            });

                            // give it a second to track
                            // setTimeout( () => form.submit(), 500 );
                        }
                        catch (e) {
                            // no dataLayer
                        }
                    }
                    else {
                        // console.log('Form did not validate');
                    }
                })
                .catch(error => {
                    // console.error('Validate form request error:', error);
                })
                .finally(data => {
                    
                    setTimeout( () => form.submit(), valid ? 500 : 0 );
                });
        });
    });
});

// add a window resize observer to the header.header and update a root css variable '--aras-header-height'
// on domcontentloaded

document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector('.header');
    if (header) {
        const updateHeaderHeight = () => {
            const headerHeight = header.offsetHeight;
            document.documentElement.style.setProperty('--aras-header-height', `${headerHeight}px`);
        };
        // Initial call to set the variable
        updateHeaderHeight();
        // Add resize observer
        window.addEventListener('resize', updateHeaderHeight);
    }
});

// lets add an option to zoom images
document.addEventListener("DOMContentLoaded", async () => {
    const images = document.querySelectorAll('.zoomable-image>img, a[href$=".jpg"]>img[class*="wp-image-"], a[href$=".jpeg"]>img[class*="wp-image-"], a[href$=".png"] img[class*="wp-image-"], a[href$=".gif"] img[class*="wp-image-"], a[href$=".webm"] img[class*="wp-image-"]');
    if (images && images.length > 0) {
        // filter the images to the links
        const filteredImages = Array.from(images).map(image => {
            const anchor = image.closest('a');
            if( !anchor.getAttribute('data-pswp-width') ){
                // we actually want to set the width and height based on the real size
                // so we will pre-load the image to get its natural dimensions
                const img = new Image();
                img.src = anchor.href;
                img.onload = () => {
                    anchor.setAttribute('data-pswp-height', img.naturalHeight);
                    anchor.setAttribute('data-pswp-width', img.naturalWidth);
                };
            }
            return anchor;
        });
        // first lets load the photoswipe library and css
        const photoswipe = await import('photoswipe/lightbox');
        await import('photoswipe/style.css');
        const lightbox = new PhotoSwipeLightbox({
            gallery: 'body',
            children: filteredImages,
            pswpModule: () => import('photoswipe')
        });
        
        lightbox.init();
    }
});

// look for .quote-carousel and initialize swiper if it exists
document.addEventListener("DOMContentLoaded", async () => {
    const quoteCarousel = document.querySelector('.quote-carousel');
    if (quoteCarousel) {
        // load swiper
        const { Swiper } = await import('swiper');
        const { Navigation, Pagination, Autoplay } = await import('swiper/modules');
        
        // also need to import the swiper css
        await import('swiper/css');
        await import('swiper/css/pagination');
        await import('swiper/css/navigation');
        
        // initialize swiper
        new Swiper(quoteCarousel, {
            modules:[Navigation, Pagination, Autoplay],
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,

            autoplay: {
                delay: 5000,
                disableOnInteraction: true,
                pauseOnMouseEnter: true
            },

            // If we need pagination
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },

            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }
});
