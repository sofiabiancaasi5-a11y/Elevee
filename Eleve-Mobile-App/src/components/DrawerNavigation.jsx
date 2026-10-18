import React, { useEffect, useRef, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  ScrollView,
  StyleSheet,
  Dimensions,
  Animated,
  TouchableWithoutFeedback,
  Platform,
  Linking,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useRouter, usePathname } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useDrawer } from '../context/DrawerContext';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { useWishlist } from '../context/WishlistContext';
import { useNotifications } from '../context/NotificationContext';
import { useTheme } from '../context/ThemeContext';
import { COLORS } from '../constants/theme';

const { width } = Dimensions.get('window');
const DRAWER_WIDTH = Math.min(width * 0.84, 340);

const CATEGORIES = [
  { name: 'Sneakers & Low-Tops', slug: 'sneakers', icon: 'footsteps-outline' },
  { name: 'Sculpted Heels & Pumps', slug: 'heels', icon: 'diamond-outline' },
  { name: 'Glove Ballet Flats', slug: 'flats', icon: 'sparkles-outline' },
  { name: 'Lug Loafers & Monks', slug: 'loafers', icon: 'business-outline' },
  { name: 'Chelsea Boots', slug: 'boots', icon: 'shield-outline' },
  { name: 'Artisanal Slides', slug: 'sandals', icon: 'sunny-outline' },
];

export default function DrawerNavigation() {
  const router = useRouter();
  const pathname = usePathname();
  const { isDrawerOpen, closeDrawer } = useDrawer();
  const { user, isAuthenticated, logout } = useAuth();
  const { cart } = useCart();
  const { wishlist } = useWishlist();
  const { unreadCount, openModal, showToast } = useNotifications();
  const { theme, colors, isDark, setThemeMode } = useTheme();

  const [isRendered, setIsRendered] = useState(false);
  const animX = useRef(new Animated.Value(-DRAWER_WIDTH)).current;
  const animOpacity = useRef(new Animated.Value(0)).current;

  // Active route checks for luxury visual indicators
  const isHomeActive = pathname === '/' || pathname === '/(tabs)' || pathname === '/(tabs)/index';
  const isShopActive = pathname?.includes('/shop');
  const isWishlistActive = pathname?.includes('/wishlist');
  const isCartActive = pathname?.includes('/cart');
  const isProfileActive = pathname?.includes('/profile');

  useEffect(() => {
    if (isDrawerOpen) {
      setIsRendered(true);
      Animated.parallel([
        Animated.spring(animX, {
          toValue: 0,
          useNativeDriver: true,
          bounciness: 2,
          speed: 15,
        }),
        Animated.timing(animOpacity, {
          toValue: 1,
          duration: 250,
          useNativeDriver: true,
        }),
      ]).start();
    } else {
      Animated.parallel([
        Animated.timing(animX, {
          toValue: -DRAWER_WIDTH,
          duration: 220,
          useNativeDriver: true,
        }),
        Animated.timing(animOpacity, {
          toValue: 0,
          duration: 200,
          useNativeDriver: true,
        }),
      ]).start(({ finished }) => {
        if (finished) {
          setIsRendered(false);
        }
      });
    }
  }, [isDrawerOpen, animX, animOpacity]);

  if (!isDrawerOpen && !isRendered) {
    return null;
  }

  const navigateTo = (path, params) => {
    closeDrawer();
    setTimeout(() => {
      try {
        if (params) {
          router.navigate({ pathname: path, params });
        } else {
          router.navigate(path);
        }
      } catch {
        // Fallback to push if navigate fails
        if (params) {
          router.push({ pathname: path, params });
        } else {
          router.push(path);
        }
      }
    }, 180);
  };

  const handleOpenNotifs = () => {
    closeDrawer();
    setTimeout(() => {
      openModal();
    }, 180);
  };

  return (
    <View style={styles.overlayContainer} pointerEvents={isDrawerOpen ? 'auto' : 'none'}>
      {/* Backdrop */}
      <TouchableWithoutFeedback onPress={closeDrawer}>
        <Animated.View style={[styles.backdrop, { opacity: animOpacity }]} />
      </TouchableWithoutFeedback>

      {/* Slide-over Drawer Panel */}
      <Animated.View
        style={[
          styles.drawerPanel,
          {
            backgroundColor: colors.surface,
            borderRightColor: colors.border,
            transform: [{ translateX: animX }],
          },
        ]}
      >
        <SafeAreaView style={styles.safeArea} edges={['top', 'bottom']}>
          {/* Atelier Brand Header */}
          <View style={[styles.header, { borderBottomColor: colors.borderSubtle }]}>
            <View style={styles.brandRow}>
              <TouchableOpacity
                style={{ flexDirection: 'row', alignItems: 'center', flex: 1 }}
                onPress={() => navigateTo('/(tabs)')}
                activeOpacity={0.8}
              >
                <View style={[styles.monogram, { backgroundColor: colors.monogramBg }]}>
                  <Text style={[styles.monogramText, { color: colors.monogramText }]}>É</Text>
                </View>
                <View>
                  <Text style={[styles.brandTitle, { color: colors.textPrimary }]}>ÉLEVÉ PARIS</Text>
                  <Text style={[styles.brandSubtitle, { color: colors.accent }]}>HAUTE CORDONNERIE</Text>
                </View>
              </TouchableOpacity>
              <TouchableOpacity
                onPress={closeDrawer}
                style={styles.closeBtn}
                hitSlop={{ top: 12, bottom: 12, left: 12, right: 12 }}
              >
                <Ionicons name="close" size={22} color={colors.textPrimary} />
              </TouchableOpacity>
            </View>

            {/* Client Status Card - Fully Interactive */}
            <TouchableOpacity
              activeOpacity={0.8}
              onPress={() => navigateTo(isAuthenticated ? '/(tabs)/profile' : '/auth/login')}
              style={[
                styles.clientCard,
                {
                  backgroundColor: colors.backgroundSubtle,
                  borderColor: isProfileActive ? colors.accent : colors.border,
                },
              ]}
            >
              <View style={[styles.clientAvatar, { backgroundColor: colors.champagne }]}>
                <Text style={styles.clientAvatarText}>
                  {user?.name ? user.name.charAt(0).toUpperCase() : 'É'}
                </Text>
              </View>
              <View style={styles.clientDetails}>
                <Text style={[styles.clientGreeting, { color: colors.textPrimary }]}>
                  {isAuthenticated ? `Welcome, ${user?.name?.split(' ')[0]}` : 'Welcome to the Atelier'}
                </Text>
                <Text style={[styles.clientEmail, { color: colors.textMuted }]} numberOfLines={1}>
                  {isAuthenticated ? (user?.email || 'Manage Account & Orders') : 'Sign in to access bespoke curation'}
                </Text>
              </View>
              <Ionicons name="chevron-forward" size={15} color={colors.textMuted} />
            </TouchableOpacity>
          </View>

          {/* Navigation Links List */}
          <ScrollView
            style={styles.scrollList}
            contentContainerStyle={styles.scrollContent}
            showsVerticalScrollIndicator={false}
          >
            {/* Atelier Ambience Selector */}
            <Text style={[styles.sectionHeader, { color: colors.accent }]}>ATELIER AMBIENCE</Text>
            <View style={[styles.ambienceContainer, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}>
              <TouchableOpacity
                style={[
                  styles.ambienceOption,
                  theme === 'light' && [styles.ambienceOptionActive, { backgroundColor: colors.surface, borderColor: colors.border }],
                ]}
                onPress={() => setThemeMode('light')}
                activeOpacity={0.8}
              >
                <Ionicons
                  name="sunny-outline"
                  size={15}
                  color={theme === 'light' ? colors.accent : colors.textMuted}
                />
                <Text
                  style={[
                    styles.ambienceOptionText,
                    { color: theme === 'light' ? colors.textPrimary : colors.textMuted },
                    theme === 'light' && styles.ambienceOptionTextActive,
                  ]}
                >
                  Ivory Day
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.ambienceOption,
                  theme === 'dark' && [styles.ambienceOptionActive, { backgroundColor: colors.surface, borderColor: colors.border }],
                ]}
                onPress={() => setThemeMode('dark')}
                activeOpacity={0.8}
              >
                <Ionicons
                  name="moon-outline"
                  size={15}
                  color={theme === 'dark' ? colors.accent : colors.textMuted}
                />
                <Text
                  style={[
                    styles.ambienceOptionText,
                    { color: theme === 'dark' ? colors.textPrimary : colors.textMuted },
                    theme === 'dark' && styles.ambienceOptionTextActive,
                  ]}
                >
                  Obsidian
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.ambienceOption,
                  theme === 'system' && [styles.ambienceOptionActive, { backgroundColor: colors.surface, borderColor: colors.border }],
                ]}
                onPress={() => setThemeMode('system')}
                activeOpacity={0.8}
              >
                <Ionicons
                  name="phone-portrait-outline"
                  size={14}
                  color={theme === 'system' ? colors.accent : colors.textMuted}
                />
                <Text
                  style={[
                    styles.ambienceOptionText,
                    { color: theme === 'system' ? colors.textPrimary : colors.textMuted },
                    theme === 'system' && styles.ambienceOptionTextActive,
                  ]}
                >
                  Auto
                </Text>
              </TouchableOpacity>
            </View>

            {/* Core Destinations */}
            <Text style={[styles.sectionHeader, { color: colors.accent, marginTop: 18 }]}>MAISON & CATALOG</Text>

            <TouchableOpacity
              style={[
                styles.navRow,
                { borderBottomColor: colors.borderSubtle },
                isHomeActive && { backgroundColor: isDark ? 'rgba(197, 168, 128, 0.08)' : 'rgba(197, 168, 128, 0.12)' },
              ]}
              onPress={() => navigateTo('/(tabs)')}
              activeOpacity={0.75}
            >
              <Ionicons name={isHomeActive ? "home" : "home-outline"} size={19} color={isHomeActive ? colors.accent : colors.textPrimary} />
              <Text style={[styles.navLabel, { color: isHomeActive ? colors.accent : colors.textPrimary }, isHomeActive && styles.navLabelActive]}>
                Atelier Maison Home
              </Text>
              <Ionicons name="chevron-forward" size={14} color={isHomeActive ? colors.accent : colors.textMuted} style={styles.chevron} />
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.navRow,
                { borderBottomColor: colors.borderSubtle },
                isShopActive && { backgroundColor: isDark ? 'rgba(197, 168, 128, 0.08)' : 'rgba(197, 168, 128, 0.12)' },
              ]}
              onPress={() => navigateTo('/(tabs)/shop', { category: 'all' })}
              activeOpacity={0.75}
            >
              <Ionicons name={isShopActive ? "grid" : "grid-outline"} size={19} color={isShopActive ? colors.accent : colors.textPrimary} />
              <Text style={[styles.navLabel, { color: isShopActive ? colors.accent : colors.textPrimary }, isShopActive && styles.navLabelActive]}>
                Complete Footwear Catalog
              </Text>
              <Ionicons name="chevron-forward" size={14} color={isShopActive ? colors.accent : colors.textMuted} style={styles.chevron} />
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.navRow,
                { borderBottomColor: colors.borderSubtle },
                isWishlistActive && { backgroundColor: isDark ? 'rgba(197, 168, 128, 0.08)' : 'rgba(197, 168, 128, 0.12)' },
              ]}
              onPress={() => navigateTo('/(tabs)/wishlist')}
              activeOpacity={0.75}
            >
              <Ionicons name={isWishlistActive ? "heart" : "heart-outline"} size={19} color={isWishlistActive ? colors.accent : colors.textPrimary} />
              <Text style={[styles.navLabel, { color: isWishlistActive ? colors.accent : colors.textPrimary }, isWishlistActive && styles.navLabelActive]}>
                Curated Wishlist
              </Text>
              {wishlist.length > 0 && (
                <View style={[styles.badge, { backgroundColor: isDark ? colors.champagne : colors.accent }]}>
                  <Text style={[styles.badgeText, { color: isDark ? '#141414' : '#FFFFFF' }]}>{wishlist.length}</Text>
                </View>
              )}
              <Ionicons name="chevron-forward" size={14} color={colors.textMuted} style={styles.chevron} />
            </TouchableOpacity>

            <TouchableOpacity
              style={[
                styles.navRow,
                { borderBottomColor: colors.borderSubtle },
                isCartActive && { backgroundColor: isDark ? 'rgba(197, 168, 128, 0.08)' : 'rgba(197, 168, 128, 0.12)' },
              ]}
              onPress={() => navigateTo('/(tabs)/cart')}
              activeOpacity={0.75}
            >
              <Ionicons name={isCartActive ? "bag" : "bag-outline"} size={19} color={isCartActive ? colors.accent : colors.textPrimary} />
              <Text style={[styles.navLabel, { color: isCartActive ? colors.accent : colors.textPrimary }, isCartActive && styles.navLabelActive]}>
                Shopping Bag
              </Text>
              {cart?.total_items > 0 && (
                <View style={[styles.badge, { backgroundColor: isDark ? colors.champagne : colors.accent }]}>
                  <Text style={[styles.badgeText, { color: isDark ? '#141414' : '#FFFFFF' }]}>{cart.total_items}</Text>
                </View>
              )}
              <Ionicons name="chevron-forward" size={14} color={colors.textMuted} style={styles.chevron} />
            </TouchableOpacity>

            {/* Notifications Shortcut */}
            <TouchableOpacity
              style={[styles.navRow, { borderBottomColor: colors.borderSubtle }]}
              onPress={handleOpenNotifs}
              activeOpacity={0.75}
            >
              <Ionicons name="notifications-outline" size={19} color={colors.textPrimary} />
              <Text style={[styles.navLabel, { color: colors.textPrimary }]}>Atelier Dispatch Updates</Text>
              {unreadCount > 0 && (
                <View style={[styles.badge, { backgroundColor: '#DC2626' }]}>
                  <Text style={[styles.badgeText, { color: '#FFFFFF' }]}>{unreadCount > 9 ? '9+' : unreadCount}</Text>
                </View>
              )}
              <Ionicons name="chevron-forward" size={14} color={colors.textMuted} style={styles.chevron} />
            </TouchableOpacity>

            {/* Category Quick Jumps */}
            <Text style={[styles.sectionHeader, { color: colors.accent, marginTop: 20 }]}>CURATED SILHOUETTES</Text>

            {CATEGORIES.map((cat) => (
              <TouchableOpacity
                key={cat.slug}
                style={styles.categoryRow}
                onPress={() => navigateTo('/(tabs)/shop', { category: cat.slug })}
                activeOpacity={0.75}
              >
                <Ionicons name={cat.icon} size={16} color={colors.accent} />
                <Text style={[styles.categoryLabel, { color: colors.textSecondary }]}>{cat.name}</Text>
                <Ionicons name="arrow-forward" size={13} color={colors.textMuted} style={styles.chevron} />
              </TouchableOpacity>
            ))}

            {/* Account & Client Desk */}
            <Text style={[styles.sectionHeader, { color: colors.accent, marginTop: 20 }]}>CLIENT SERVICES</Text>

            <TouchableOpacity
              style={[
                styles.navRow,
                { borderBottomColor: colors.borderSubtle },
                isProfileActive && { backgroundColor: isDark ? 'rgba(197, 168, 128, 0.08)' : 'rgba(197, 168, 128, 0.12)' },
              ]}
              onPress={() => navigateTo('/(tabs)/profile')}
              activeOpacity={0.75}
            >
              <Ionicons name={isProfileActive ? "person" : "person-outline"} size={19} color={isProfileActive ? colors.accent : colors.textPrimary} />
              <Text style={[styles.navLabel, { color: isProfileActive ? colors.accent : colors.textPrimary }, isProfileActive && styles.navLabelActive]}>
                Client Account & Orders
              </Text>
              <Ionicons name="chevron-forward" size={14} color={colors.textMuted} style={styles.chevron} />
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.navRow, { borderBottomColor: colors.borderSubtle }]}
              onPress={() => navigateTo('/(tabs)/profile', { section: 'track', t: String(Date.now()) })}
              activeOpacity={0.75}
            >
              <Ionicons name="locate-outline" size={19} color={colors.textPrimary} />
              <Text style={[styles.navLabel, { color: colors.textPrimary }]}>Live Order Tracking</Text>
              <Ionicons name="chevron-forward" size={14} color={colors.textMuted} style={styles.chevron} />
            </TouchableOpacity>

            {/* Atelier Concierge Box - Interactive Phone & Email */}
            <TouchableOpacity
              activeOpacity={0.85}
              style={[styles.conciergeCard, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}
              onPress={() => {
                Alert.alert(
                  'Private Client Concierge',
                  'Select your preferred luxury contact method:',
                  [
                    {
                      text: 'Call Atelier (+63 2 8811-3400)',
                      onPress: () => Linking.openURL('tel:+63288113400'),
                    },
                    {
                      text: 'Email Concierge Desk',
                      onPress: () => Linking.openURL('mailto:concierge@atelier-eleve.ph?subject=Client Inquiry'),
                    },
                    { text: 'Cancel', style: 'cancel' },
                  ]
                );
              }}
            >
              <Ionicons name="chatbubbles-outline" size={18} color={colors.accent} />
              <View style={{ flex: 1 }}>
                <Text style={[styles.conciergeTitle, { color: colors.textPrimary }]}>Private Client Concierge</Text>
                <Text style={[styles.conciergeDesc, { color: colors.textSecondary }]}>concierge@atelier-eleve.ph</Text>
                <Text style={[styles.conciergeDesc, { color: colors.textSecondary }]}>+63 (2) 8811-3400 (9am - 8pm PHT)</Text>
              </View>
              <Ionicons name="call-outline" size={15} color={colors.accent} />
            </TouchableOpacity>

            {/* Auth Actions */}
            <View style={{ marginTop: 22, marginBottom: 20 }}>
              {isAuthenticated ? (
                <TouchableOpacity
                  style={[
                    styles.authActionBtn,
                    {
                      backgroundColor: isDark ? 'rgba(220, 38, 38, 0.12)' : '#FEF2F2',
                      borderColor: isDark ? 'rgba(220, 38, 38, 0.35)' : '#FCA5A5',
                    },
                  ]}
                  onPress={() => {
                    closeDrawer();
                    logout();
                    showToast('Atelier Signed Out', 'You have securely signed out of your client profile.');
                  }}
                  activeOpacity={0.8}
                >
                  <Ionicons name="log-out-outline" size={17} color="#DC2626" />
                  <Text style={[styles.authActionBtnText, { color: '#DC2626' }]}>Sign Out of Atelier</Text>
                </TouchableOpacity>
              ) : (
                <TouchableOpacity
                  style={[
                    styles.authActionBtn,
                    {
                      backgroundColor: colors.textPrimary,
                      borderColor: colors.textPrimary,
                    },
                  ]}
                  onPress={() => navigateTo('/auth/login')}
                  activeOpacity={0.85}
                >
                  <Ionicons name="log-in-outline" size={17} color={colors.background} />
                  <Text style={[styles.authActionBtnText, { color: colors.background }]}>Sign In / Register</Text>
                </TouchableOpacity>
              )}
            </View>
          </ScrollView>

          {/* Footer Motto */}
          <View style={[styles.footer, { borderTopColor: colors.borderSubtle }]}>
            <Text style={[styles.footerMotto, { color: colors.textPrimary }]}>Every Step, Elevated.</Text>
            <Text style={[styles.footerSub, { color: colors.textMuted }]}>Paris &bull; Manila &bull; Milan</Text>
          </View>
        </SafeAreaView>
      </Animated.View>
    </View>
  );
}

const styles = StyleSheet.create({
  overlayContainer: {
    ...StyleSheet.absoluteFillObject,
    zIndex: 9999,
    elevation: 50,
  },
  backdrop: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0, 0, 0, 0.65)',
  },
  drawerPanel: {
    width: DRAWER_WIDTH,
    height: '100%',
    borderRightWidth: 1,
    shadowColor: '#000',
    shadowOffset: { width: 4, height: 0 },
    shadowOpacity: 0.25,
    shadowRadius: 16,
    elevation: 60,
    zIndex: 10000,
  },
  safeArea: {
    flex: 1,
  },
  header: {
    paddingHorizontal: 20,
    paddingTop: 10,
    paddingBottom: 16,
    borderBottomWidth: 1,
  },
  brandRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 14,
  },
  monogram: {
    width: 36,
    height: 36,
    borderRadius: 18,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  monogramText: {
    fontFamily: 'serif',
    fontSize: 18,
    fontWeight: 'bold',
  },
  brandTitle: {
    fontFamily: 'serif',
    fontSize: 14,
    fontWeight: '700',
    letterSpacing: 2,
  },
  brandSubtitle: {
    fontSize: 8,
    letterSpacing: 1.5,
    fontWeight: '600',
  },
  closeBtn: {
    marginLeft: 'auto',
    padding: 4,
  },
  clientCard: {
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: 12,
    borderWidth: 1,
    padding: 10,
    gap: 10,
  },
  clientAvatar: {
    width: 34,
    height: 34,
    borderRadius: 17,
    alignItems: 'center',
    justifyContent: 'center',
  },
  clientAvatarText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#FFFFFF',
    fontFamily: 'serif',
  },
  clientDetails: {
    flex: 1,
  },
  clientGreeting: {
    fontSize: 12,
    fontWeight: '700',
  },
  clientEmail: {
    fontSize: 10,
    marginTop: 1,
  },
  scrollList: {
    flex: 1,
  },
  scrollContent: {
    paddingHorizontal: 20,
    paddingTop: 16,
    paddingBottom: 60, // Ample room so logout button is NEVER covered by the footer!
  },
  sectionHeader: {
    fontSize: 9.5,
    fontWeight: '700',
    letterSpacing: 1.5,
    marginBottom: 10,
  },
  ambienceContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: 10,
    borderWidth: 1,
    padding: 3,
    marginBottom: 8,
  },
  ambienceOption: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 7,
    borderRadius: 8,
    gap: 5,
    borderWidth: 1,
    borderColor: 'transparent',
  },
  ambienceOptionActive: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.12,
    shadowRadius: 2,
    elevation: 2,
  },
  ambienceOptionText: {
    fontSize: 11,
    fontWeight: '500',
  },
  ambienceOptionTextActive: {
    fontWeight: '700',
  },
  navRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 11,
    paddingHorizontal: 8,
    borderRadius: 8,
    borderBottomWidth: 0.5,
    gap: 12,
  },
  navLabel: {
    fontSize: 13,
    fontWeight: '500',
  },
  navLabelActive: {
    fontWeight: '700',
  },
  chevron: {
    marginLeft: 'auto',
  },
  badge: {
    marginLeft: 'auto',
    paddingHorizontal: 7,
    paddingVertical: 2,
    borderRadius: 10,
    marginRight: 6,
  },
  badgeText: {
    fontSize: 10,
    fontWeight: 'bold',
  },
  categoryRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 9,
    paddingHorizontal: 8,
    borderRadius: 8,
    gap: 10,
  },
  categoryLabel: {
    fontSize: 12.5,
    fontWeight: '500',
  },
  conciergeCard: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    borderRadius: 10,
    borderWidth: 1,
    padding: 12,
    marginTop: 14,
    gap: 10,
  },
  conciergeTitle: {
    fontSize: 11,
    fontWeight: '700',
    marginBottom: 2,
  },
  conciergeDesc: {
    fontSize: 10,
    lineHeight: 14,
  },
  authActionBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    borderRadius: 10,
    borderWidth: 1,
    gap: 8,
  },
  authActionBtnText: {
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 0.5,
  },
  footer: {
    paddingHorizontal: 20,
    paddingVertical: 14,
    borderTopWidth: 1,
    alignItems: 'center',
  },
  footerMotto: {
    fontFamily: 'serif',
    fontSize: 11,
    fontStyle: 'italic',
  },
  footerSub: {
    fontSize: 8.5,
    letterSpacing: 1.5,
    marginTop: 2,
  },
});
