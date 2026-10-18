import React, { createContext, useContext, useState, useEffect, useMemo } from 'react';
import { useColorScheme } from 'react-native';
import storage from '../services/storage';
import { THEMES } from '../constants/theme';

const THEME_STORAGE_KEY = '@eleve_theme_mode';

const ThemeContext = createContext({
  theme: 'light',
  themeMode: 'system',
  isDark: false,
  colors: THEMES.light,
  toggleTheme: () => {},
  setThemeMode: () => {},
});

export const ThemeProvider = ({ children }) => {
  const systemColorScheme = useColorScheme();
  const [themeMode, setThemeModeState] = useState('system'); // 'light' | 'dark' | 'system'
  const [isLoaded, setIsLoaded] = useState(false);

  // Load saved preference from storage on mount
  useEffect(() => {
    async function loadSavedTheme() {
      try {
        const savedMode = await storage.getItem(THEME_STORAGE_KEY);
        if (savedMode && ['light', 'dark', 'system'].includes(savedMode)) {
          setThemeModeState(savedMode);
        }
      } catch (err) {
        console.warn('Failed to load theme preference:', err);
      } finally {
        setIsLoaded(true);
      }
    }
    loadSavedTheme();
  }, []);

  const setThemeMode = async (mode) => {
    if (!['light', 'dark', 'system'].includes(mode)) return;
    setThemeModeState(mode);
    try {
      await storage.setItem(THEME_STORAGE_KEY, mode);
    } catch (err) {
      console.warn('Failed to save theme preference:', err);
    }
  };

  const toggleTheme = () => {
    const nextTheme = resolvedTheme === 'dark' ? 'light' : 'dark';
    setThemeMode(nextTheme);
  };

  // Resolve active theme based on user preference or system
  const resolvedTheme = useMemo(() => {
    if (themeMode === 'system') {
      return systemColorScheme === 'dark' ? 'dark' : 'light';
    }
    return themeMode;
  }, [themeMode, systemColorScheme]);

  const isDark = resolvedTheme === 'dark';
  const colors = THEMES[resolvedTheme] || THEMES.light;

  const value = useMemo(() => ({
    theme: resolvedTheme,
    themeMode,
    isDark,
    colors,
    toggleTheme,
    setThemeMode,
  }), [resolvedTheme, themeMode, isDark, colors]);

  return (
    <ThemeContext.Provider value={value}>
      {children}
    </ThemeContext.Provider>
  );
};

export const useTheme = () => {
  const context = useContext(ThemeContext);
  if (!context) {
    throw new Error('useTheme must be used within a ThemeProvider');
  }
  return context;
};

export default ThemeContext;
