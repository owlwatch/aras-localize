(function () {
  const settings = window.ArasLocalize || {};
  const selector = settings.selector || '.aras-localize-switcher';
  const availableLanguages = Array.isArray(settings.availableLanguages)
    ? settings.availableLanguages.filter(Boolean)
    : [];
  const sourceLanguage = settings.sourceLanguage || 'en';

  function formatCode(code) {
    if (!code) return '--';
    return code.split('-')[0].toUpperCase();
  }

  function closeAll(except) {
    document.querySelectorAll('.aras-localize.is-open').forEach((node) => {
      if (node !== except) {
        node.classList.remove('is-open');
        const toggle = node.querySelector('.aras-localize__toggle');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  function updateCurrentLanguage(code) {
    document.querySelectorAll(selector).forEach((container) => {
      container.setAttribute('data-current-lang', code);

      const wrapper = container.querySelector('.aras-localize');
      const label = container.querySelector('.aras-localize__code');
      const options = container.querySelectorAll('.aras-localize__option');

      if (label) {
        label.textContent = formatCode(code);
      }

      options.forEach((option) => {
        const isActive = option.getAttribute('data-lang') === code;
        option.classList.toggle('is-active', isActive);

        const item = option.closest('.aras-localize__item');
        if (item) {
          item.setAttribute('aria-selected', isActive ? 'true' : 'false');
        }
      });

      if (wrapper) {
        wrapper.classList.remove('is-open');
      }

      const toggle = container.querySelector('.aras-localize__toggle');
      if (toggle) {
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  function applyLanguage(option) {
    const code = option.getAttribute('data-lang');
    const url = option.getAttribute('data-url') || option.getAttribute('href');

    if (!code || !url) {
      return;
    }

    if (window.history && typeof window.history.replaceState === 'function') {
      window.history.replaceState({}, '', url);
    }

    if (window.Localize && typeof window.Localize.setLanguage === 'function') {
      window.Localize.setLanguage(code);
    }

    localStorage.setItem('loadedLang', code);
  }

  function attachSwitcher(container) {
    const wrapper = container.querySelector('.aras-localize');
    const toggle = container.querySelector('.aras-localize__toggle');
    const options = container.querySelectorAll('.aras-localize__option');

    if (!wrapper || !toggle || !options.length) {
      return;
    }

    toggle.addEventListener('click', () => {
      const isOpen = wrapper.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

      if (isOpen) {
        closeAll(wrapper);
      }
    });

    options.forEach((option) => {
      option.addEventListener('click', (event) => {
        if (!window.Localize || typeof window.Localize.setLanguage !== 'function') {
          return;
        }
        event.stopPropagation();
        event.preventDefault();
        applyLanguage(option);
      });
    });
  }

  function attachGlobalHandlers() {
    document.addEventListener('click', (event) => {
      if (!event.target.closest('.aras-localize')) {
        closeAll(null);
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeAll(null);
      }
    });
  }

  function getUrlWithLanguage(input, lang, knownLanguages, sourceLang) {
    const url = input instanceof URL ? new URL(input.href) : new URL(input, window.location.origin);
    
    const languages = Array.from(
      new Set([...(Array.isArray(knownLanguages) ? knownLanguages : []), sourceLang].filter(Boolean)),
    );

    const targetLanguage = lang || sourceLang;
    const pathSegments = url.pathname.split('/').filter(Boolean);
    const hadTrailingSlash = url.pathname.length > 1 && url.pathname.endsWith('/');
    const currentPathSegments = window.location.pathname.split('/').filter(Boolean);
    const hasExplicitSourceLanguage =
      (pathSegments.length && pathSegments[0] === sourceLang) ||
      (currentPathSegments.length && currentPathSegments[0] === sourceLang);

    if (pathSegments.length && languages.includes(pathSegments[0])) {
      pathSegments.shift();
    }

    if (targetLanguage && (targetLanguage !== sourceLang || hasExplicitSourceLanguage)) {
      pathSegments.unshift(targetLanguage);
    }

    let pathname = '/' + pathSegments.join('/');
    if (pathname !== '/' && hadTrailingSlash) {
      pathname += '/';
    }

    url.pathname = pathname;

    return url.toString();
  }

  window.getUrlWithLanguage = getUrlWithLanguage;

  function updateLinks() {
    const EXCLUDED_PATHS = ['/wp-admin', '/wp-login', '/wp-content', '/wp-includes'];
    const currentLang = OVERRIDE_LANG || localStorage.getItem('loadedLang') || sourceLanguage;

    
    const links = document.querySelectorAll('a:not([data-localize-ignore])');
    links.forEach((link) => {
      if (link.href) {
        let url = new URL(link.href);
        if (url.hostname === document.domain) {
          if (url.pathname) {
            for (let i = 0; i < EXCLUDED_PATHS.length; i++) {
              if (url.pathname.includes(EXCLUDED_PATHS[i])) {
                return;
              }
            }
            link.href = getUrlWithLanguage(url, currentLang, availableLanguages, sourceLanguage);
          }
        }
      }
    });
  }

  function init() {
    const containers = document.querySelectorAll(selector);
    if (containers.length ){

      if (window.Localize && typeof window.Localize.hideWidget === 'function') {
        window.Localize.hideWidget();
      }

      containers.forEach((container) => {
        attachSwitcher(container);
      });
    }

    attachGlobalHandlers();


    if (window.Localize && typeof window.Localize.on === 'function') {
      setTimeout(() => {
        // we also want to remove the default listener to setLanguage
        if (typeof window.Localize.off === 'function') {
          window.Localize.off('setLanguage');
        }
        window.Localize.on('setLanguage', function (data) {
          console.log('setLanguage event received:', data);
          if (data && data.to) {
            updateCurrentLanguage(data.to);
            // we need to implement our own link replacing logic
            updateLinks();
          }
        });
        updateLinks();
      }, 100);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
