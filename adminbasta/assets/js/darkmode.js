const darkMode = document.querySelector('.dark-mode');

document.addEventListener("DOMContentLoaded", function () {
    // Retrieve dark mode state from localStorage
    const isDarkMode = localStorage.getItem('darkMode') === 'true';

    // Apply dark mode if it was enabled
    if (isDarkMode) {
        document.body.classList.add('dark-mode-variables');
        darkMode.querySelector('span:nth-child(1)').classList.add('active');
        darkMode.querySelector('span:nth-child(2)').classList.remove('active');
    }

    // Update the dark mode icon based on the stored state
    const updateDarkModeIcon = () => {
        const isDarkModeEnabled = document.body.classList.contains('dark-mode-variables');
        const activeIcon = isDarkModeEnabled ? 'span:nth-child(1)' : 'span:nth-child(2)';
        const inactiveIcon = isDarkModeEnabled ? 'span:nth-child(2)' : 'span:nth-child(1)';

        // Remove active class from inactive icon
        darkMode.querySelector(inactiveIcon).classList.remove('active');

        // Add active class to active icon
        darkMode.querySelector(activeIcon).classList.add('active');
    };

    darkMode.addEventListener('click', (event) => {
        // Toggle dark mode class on the body
        document.body.classList.toggle('dark-mode-variables');

        // Update the dark mode icon
        updateDarkModeIcon();

        // Store the dark mode state in localStorage
        const isDarkModeEnabled = document.body.classList.contains('dark-mode-variables');
        localStorage.setItem('darkMode', isDarkModeEnabled.toString());
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;
    const html = document.documentElement; // Select the html element

    const applyDarkMode = () => {
        const isDarkMode = body.classList.contains('dark-mode');

        if (isDarkMode) {
            body.classList.add('dark-mode-variables');
        }
    };

    const toggleDarkMode = () => {
        // Toggle dark mode class and update localStorage
        body.classList.toggle('dark-mode');
        const isDarkMode = body.classList.contains('dark-mode');
        localStorage.setItem('darkMode', isDarkMode.toString());
    };

    const isDarkMode = localStorage.getItem('darkMode') === 'true';

    if (isDarkMode) {
        // Make sure to add the 'dark-mode' class if dark mode is enabled
        body.classList.add('dark-mode');
    }

    darkModeToggle.addEventListener('click', toggleDarkMode);
});
