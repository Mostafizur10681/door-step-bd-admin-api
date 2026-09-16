import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import flatpickr from 'flatpickr';

window.Alpine = Alpine;
window.Chart = Chart;
window.flatpickr = flatpickr;

// Global theme helper
window.toggleDarkMode = function (theme) {
  const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
  
  if (isDark) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
  
  localStorage.setItem('theme', theme);
  window.dispatchEvent(new CustomEvent('themechanged', { detail: { theme, isDark } }));
};

// Helper for Chart.js theme colors
window.getChartThemeColors = function () {
  const isDark = document.documentElement.classList.contains('dark');
  return {
    isDark,
    textColor: isDark ? '#9CA3AF' : '#64748B',
    gridColor: isDark ? 'rgba(55, 65, 81, 0.4)' : 'rgba(226, 232, 240, 0.8)',
    tooltipBg: isDark ? '#1F2937' : '#0F172A',
    tooltipText: '#FFFFFF',
    primaryColor: '#10B981',
    primaryFill: isDark ? 'rgba(16, 185, 129, 0.15)' : 'rgba(16, 185, 129, 0.1)',
  };
};

// Listen for system theme changes if in 'system' mode
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
  const currentTheme = localStorage.getItem('theme') || 'light';
  if (currentTheme === 'system') {
    window.toggleDarkMode('system');
  }
});

// Auto apply theme on load
const savedTheme = localStorage.getItem('theme') || 'light';
window.toggleDarkMode(savedTheme);

Alpine.start();
