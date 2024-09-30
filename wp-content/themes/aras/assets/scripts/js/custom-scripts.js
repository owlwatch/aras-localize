////Maintain Query Parameters
document.addEventListener("DOMContentLoaded", function () {
    var currentUrl = new URL(window.location.href);
    var params = new URLSearchParams(currentUrl.search);
    var utm = {};
    var found = false;

    // look for UTM parameters
    params.forEach((value, key) => {
        if (key.match(/^utm_/) && value) {
            var shortKey = key.replace(/^utm_/, '');
            utm[shortKey] = value;
            found = true;
        }
    });
    // save our updated UTM object to localStorage
    if (found) {
        window.localStorage.setItem('aras_utm', JSON.stringify(utm))
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
            
            fetch(url, {
                method: 'POST',
                body: new FormData(form),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.is_valid) {
                        try {
                            dataLayer.push({
                                event: 'mktoLead',
                                mktoFormId: form_id
                            });
                        }
                        catch (e) {
                            // no dataLayer
                        }
                    }
                    else {
                        console.log('Form did not validate');
                    }
                })
                .catch(error => {
                    console.error('Validate form request error:', error);

                })
                .finally(data => {
                    form.submit()
                });
        });
    });
});