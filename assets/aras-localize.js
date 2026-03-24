(function () {
  const settings = window.ArasLocalize || {};
  const selector = settings.selector || '.aras-localize-switcher';

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

  function init() {
    const containers = document.querySelectorAll(selector);
    if (!containers.length) return;

    if (window.Localize && typeof window.Localize.hideWidget === 'function') {
      window.Localize.hideWidget();
      // we also want to remove the default listener to setLanguage
      if (typeof window.Localize.off === 'function') {
        window.Localize.off('setLanguage');
      }
    }

    containers.forEach((container) => {
      attachSwitcher(container);
    });

    attachGlobalHandlers();

    if (window.Localize && typeof window.Localize.on === 'function') {
      window.Localize.on('setLanguage', function (data) {
        if (data && data.to) {
          updateCurrentLanguage(data.to);
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
