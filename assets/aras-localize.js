(function () {
  const settings = window.ArasLocalize || {};
  const selector = settings.selector || '.aras-localize-switcher';
  const maxRetries = 20;
  const retryDelay = 250;

  function formatCode(code) {
    if (!code) return '--';
    return code.split('-')[0].toUpperCase();
  }

  function normalizeLanguages(languages) {
    if (!Array.isArray(languages)) return [];
    return languages.filter((lang) => lang && lang.code && lang.name);
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

  function renderSwitcher(container, languages, currentLang) {
    const wrapper = document.createElement('div');
    wrapper.className = 'aras-localize';

    const toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'aras-localize__toggle';
    toggle.setAttribute('aria-haspopup', 'listbox');
    toggle.setAttribute('aria-expanded', 'false');

    const label = document.createElement('span');
    label.className = 'aras-localize__code';
    label.textContent = formatCode(currentLang);

    const chevron = document.createElement('span');
    chevron.className = 'aras-localize__chevron';

    toggle.appendChild(label);
    toggle.appendChild(chevron);

    const menu = document.createElement('ul');
    menu.className = 'aras-localize__menu';
    menu.setAttribute('role', 'listbox');

    languages.forEach((lang) => {
      const item = document.createElement('li');
      item.className = 'aras-localize__item';
      item.setAttribute('role', 'option');
      item.setAttribute('aria-selected', lang.code === currentLang ? 'true' : 'false');

      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'aras-localize__option';
      button.textContent = lang.name;
      button.setAttribute('data-lang', lang.code);
      if (lang.code === currentLang) {
        button.classList.add('is-active');
      }

      button.addEventListener('click', () => {
        if (window.Localize && typeof window.Localize.setLanguage === 'function') {
          window.Localize.setLanguage(lang.code);
        }
        wrapper.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });

      item.appendChild(button);
      menu.appendChild(item);
    });

    toggle.addEventListener('click', () => {
      const isOpen = wrapper.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      if (isOpen) {
        closeAll(wrapper);
      }
    });

    wrapper.appendChild(toggle);
    wrapper.appendChild(menu);

    container.innerHTML = '';
    container.appendChild(wrapper);
  }

  function updateCurrentLanguage(code) {
    document.querySelectorAll('.aras-localize').forEach((wrapper) => {
      const label = wrapper.querySelector('.aras-localize__code');
      const options = wrapper.querySelectorAll('.aras-localize__option');
      if (label) label.textContent = formatCode(code);
      options.forEach((option) => {
        const isActive = option.getAttribute('data-lang') === code;
        option.classList.toggle('is-active', isActive);
        const item = option.closest('.aras-localize__item');
        if (item) item.setAttribute('aria-selected', isActive ? 'true' : 'false');
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

  function init(retryCount) {
    const containers = document.querySelectorAll(selector);
    if (!containers.length) return;

    if (!window.Localize || typeof window.Localize.getAvailableLanguages !== 'function') {
      if (retryCount < maxRetries) {
        setTimeout(() => init(retryCount + 1), retryDelay);
      }
      return;
    }

    if (typeof window.Localize.hideWidget === 'function') {
      window.Localize.hideWidget();
    }

    window.Localize.getAvailableLanguages(function (err, languages) {
      if (err) return;

      const normalized = normalizeLanguages(languages);
      if (!normalized.length) return;

      let current = null;
      if (typeof window.Localize.getLanguage === 'function') {
        current = window.Localize.getLanguage();
      }
      if (!current && typeof window.Localize.getSourceLanguage === 'function') {
        current = window.Localize.getSourceLanguage();
      }
      if (!current && normalized[0]) {
        current = normalized[0].code;
      }

      containers.forEach((container) => {
        renderSwitcher(container, normalized, current);
      });

      attachGlobalHandlers();

      if (typeof window.Localize.on === 'function') {
        window.Localize.on('setLanguage', function (data) {
          if (data && data.to) {
            updateCurrentLanguage(data.to);
          }
        });
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => init(0));
  } else {
    init(0);
  }
})();
