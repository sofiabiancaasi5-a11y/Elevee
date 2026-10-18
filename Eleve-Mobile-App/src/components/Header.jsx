import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useCart } from '../context/CartContext';
import { useNotifications } from '../context/NotificationContext';
import { useDrawer } from '../context/DrawerContext';
import { useTheme } from '../context/ThemeContext';

export default function Header({ title = 'ÉLEVÉ', showBack = false, showCart = true, showNotif = true }) {
  const router = useRouter();
  const { cart } = useCart();
  const { unreadCount, openModal } = useNotifications();
  const { openDrawer } = useDrawer();
  const { isDark, toggleTheme, colors } = useTheme();
  const itemCount = cart?.total_items || 0;

  return (
    <View style={[styles.container, { backgroundColor: colors.background, borderBottomColor: colors.border }]}>
      <View style={styles.left}>
        {showBack ? (
          <TouchableOpacity 
            onPress={() => router.back()} 
            hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
            style={styles.iconBtn}
          >
            <Ionicons name="arrow-back" size={22} color={colors.textPrimary} />
          </TouchableOpacity>
        ) : (
          <TouchableOpacity
            style={[styles.monogram, { backgroundColor: isDark ? colors.surfaceSubtle : colors.obsidian, borderColor: colors.border, borderWidth: isDark ? 1 : 0 }]}
            onPress={openDrawer}
            activeOpacity={0.8}
            hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
          >
            <Text style={[styles.monogramText, { color: isDark ? colors.champagne : colors.ivory }]}>É</Text>
          </TouchableOpacity>
        )}
      </View>

      <View style={styles.center}>
        <Text style={[styles.brandTitle, { color: colors.textPrimary }]}>{title}</Text>
        <Text style={[styles.subtitle, { color: colors.textMuted }]}>PARIS</Text>
      </View>

      <View style={styles.right}>
        {/* Luxury Ambience Theme Toggle */}
        <TouchableOpacity
          style={styles.iconBtn}
          onPress={toggleTheme}
          hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
          activeOpacity={0.75}
          title="Toggle Day / Night Ambience"
        >
          <Ionicons
            name={isDark ? 'sunny' : 'moon-outline'}
            size={19}
            color={isDark ? colors.champagne : colors.textPrimary}
          />
        </TouchableOpacity>

        {showNotif && (
          <TouchableOpacity 
            style={[styles.iconBtn, { marginLeft: 10 }]} 
            onPress={openModal}
            hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
          >
            <Ionicons name="notifications-outline" size={20} color={colors.textPrimary} />
            {unreadCount > 0 && (
              <View style={[styles.notifBadge, { backgroundColor: colors.champagne }]}>
                <Text style={styles.notifBadgeText}>{unreadCount > 9 ? '9+' : unreadCount}</Text>
              </View>
            )}
          </TouchableOpacity>
        )}

        {showCart && (
          <TouchableOpacity 
            style={[styles.iconBtn, { marginLeft: 10 }]} 
            onPress={() => router.push('/(tabs)/cart')}
            hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
          >
            <Ionicons name="bag-outline" size={20} color={colors.textPrimary} />
            {itemCount > 0 && (
              <View style={[styles.badge, { backgroundColor: isDark ? colors.champagne : colors.obsidian }]}>
                <Text style={[styles.badgeText, { color: isDark ? '#141414' : '#FFFFFF' }]}>
                  {itemCount > 9 ? '9+' : itemCount}
                </Text>
              </View>
            )}
          </TouchableOpacity>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    height: 56,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    borderBottomWidth: 1,
  },
  left: {
    width: 60,
    alignItems: 'flex-start',
  },
  center: {
    alignItems: 'center',
  },
  right: {
    width: 90,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'flex-end',
  },
  monogram: {
    width: 30,
    height: 30,
    borderRadius: 15,
    justifyContent: 'center',
    alignItems: 'center',
  },
  monogramText: {
    fontSize: 14,
    fontWeight: '700',
    fontFamily: 'serif',
  },
  brandTitle: {
    fontSize: 18,
    fontWeight: '800',
    letterSpacing: 3,
    fontFamily: 'serif',
  },
  subtitle: {
    fontSize: 8,
    letterSpacing: 2,
    marginTop: -2,
  },
  iconBtn: {
    position: 'relative',
    padding: 2,
  },
  badge: {
    position: 'absolute',
    top: -4,
    right: -6,
    borderRadius: 8,
    width: 16,
    height: 16,
    justifyContent: 'center',
    alignItems: 'center',
  },
  badgeText: {
    fontSize: 9,
    fontWeight: '700',
  },
  notifBadge: {
    position: 'absolute',
    top: -4,
    right: -6,
    borderRadius: 8,
    minWidth: 16,
    height: 16,
    paddingHorizontal: 3,
    justifyContent: 'center',
    alignItems: 'center',
  },
  notifBadgeText: {
    color: '#171717',
    fontSize: 9,
    fontWeight: '800',
  },
});
