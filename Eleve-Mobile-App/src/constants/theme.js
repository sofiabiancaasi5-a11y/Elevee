export const COLORS = {
  obsidian: '#171717',
  charcoal: '#262626',
  ivory: '#F7F5F0',
  ivoryLight: '#FFFFFF',
  sand: '#D8D0C4',
  sandLight: '#E8E2D9',
  champagne: '#C5A880',
  champagneLight: '#F3EDE4',
  accent: '#A67C52',
  textPrimary: '#171717',
  textSecondary: '#66615B',
  textMuted: '#9E988F',
  border: '#E2DCD5',
  cardBg: '#FFFFFF',
  success: '#1B8755',
  warning: '#D97706',
  error: '#DC2626',
};

export const THEMES = {
  light: {
    name: 'light',
    isDark: false,
    background: '#F7F5F0',
    backgroundSubtle: '#F0ECE4',
    surface: '#FFFFFF',
    surfaceSubtle: '#F5F2EB',
    card: '#FFFFFF',
    cardBorder: 'rgba(226, 220, 213, 0.85)',
    textPrimary: '#171717',
    textSecondary: '#66615B',
    textMuted: '#9E988F',
    border: '#E2DCD5',
    borderSubtle: '#F0ECE4',
    accent: '#A67C52',
    champagne: '#C5A880',
    champagneLight: '#F3EDE4',
    obsidian: '#171717',
    ivory: '#F7F5F0',
    tabBar: '#FFFFFF',
    tabBarBorder: '#E2DCD5',
    tabBarActive: '#171717',
    tabBarInactive: '#A8A29E',
    imageBacking: '#F5F2EB',
    badgeBg: 'rgba(255, 255, 255, 0.92)',
    badgeText: '#A67C52',
    inputBg: '#FBF9F6',
    inputBorder: '#E2DCD5',
    inputText: '#171717',
    statusBarStyle: 'dark',
    monogramBg: '#171717',
    monogramText: '#F7F5F0',
  },
  dark: {
    name: 'dark',
    isDark: true,
    background: '#0F0F0F',
    backgroundSubtle: '#171717',
    surface: '#1A1A1A',
    surfaceSubtle: '#242424',
    card: '#1A1A1A',
    cardBorder: 'rgba(216, 208, 196, 0.16)',
    textPrimary: '#F7F5F0',
    textSecondary: '#D8D0C4',
    textMuted: '#8E887E',
    border: 'rgba(216, 208, 196, 0.18)',
    borderSubtle: '#262626',
    accent: '#D4B996',
    champagne: '#C5A880',
    champagneLight: '#26221D',
    obsidian: '#F7F5F0',
    ivory: '#0F0F0F',
    tabBar: '#141414',
    tabBarBorder: 'rgba(216, 208, 196, 0.14)',
    tabBarActive: '#C5A880',
    tabBarInactive: '#737373',
    imageBacking: '#141414',
    badgeBg: 'rgba(26, 26, 26, 0.92)',
    badgeText: '#D4B996',
    inputBg: '#242424',
    inputBorder: 'rgba(216, 208, 196, 0.22)',
    inputText: '#F7F5F0',
    statusBarStyle: 'light',
    monogramBg: '#C5A880',
    monogramText: '#171717',
  },
};

export const Colors = {
  light: {
    text: '#171717',
    background: '#F7F5F0',
    backgroundElement: '#ECE8E1',
    backgroundSelected: '#D8D0C4',
    textSecondary: '#66615B',
  },
  dark: {
    text: '#FFFFFF',
    background: '#0F0F0F',
    backgroundElement: '#1F1F1F',
    backgroundSelected: '#2E2A25',
    textSecondary: '#D8D0C4',
  },
};

export const CURRENCY = '₱';

export const formatPrice = (amount) => {
  if (amount === null || amount === undefined) return `${CURRENCY}0.00`;
  
  let clean = amount;
  if (typeof amount === 'string') {
    clean = amount.replace(/[$₱,\s]/g, '');
  }
  
  const num = Number(clean);
  if (isNaN(num)) return `${CURRENCY}0.00`;
  
  const fixed = num.toFixed(2);
  const parts = fixed.split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return `${CURRENCY}${parts.join('.')}`;
};

export const SPACING = {
  xs: 4,
  sm: 8,
  md: 16,
  lg: 24,
  xl: 32,
  xxl: 48,
};

export const FONTS = {
  display: 'Playfair Display, Georgia, serif',
  body: 'Inter, system-ui, -apple-system, sans-serif',
};

export const GLASS = {
  card: {
    backgroundColor: 'rgba(255, 255, 255, 0.92)',
    borderColor: 'rgba(216, 208, 196, 0.65)',
    borderWidth: 1,
  },
  darkCard: {
    backgroundColor: 'rgba(23, 23, 23, 0.94)',
    borderColor: 'rgba(197, 168, 128, 0.35)',
    borderWidth: 1,
  },
  pill: {
    backgroundColor: 'rgba(247, 245, 240, 0.9)',
    borderColor: 'rgba(216, 208, 196, 0.8)',
    borderWidth: 1,
  },
};

export const SHADOWS = {
  subtle: {
    shadowColor: '#171717',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 2,
  },
  card: {
    shadowColor: '#171717',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 16,
    elevation: 4,
  },
  elevated: {
    shadowColor: '#171717',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.12,
    shadowRadius: 24,
    elevation: 8,
  },
  floating: {
    shadowColor: '#171717',
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.22,
    shadowRadius: 30,
    elevation: 12,
  },
};
