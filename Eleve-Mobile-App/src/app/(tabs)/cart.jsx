import React from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Image } from 'expo-image';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import Header from '../../components/Header';
import EmptyState from '../../components/EmptyState';
import { useCart } from '../../context/CartContext';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { COLORS, formatPrice } from '../../constants/theme';

export default function CartScreen() {
  const router = useRouter();
  const { cart, updateQuantity, removeFromCart, isLoading } = useCart();
  const { isAuthenticated } = useAuth();
  const { colors, isDark } = useTheme();
  const items = cart?.items || [];
  const subtotal = cart?.subtotal || 0;
  const shippingFee = subtotal >= 5000 || subtotal === 0 ? 0.00 : 250.00;
  const total = subtotal + shippingFee;

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header title="SHOPPING BAG" showCart={false} />

      {items.length === 0 ? (
        <EmptyState
          icon="bag-outline"
          title="Your Bag is Empty"
          description="Explore our curated collection of luxury minimalist footwear and elevate your daily cadence."
          buttonText="Explore Footwear"
          onButtonPress={() => router.push('/(tabs)/shop')}
        />
      ) : (
        <View style={styles.container}>
          <ScrollView 
            style={styles.itemsList} 
            contentContainerStyle={styles.itemsContent}
            showsVerticalScrollIndicator={false}
          >
            {/* Packaging Notice */}
            <View style={[styles.noticeBox, { backgroundColor: colors.backgroundSubtle, borderColor: colors.border }]}>
              <Ionicons name="gift-outline" size={16} color={colors.accent} />
              <Text style={[styles.noticeText, { color: colors.textSecondary }]}>
                Includes complimentary custom linen shoe dust-bags and cedar wood shoehorn.
              </Text>
            </View>

            {/* Items */}
            {items.map((item) => {
              const variant = item.variant;
              const product = item.product || variant?.product;

              return (
                <View
                  key={item.id}
                  style={[
                    styles.itemCard,
                    {
                      backgroundColor: colors.card,
                      borderColor: colors.cardBorder,
                    },
                  ]}
                >
                  <Image
                    source={{ uri: product?.primary_image }}
                    style={[styles.itemImage, { backgroundColor: colors.imageBacking }]}
                    contentFit="cover"
                    transition={200}
                  />

                  <View style={styles.itemDetails}>
                    <View style={styles.itemHeader}>
                      <Text style={[styles.itemName, { color: colors.textPrimary }]} numberOfLines={1}>
                        {product?.name}
                      </Text>
                      <TouchableOpacity 
                        onPress={() => removeFromCart(item.id)}
                        hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
                      >
                        <Ionicons name="trash-outline" size={16} color={colors.textMuted} />
                      </TouchableOpacity>
                    </View>

                    <Text style={[styles.itemVariant, { color: colors.textMuted }]}>
                      Size: US {variant?.size} &bull; {variant?.color}
                    </Text>

                    <View style={styles.itemFooter}>
                      <Text style={[styles.itemPrice, { color: colors.textPrimary }]}>
                        {formatPrice(variant?.effective_price || 0)}
                      </Text>

                      {/* Quantity Stepper */}
                      <View style={[styles.stepper, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border }]}>
                        <TouchableOpacity
                          style={styles.stepBtn}
                          onPress={() => updateQuantity(item.id, item.quantity - 1)}
                          disabled={isLoading}
                        >
                          <Ionicons name="remove" size={14} color={colors.textPrimary} />
                        </TouchableOpacity>

                        <Text style={[styles.stepQty, { color: colors.textPrimary }]}>{item.quantity}</Text>

                        <TouchableOpacity
                          style={styles.stepBtn}
                          onPress={() => updateQuantity(item.id, item.quantity + 1)}
                          disabled={isLoading || (variant && item.quantity >= variant.stock_quantity)}
                        >
                          <Ionicons name="add" size={14} color={colors.textPrimary} />
                        </TouchableOpacity>
                      </View>
                    </View>
                  </View>
                </View>
              );
            })}

            {/* Guest Sign In Prompt Card */}
            {!isAuthenticated && (
              <TouchableOpacity
                style={[
                  styles.guestNoticeCard,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                  },
                ]}
                onPress={() => router.push({ pathname: '/auth/login', params: { redirect: '/checkout' } })}
                activeOpacity={0.85}
              >
                <View style={[styles.guestNoticeBadge, { backgroundColor: colors.monogramBg }]}>
                  <Text style={[styles.guestNoticeBadgeText, { color: colors.monogramText }]}>É</Text>
                </View>
                <View style={{ flex: 1 }}>
                  <Text style={[styles.guestNoticeTitle, { color: colors.textPrimary }]}>Client Sign In Required for Checkout</Text>
                  <Text style={[styles.guestNoticeSub, { color: colors.textSecondary }]}>
                    Sign in to access saved addresses, courier coordination, and reserve your footwear.
                  </Text>
                </View>
                <Ionicons name="chevron-forward" size={16} color={colors.accent} />
              </TouchableOpacity>
            )}

            {/* Financial Summary Card */}
            <View style={[styles.summaryCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
              <Text style={[styles.summaryTitle, { color: colors.accent }]}>ORDER SUMMARY</Text>
              
              <View style={styles.summaryRow}>
                <Text style={[styles.summaryLabel, { color: colors.textSecondary }]}>Subtotal</Text>
                <Text style={[styles.summaryValue, { color: colors.textPrimary }]}>{formatPrice(subtotal)}</Text>
              </View>

              <View style={styles.summaryRow}>
                <Text style={[styles.summaryLabel, { color: colors.textSecondary }]}>Complimentary Shipping</Text>
                <Text style={[styles.summaryValue, { color: colors.textPrimary }]}>
                  {shippingFee === 0 ? 'FREE' : formatPrice(shippingFee)}
                </Text>
              </View>

              <View style={[styles.summaryRow, styles.summaryTotalRow, { borderTopColor: colors.borderSubtle }]}>
                <Text style={[styles.totalLabel, { color: colors.textPrimary }]}>Estimated Total</Text>
                <Text style={[styles.totalValue, { color: colors.textPrimary }]}>{formatPrice(total)}</Text>
              </View>
            </View>
          </ScrollView>

          {/* Sticky Checkout CTA Bar */}
          <View style={[styles.checkoutBar, { backgroundColor: colors.surface, borderTopColor: colors.borderSubtle }]}>
            <View style={styles.checkoutBarPrice}>
              <Text style={[styles.checkoutBarLabel, { color: colors.textMuted }]}>Total Due</Text>
              <Text style={[styles.checkoutBarAmount, { color: colors.textPrimary }]}>{formatPrice(total)}</Text>
            </View>

            <TouchableOpacity
              style={[styles.checkoutButton, { backgroundColor: colors.textPrimary }]}
              onPress={() => {
                if (!isAuthenticated) {
                  Alert.alert(
                    'Client Sign In Required',
                    'To arrange white-glove delivery, order tracking, and authenticity certification, please sign in to your ÉLEVÉ client account.',
                    [
                      {
                        text: 'Sign In / Register',
                        onPress: () => router.push({ pathname: '/auth/login', params: { redirect: '/checkout' } }),
                      },
                      { text: 'Continue Browsing', style: 'cancel' },
                    ]
                  );
                  return;
                }
                router.push('/checkout');
              }}
              activeOpacity={0.85}
              disabled={isLoading}
            >
              <Text style={[styles.checkoutButtonText, { color: colors.background }]}>Proceed to Checkout</Text>
              <Ionicons name="arrow-forward" size={16} color={colors.background} />
            </TouchableOpacity>
          </View>
        </View>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: COLORS.ivory,
  },
  container: {
    flex: 1,
  },
  itemsList: {
    flex: 1,
  },
  itemsContent: {
    padding: 16,
    paddingBottom: 24,
  },
  noticeBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ECE8E1',
    borderWidth: 1,
    borderColor: '#D8D0C4',
    padding: 12,
    borderRadius: 8,
    marginBottom: 16,
    gap: 10,
  },
  noticeText: {
    flex: 1,
    fontSize: 11,
    color: COLORS.textSecondary,
    lineHeight: 16,
  },
  itemCard: {
    flexDirection: 'row',
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border,
    padding: 12,
    marginBottom: 12,
    gap: 12,
  },
  itemImage: {
    width: 80,
    height: 80,
    borderRadius: 8,
    backgroundColor: '#F3EFEA',
  },
  itemDetails: {
    flex: 1,
    justifyContent: 'space-between',
  },
  itemHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
  },
  itemName: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.obsidian,
    flex: 1,
    marginRight: 8,
    fontFamily: 'serif',
  },
  itemVariant: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginVertical: 4,
  },
  itemFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  itemPrice: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  stepper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F7F5F0',
    borderRadius: 6,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  stepBtn: {
    width: 28,
    height: 26,
    alignItems: 'center',
    justifyContent: 'center',
  },
  stepQty: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.obsidian,
    paddingHorizontal: 8,
  },
  summaryCard: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border,
    padding: 16,
    marginTop: 8,
    gap: 10,
  },
  summaryTitle: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 1.5,
    color: COLORS.textMuted,
    marginBottom: 4,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  summaryLabel: {
    fontSize: 12,
    color: COLORS.textSecondary,
  },
  summaryValue: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.obsidian,
  },
  summaryTotalRow: {
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
    paddingTop: 10,
    marginTop: 4,
  },
  totalLabel: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  totalValue: {
    fontSize: 16,
    fontWeight: '700',
    fontFamily: 'serif',
    color: COLORS.obsidian,
  },
  checkoutBar: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingVertical: 14,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 8,
  },
  checkoutBarPrice: {
    gap: 2,
  },
  checkoutBarLabel: {
    fontSize: 10,
    color: COLORS.textMuted,
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  checkoutBarAmount: {
    fontSize: 18,
    fontWeight: '700',
    fontFamily: 'serif',
    color: COLORS.obsidian,
  },
  checkoutButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.obsidian,
    paddingHorizontal: 20,
    paddingVertical: 14,
    borderRadius: 8,
    gap: 8,
  },
  checkoutButtonText: {
    color: COLORS.ivory,
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  guestNoticeCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 10,
    borderWidth: 1,
    marginBottom: 16,
    gap: 12,
  },
  guestNoticeBadge: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
  },
  guestNoticeBadgeText: {
    fontFamily: 'serif',
    fontSize: 15,
    fontWeight: 'bold',
  },
  guestNoticeTitle: {
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 0.3,
  },
  guestNoticeSub: {
    fontSize: 10.5,
    lineHeight: 15,
    marginTop: 2,
  },
});
