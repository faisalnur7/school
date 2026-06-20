import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const THEME_STORAGE_KEY = 'school-theme';

function normalizeTheme(theme) {
    return theme === 'dark' ? 'dark' : 'light';
}

function getPreferredTheme() {
    try {
        const storedTheme = window.localStorage.getItem(THEME_STORAGE_KEY);
        if (storedTheme) {
            return normalizeTheme(storedTheme);
        }
    } catch (error) {
        // Ignore storage errors and fall back to system preference.
    }

    return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
        ? 'dark'
        : 'light';
}

function applyTheme(theme) {
    const nextTheme = normalizeTheme(theme);
    const root = document.documentElement;

    root.dataset.theme = nextTheme;
    root.classList.toggle('dark', nextTheme === 'dark');
    root.style.colorScheme = nextTheme;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        updateThemeToggleButton(button, nextTheme);
    });
}

function updateThemeToggleButton(button, activeTheme) {
    const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';
    const nextThemeLabel = nextTheme === 'dark' ? 'Dark' : 'Light';

    button.setAttribute('aria-pressed', activeTheme === 'dark' ? 'true' : 'false');
    button.setAttribute('aria-label', `Switch to ${nextThemeLabel.toLowerCase()} mode`);
    button.setAttribute('title', `Switch to ${nextThemeLabel.toLowerCase()} mode`);
    button.dataset.activeTheme = activeTheme;
    button.dataset.nextTheme = nextTheme;

    const label = button.querySelector('[data-theme-toggle-label]');

    if (label) {
        label.textContent = nextThemeLabel;
    }
}

function setTheme(theme, persist = true) {
    const nextTheme = normalizeTheme(theme);

    if (persist) {
        try {
            window.localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
        } catch (error) {
            // Ignore storage errors and still apply the requested theme.
        }
    }

    applyTheme(nextTheme);
}

window.setThemeMode = setTheme;
window.toggleThemeMode = function toggleThemeMode() {
    const currentTheme = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
    setTheme(currentTheme === 'dark' ? 'light' : 'dark');
};

function syncThemeButtons() {
    const currentTheme = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        updateThemeToggleButton(button, currentTheme);
    });
}

applyTheme(getPreferredTheme());
syncThemeButtons();

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-theme-toggle]');

    if (!toggle) {
        return;
    }

    event.preventDefault();
    window.toggleThemeMode();
});

const colorSchemeQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

if (colorSchemeQuery && colorSchemeQuery.addEventListener) {
    colorSchemeQuery.addEventListener('change', (event) => {
        try {
            if (!window.localStorage.getItem(THEME_STORAGE_KEY)) {
                setTheme(event.matches ? 'dark' : 'light', false);
            }
        } catch (error) {
            setTheme(event.matches ? 'dark' : 'light', false);
        }
    });
}
