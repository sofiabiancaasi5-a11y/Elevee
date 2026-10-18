import React, { useState, useEffect, useCallback, useRef } from 'react';
import {
  View,
  Text,
  TextInput,
  Alert,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter, useLocalSearchParams } from 'expo-router';
import Header from '../../components/Header';
import EditProfileModal from '../../components/EditProfileModal';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { api } from '../../services/api';
import { COLORS, formatPrice } from '../../constants/theme';

export default function ProfileScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const { user, isAuthenticated, logout } = useAuth();
  const { theme, colors, isDark, setThemeMode } = useTheme();
  const [orders, setOrders] = useState([]);
  const [isLoadingOrders, setIsLoadingOrders] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [searchOrderRef, setSearchOrderRef] = useState('');

  const trackingInputRef = useRef(null);
  const scrollViewRef = useRef(null);

  useEffect(() => {
    if (params.section === 'track') {
      const timer = setTimeout(() => {
        scrollViewRef.current?.scrollTo({ y: isAuthenticated ? 180 : 250, animated: true });
        trackingInputRef.current?.focus();
      }, 350);
      return () => clearTimeout(timer);
    }
  }, [params.section, params.t, isAuthenticated]);

  const loadUserOrders = useCallback(async () => {
    if (!isAuthenticated) return;
    try {
      setIsLoadingOrders(true);
      const res = await api.getOrders();
      if (res.data) {
        setOrders(res.data);
      }
    } catch (e) {
      console.warn('Failed to load orders:', e.message);
    } finally {
      setIsLoadingOrders(false);
      setRefreshing(false);
    }
  }, [isAuthenticated]);

  useEffect(() => {
    loadUserOrders();
  }, [loadUserOrders]);

  const onRefresh = () => {
    setRefreshing(true);
    loadUserOrders();
  };

  const handleOrderPress = (orderNumber) => {
    router.push({
      pathname: '/order/[id]',
      params: { id: orderNumber },
    });
  };

  const handleTrackSearch = () => {
    const cleaned = searchOrderRef.trim().replace(/^#/, '');
    if (!cleaned) {
      Alert.alert('Order Reference Required', 'Please enter your order reference (e.g. ELE-20261018-MVEGP)');
      return;
    }
    router.push({
      pathname: '/order/[id]',
      params: { id: cleaned.toUpperCase() },
    });
  };

  const renderTrackingSearchBox = () => (
    <View style={[styles.trackingSearchCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
      <View style={styles.trackingSearchHeader}>
        <Ionicons name="compass-outline" size={17} color={colors.accent} />
        <Text style={[styles.trackingSearchTitle, { color: colors.textPrimary }]}>Live Order Tracking Lookup</Text>
      </View>
      <Text style={[styles.trackingSearchSub, { color: colors.textMuted }]}>
        Track real-time crafting & dispatch by order reference:
      </Text>
      <View style={styles.trackingInputRow}>
        <TextInput
          ref={trackingInputRef}
          style={[
            styles.trackingInput,
            {
              backgroundColor: colors.surfaceSubtle,
              borderColor: colors.border,
              color: colors.textPrimary,
            },
          ]}
          placeholder="e.g. ELE-20261018-MVEGP"
          placeholderTextColor={colors.textMuted}
          value={searchOrderRef}
          onChangeText={setSearchOrderRef}
          autoCapitalize="characters"
          autoCorrect={false}
          returnKeyType="go"
          onSubmitEditing={handleTrackSearch}
        />
        <TouchableOpacity
          style={[styles.trackSubmitBtn, { backgroundColor: colors.accent }]}
          onPress={handleTrackSearch}
          activeOpacity={0.85}
        >
          <Text style={styles.trackSubmitBtnText}>Track</Text>
          <Ionicons name="arrow-forward" size={13} color="#FFFFFF" />
        </TouchableOpacity>
      </View>
    </View>
  );

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header title="CLIENT ACCOUNT" />

      <ScrollView
        ref={scrollViewRef}
        style={styles.container}
        contentContainerStyle={styles.content}
        showsVerticalScrollIndicator={false}
        refreshControl={
          isAuthenticated ? (
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.textPrimary} />
          ) : undefined
        }
      >
        {/* Atelier Ambience Selector Card */}
        <View style={[styles.ambienceCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <View style={styles.ambienceHeader}>
            <Ionicons name="sparkles-outline" size={16} color={colors.accent} />
            <Text style={[styles.ambienceTitle, { color: colors.textPrimary }]}>Atelier Ambience Theme</Text>
          </View>
          <View style={[styles.ambienceToggleGroup, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}>
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
                size={14}
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
                size={14}
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
        </View>

        {!isAuthenticated ? (
          <View style={styles.guestContainer}>
            <View style={[styles.guestMonogram, { backgroundColor: colors.monogramBg }]}>
              <Text style={[styles.guestMonogramText, { color: colors.monogramText }]}>É</Text>
            </View>
            <Text style={[styles.guestTitle, { color: colors.textPrimary }]}>Welcome to ÉLEVÉ</Text>
            <Text style={[styles.guestSubtitle, { color: colors.textSecondary }]}>
              Sign in to manage your orders, track shipments in real-time, and preserve your curated wishlist.
            </Text>

            <View style={styles.authButtons}>
              <TouchableOpacity
                style={[styles.signInButton, { backgroundColor: colors.textPrimary }]}
                onPress={() => router.push('/auth/login')}
                activeOpacity={0.85}
              >
                <Text style={[styles.signInButtonText, { color: colors.background }]}>Sign In</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.registerButton, { borderColor: colors.border }]}
                onPress={() => router.push('/auth/register')}
                activeOpacity={0.85}
              >
                <Text style={[styles.registerButtonText, { color: colors.textPrimary }]}>Create Account</Text>
              </TouchableOpacity>
            </View>

            {/* Quick Live Tracking for Guests */}
            {renderTrackingSearchBox()}
          </View>
        ) : (
          <View style={styles.authContainer}>
            <View style={[styles.profileCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
              <View style={[styles.profileAvatar, { backgroundColor: colors.champagne }]}>
                <Text style={styles.profileAvatarText}>
                  {user?.name ? user.name.charAt(0).toUpperCase() : 'C'}
                </Text>
              </View>

              <View style={styles.profileDetails}>
                <Text style={[styles.profileName, { color: colors.textPrimary }]}>{user?.name}</Text>
                <Text style={[styles.profileEmail, { color: colors.textMuted }]}>{user?.email}</Text>
                {user?.phone ? <Text style={[styles.profilePhone, { color: colors.textSecondary }]}>{user.phone}</Text> : null}
              </View>

              <TouchableOpacity
                style={[styles.editProfileBtn, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border }]}
                onPress={() => setIsEditModalOpen(true)}
                activeOpacity={0.8}
                hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
              >
                <Ionicons name="create-outline" size={16} color={colors.textPrimary} />
                <Text style={[styles.editProfileBtnText, { color: colors.textPrimary }]}>Edit</Text>
              </TouchableOpacity>
            </View>

            {/* Quick Live Tracking Lookup */}
            {renderTrackingSearchBox()}

            <View style={styles.section}>
              <View style={styles.sectionHeader}>
                <Text style={[styles.sectionTitle, { color: colors.textPrimary }]}>Order History & Tracking</Text>
                <Text style={[styles.orderCount, { color: colors.accent }]}>{orders.length} Orders</Text>
              </View>

              {isLoadingOrders && orders.length === 0 ? (
                <ActivityIndicator size="small" color={colors.textPrimary} style={{ padding: 20 }} />
              ) : orders.length === 0 ? (
                <View style={[styles.emptyOrdersCard, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}>
                  <Ionicons name="receipt-outline" size={28} color={colors.textMuted} />
                  <Text style={[styles.emptyOrdersText, { color: colors.textMuted }]}>You have not placed any orders yet.</Text>
                </View>
              ) : (
                <View style={styles.ordersList}>
                  {orders.map((order) => {
                    const badgeStyles = matchOrderBadge(order.status);

                    return (
                      <TouchableOpacity
                        key={order.id}
                        style={[styles.orderCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}
                        onPress={() => handleOrderPress(order.order_number)}
                        activeOpacity={0.8}
                      >
                        <View style={styles.orderCardTop}>
                          <View>
                            <Text style={[styles.orderNumber, { color: colors.textPrimary }]}>{order.order_number}</Text>
                            <Text style={[styles.orderDate, { color: colors.textMuted }]}>
                              {order.formatted_date || order.created_at}
                            </Text>
                          </View>

                          <View style={[styles.statusBadge, { backgroundColor: badgeStyles.bg, borderColor: badgeStyles.border }]}>
                            <Text style={[styles.statusText, { color: badgeStyles.text }]}>
                              {order.status.toUpperCase()}
                            </Text>
                          </View>
                        </View>

                        <View style={[styles.orderCardBottom, { borderTopColor: colors.borderSubtle }]}>
                          <Text style={[styles.orderItemsSummary, { color: colors.textSecondary }]}>
                            {order.items?.length || 1} pairs &bull; Total:
                          </Text>
                          <Text style={[styles.orderAmount, { color: colors.textPrimary }]}>
                            {formatPrice(order.total_amount)}
                          </Text>
                          
                          <Ionicons name="chevron-forward" size={16} color={colors.textMuted} style={{ marginLeft: 'auto' }} />
                        </View>
                      </TouchableOpacity>
                    );
                  })}
                </View>
              )}
            </View>

            {/* Saved Addresses Section */}
            {user?.addresses && user.addresses.length > 0 && (
              <View style={styles.section}>
                <Text style={[styles.sectionTitle, { color: colors.textPrimary }]}>Default Delivery Destination</Text>
                <View style={[styles.addressCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
                  <Text style={[styles.addressName, { color: colors.textPrimary }]}>{user.addresses[0].recipient_name}</Text>
                  <Text style={[styles.addressLine, { color: colors.textSecondary }]}>
                    {user.addresses[0].apartment ? `${user.addresses[0].apartment}, ` : ''}{user.addresses[0].street_address}
                  </Text>
                  <Text style={[styles.addressCity, { color: colors.textSecondary }]}>
                    {user.addresses[0].city}, {user.addresses[0].province_state} {user.addresses[0].postal_code}
                  </Text>
                  <Text style={[styles.addressPhone, { color: colors.textMuted }]}>{user.addresses[0].phone_number}</Text>
                </View>
              </View>
            )}

            {/* Logout Button */}
            <TouchableOpacity
              style={[
                styles.logoutButton,
                {
                  backgroundColor: isDark ? 'rgba(220, 38, 38, 0.12)' : '#FEF2F2',
                  borderColor: isDark ? 'rgba(220, 38, 38, 0.35)' : '#FECACA',
                },
              ]}
              onPress={logout}
              activeOpacity={0.85}
            >
              <Ionicons name="log-out-outline" size={18} color="#DC2626" />
              <Text style={styles.logoutButtonText}>Sign Out of Atelier</Text>
            </TouchableOpacity>
          </View>
        )}
      </ScrollView>

      {/* Edit Profile & Address Modal */}
      <EditProfileModal
        visible={isEditModalOpen}
        onClose={() => setIsEditModalOpen(false)}
      />
    </SafeAreaView>
  );
}

function matchOrderBadge(status) {
  switch (status) {
    case 'pending':
      return { bg: '#FEF3C7', text: '#92400E', border: '#FDE68A' };
    case 'confirmed':
      return { bg: '#DBEAFE', text: '#1E40AF', border: '#BFDBFE' };
    case 'processing':
      return { bg: '#F3E8FF', text: '#6B21A8', border: '#E9D5FF' };
    case 'shipped':
      return { bg: '#E0E7FF', text: '#3730A3', border: '#C7D2FE' };
    case 'delivered':
      return { bg: '#D1FAE5', text: '#065F46', border: '#A7F3D0' };
    case 'cancelled':
      return { bg: '#FEE2E2', text: '#991B1B', border: '#FECACA' };
    default:
      return { bg: '#F3F4F6', text: '#374151', border: '#E5E7EB' };
  }
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
  },
  container: {
    flex: 1,
  },
  content: {
    padding: 16,
    paddingBottom: 40,
  },
  ambienceCard: {
    borderRadius: 14,
    borderWidth: 1,
    padding: 14,
    marginBottom: 16,
  },
  ambienceHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginBottom: 10,
  },
  ambienceTitle: {
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 0.5,
  },
  ambienceToggleGroup: {
    flexDirection: 'row',
    alignItems: 'center',
    borderRadius: 10,
    borderWidth: 1,
    padding: 3,
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
  guestContainer: {
    alignItems: 'center',
    paddingVertical: 40,
    paddingHorizontal: 20,
  },
  guestMonogram: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: COLORS.obsidian,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 20,
  },
  guestMonogramText: {
    fontFamily: 'serif',
    fontSize: 32,
    fontWeight: 'bold',
    color: COLORS.ivory,
  },
  guestTitle: {
    fontFamily: 'serif',
    fontSize: 24,
    fontWeight: '700',
    color: COLORS.obsidian,
    marginBottom: 8,
  },
  guestSubtitle: {
    fontSize: 12.5,
    color: COLORS.textMuted,
    textAlign: 'center',
    lineHeight: 18,
    maxWidth: 280,
    marginBottom: 28,
  },
  authButtons: {
    width: '100%',
    gap: 12,
  },
  signInButton: {
    backgroundColor: COLORS.obsidian,
    height: 48,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  signInButtonText: {
    color: COLORS.ivory,
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  registerButton: {
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: COLORS.border,
    height: 48,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  registerButtonText: {
    color: COLORS.obsidian,
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  authContainer: {
    gap: 20,
  },
  profileCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    padding: 18,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border,
    gap: 16,
  },
  profileAvatar: {
    width: 52,
    height: 52,
    borderRadius: 26,
    backgroundColor: COLORS.obsidian,
    alignItems: 'center',
    justifyContent: 'center',
  },
  profileAvatarText: {
    color: COLORS.ivory,
    fontSize: 22,
    fontWeight: 'bold',
    fontFamily: 'serif',
  },
  profileDetails: {
    flex: 1,
  },
  editProfileBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
    backgroundColor: '#F7F5F0',
    borderWidth: 1,
    borderColor: '#D8D0C4',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
  },
  editProfileBtnText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  profileName: {
    fontSize: 16,
    fontWeight: '700',
    color: COLORS.obsidian,
    fontFamily: 'serif',
  },
  profileEmail: {
    fontSize: 12,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  profilePhone: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  section: {
    gap: 10,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'baseline',
  },
  sectionTitle: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1.5,
    color: COLORS.textMuted,
    textTransform: 'uppercase',
  },
  orderCount: {
    fontSize: 11,
    color: COLORS.textSecondary,
  },
  emptyOrdersCard: {
    backgroundColor: '#FFFFFF',
    padding: 24,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border,
    alignItems: 'center',
    gap: 8,
  },
  emptyOrdersText: {
    fontSize: 12,
    color: COLORS.textMuted,
  },
  ordersList: {
    gap: 10,
  },
  orderCard: {
    backgroundColor: '#FFFFFF',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border,
    gap: 12,
  },
  orderCardTop: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  orderNumber: {
    fontSize: 12,
    fontWeight: '700',
    fontFamily: 'monospace',
    color: COLORS.obsidian,
  },
  orderDate: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 4,
    borderWidth: 1,
  },
  statusText: {
    fontSize: 9,
    fontWeight: 'bold',
    letterSpacing: 0.8,
  },
  orderCardBottom: {
    flexDirection: 'row',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#F3EFEA',
    paddingTop: 10,
  },
  orderItemsSummary: {
    fontSize: 11,
    color: COLORS.textSecondary,
  },
  orderAmount: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.obsidian,
    marginLeft: 6,
  },
  addressCard: {
    backgroundColor: '#FFFFFF',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border,
    gap: 4,
  },
  addressName: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  addressLine: {
    fontSize: 12,
    color: COLORS.textSecondary,
  },
  addressCity: {
    fontSize: 12,
    color: COLORS.textSecondary,
  },
  addressPhone: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginTop: 4,
  },
  logoutButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FEF2F2',
    borderWidth: 1,
    borderColor: '#FECACA',
    height: 48,
    borderRadius: 8,
    gap: 8,
    marginTop: 10,
  },
  logoutButtonText: {
    color: '#DC2626',
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 1,
    textTransform: 'uppercase',
  },
  trackingSearchCard: {
    padding: 16,
    borderRadius: 14,
    borderWidth: 1,
    marginVertical: 14,
    gap: 8,
  },
  trackingSearchHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  trackingSearchTitle: {
    fontFamily: 'serif',
    fontSize: 13.5,
    fontWeight: '700',
    letterSpacing: 0.5,
  },
  trackingSearchSub: {
    fontSize: 11.5,
    lineHeight: 16,
  },
  trackingInputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    marginTop: 4,
  },
  trackingInput: {
    flex: 1,
    height: 42,
    borderRadius: 8,
    borderWidth: 1,
    paddingHorizontal: 12,
    fontSize: 12,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
  trackSubmitBtn: {
    height: 42,
    paddingHorizontal: 16,
    borderRadius: 8,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
  },
  trackSubmitBtnText: {
    color: '#FFFFFF',
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 0.5,
    textTransform: 'uppercase',
  },
});
