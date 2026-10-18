import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import Header from '../components/Header';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';
import { useTheme } from '../context/ThemeContext';
import { api } from '../services/api';
import { COLORS, formatPrice } from '../constants/theme';

const PAYMENT_METHODS = [
  { id: 'cod', label: 'Cash on Delivery (COD)', desc: 'Pay discreetly upon white-glove arrival', icon: 'cash-outline' },
  { id: 'card', label: 'Credit / Debit Card', desc: 'Visa, Mastercard, American Express', icon: 'card-outline' },
  { id: 'gcash', label: 'GCash / E-Wallet', desc: 'Instant mobile settlement', icon: 'phone-portrait-outline' },
  { id: 'bank_transfer', label: 'Direct Bank Wire', desc: 'BDO, BPI, UnionBank luxury desk', icon: 'business-outline' },
];

export default function CheckoutScreen() {
  const router = useRouter();
  const { cart, clearLocalCart } = useCart();
  const { user, isAuthenticated, isLoading: isAuthLoading } = useAuth();
  const { colors, isDark } = useTheme();

  const [customerName, setCustomerName] = useState('');
  const [customerEmail, setCustomerEmail] = useState('');
  const [customerPhone, setCustomerPhone] = useState('');
  const [streetAddress, setStreetAddress] = useState('');
  const [apartment, setApartment] = useState('');
  const [city, setCity] = useState('Makati City');
  const [provinceState, setProvinceState] = useState('Metro Manila');
  const [postalCode, setPostalCode] = useState('1229');
  const [notes, setNotes] = useState('');
  const [paymentMethod, setPaymentMethod] = useState('cod');
  const [isSubmitting, setIsSubmitting] = useState(false);

  // Synchronize authenticated user details & default address
  useEffect(() => {
    if (user) {
      if (!customerName && user.name) setCustomerName(user.name);
      if (!customerEmail && user.email) setCustomerEmail(user.email);
      if (!customerPhone && user.phone) setCustomerPhone(user.phone);

      const defaultAddr = user.addresses?.find((a) => a.is_default) || user.addresses?.[0];
      if (defaultAddr) {
        if (!streetAddress && defaultAddr.street_address) setStreetAddress(defaultAddr.street_address);
        if (!apartment && defaultAddr.apartment) setApartment(defaultAddr.apartment);
        if (defaultAddr.city) setCity(defaultAddr.city);
        if (defaultAddr.province_state) setProvinceState(defaultAddr.province_state);
        if (defaultAddr.postal_code) setPostalCode(defaultAddr.postal_code);
        if (!customerPhone && defaultAddr.phone_number) setCustomerPhone(defaultAddr.phone_number);
      }
    }
  }, [user]);

  const items = cart?.items || [];
  const subtotal = cart?.subtotal || 0;
  const shippingFee = subtotal >= 5000 ? 0.0 : 250.0;
  const total = subtotal + shippingFee;

  const handleSubmitOrder = async () => {
    // 1. Mandatory Login Guard
    if (!isAuthenticated) {
      Alert.alert(
        'Authentication Required',
        'You must be signed in to your ÉLEVÉ client account to place an order.',
        [
          {
            text: 'Sign In / Register',
            onPress: () => router.push({ pathname: '/auth/login', params: { redirect: '/checkout' } }),
          },
          { text: 'Cancel', style: 'cancel' },
        ]
      );
      return;
    }

    // 2. Shipping Details Validation
    if (!customerName.trim() || !customerEmail.trim() || !customerPhone.trim() || !streetAddress.trim() || !city.trim()) {
      Alert.alert('Incomplete Delivery Details', 'Please complete all required shipping fields.');
      return;
    }

    if (items.length === 0) {
      Alert.alert('Empty Bag', 'No footwear items found in bag.');
      return;
    }

    try {
      setIsSubmitting(true);

      const orderPayload = {
        customer_name: customerName.trim(),
        customer_email: customerEmail.trim(),
        customer_phone: customerPhone.trim(),
        shipping_address: {
          recipient_name: customerName.trim(),
          phone_number: customerPhone.trim(),
          street_address: streetAddress.trim(),
          apartment: apartment.trim(),
          city: city.trim(),
          province_state: provinceState.trim(),
          postal_code: postalCode.trim(),
          country: 'Philippines',
        },
        items: items.map((it) => ({
          product_variant_id: it.variant ? it.variant.id : it.product_variant_id,
          quantity: it.quantity,
        })),
        payment_method: paymentMethod,
        notes: notes.trim(),
      };

      const res = await api.placeOrder(orderPayload);

      if (res.data) {
        clearLocalCart();
        const createdOrder = res.data;
        router.replace({
          pathname: '/order/[id]',
          params: { id: createdOrder.order_number },
        });
      }
    } catch (err) {
      Alert.alert('Order Placement Error', err.message || 'Could not place order. Please check variant stock.');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Luxury Mandatory Authentication Gate
  if (!isAuthenticated && !isAuthLoading) {
    return (
      <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
        <Header title="CHECKOUT" showBack={true} showCart={false} />

        <View style={styles.authGateContainer}>
          <View style={[styles.authGateCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
            <View style={[styles.authGateMonogram, { backgroundColor: colors.monogramBg }]}>
              <Text style={[styles.authGateMonogramText, { color: colors.monogramText }]}>É</Text>
            </View>

            <Text style={[styles.authGateTitle, { color: colors.textPrimary }]}>Client Sign In Required</Text>
            <Text style={[styles.authGateSubtitle, { color: colors.textSecondary }]}>
              An ÉLEVÉ patron account is required to reserve footwear, verify craftsmanship authenticity, and arrange white-glove courier delivery.
            </Text>

            <View style={styles.authGatePerks}>
              <View style={styles.authGatePerkRow}>
                <Ionicons name="shield-checkmark-outline" size={17} color={colors.accent} />
                <Text style={[styles.authGatePerkText, { color: colors.textPrimary }]}>
                  Authenticity certification & bespoke craftsmanship records
                </Text>
              </View>
              <View style={styles.authGatePerkRow}>
                <Ionicons name="car-outline" size={17} color={colors.accent} />
                <Text style={[styles.authGatePerkText, { color: colors.textPrimary }]}>
                  Live atelier crafting progression & white-glove courier updates
                </Text>
              </View>
              <View style={styles.authGatePerkRow}>
                <Ionicons name="bag-check-outline" size={17} color={colors.accent} />
                <Text style={[styles.authGatePerkText, { color: colors.textPrimary }]}>
                  Saved bag preservation & seamless one-tap checkout
                </Text>
              </View>
            </View>

            <TouchableOpacity
              style={[styles.authGatePrimaryBtn, { backgroundColor: colors.textPrimary }]}
              onPress={() => router.push({ pathname: '/auth/login', params: { redirect: '/checkout' } })}
              activeOpacity={0.85}
            >
              <Ionicons name="log-in-outline" size={17} color={colors.background} />
              <Text style={[styles.authGatePrimaryBtnText, { color: colors.background }]}>Sign In to Client Account</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.authGateSecondaryBtn, { borderColor: colors.border }]}
              onPress={() => router.push({ pathname: '/auth/register', params: { redirect: '/checkout' } })}
              activeOpacity={0.85}
            >
              <Text style={[styles.authGateSecondaryBtnText, { color: colors.textPrimary }]}>Create Atelier Account</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.authGateBackBtn}
              onPress={() => router.back()}
              activeOpacity={0.8}
            >
              <Ionicons name="arrow-back" size={14} color={colors.textMuted} />
              <Text style={[styles.authGateBackBtnText, { color: colors.textMuted }]}>Return to Shopping Bag</Text>
            </TouchableOpacity>
          </View>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header title="CHECKOUT" showBack={true} showCart={false} />

      <ScrollView style={styles.container} contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
        {/* Step 1: Client & Shipping Destination */}
        <View style={[styles.card, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <View style={[styles.cardHeader, { borderBottomColor: colors.borderSubtle }]}>
            <Text style={[styles.stepNum, { backgroundColor: colors.textPrimary, color: colors.background }]}>1</Text>
            <Text style={[styles.cardTitle, { color: colors.textPrimary }]}>Shipping Destination</Text>
          </View>

          <View style={styles.formGroup}>
            <Text style={[styles.inputLabel, { color: colors.textMuted }]}>FULL NAME *</Text>
            <TextInput
              style={[styles.input, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border, color: colors.textPrimary }]}
              value={customerName}
              onChangeText={setCustomerName}
              placeholder="Sophia Laurent"
              placeholderTextColor={colors.textMuted}
            />
          </View>

          <View style={styles.row}>
            <View style={[styles.formGroup, { flex: 1 }]}>
              <Text style={[styles.inputLabel, { color: colors.textMuted }]}>EMAIL *</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border, color: colors.textPrimary }]}
                value={customerEmail}
                onChangeText={setCustomerEmail}
                placeholder="client@eleve.com"
                keyboardType="email-address"
                autoCapitalize="none"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <View style={[styles.formGroup, { flex: 1 }]}>
              <Text style={[styles.inputLabel, { color: colors.textMuted }]}>CONTACT PHONE *</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border, color: colors.textPrimary }]}
                value={customerPhone}
                onChangeText={setCustomerPhone}
                placeholder="+63 917 000 0000"
                keyboardType="phone-pad"
                placeholderTextColor={colors.textMuted}
              />
            </View>
          </View>

          <View style={styles.formGroup}>
            <Text style={[styles.inputLabel, { color: colors.textMuted }]}>STREET ADDRESS *</Text>
            <TextInput
              style={[styles.input, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border, color: colors.textPrimary }]}
              value={streetAddress}
              onChangeText={setStreetAddress}
              placeholder="e.g. 128 Paseo de Roxas"
              placeholderTextColor={colors.textMuted}
            />
          </View>

          <View style={styles.formGroup}>
            <Text style={[styles.inputLabel, { color: colors.textMuted }]}>SUITE / APARTMENT / BUILDING (OPTIONAL)</Text>
            <TextInput
              style={[styles.input, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border, color: colors.textPrimary }]}
              value={apartment}
              onChangeText={setApartment}
              placeholder="e.g. Penthouse 3A, The Residences"
              placeholderTextColor={colors.textMuted}
            />
          </View>

          <View style={styles.row}>
            <View style={[styles.formGroup, { flex: 1 }]}>
              <Text style={[styles.inputLabel, { color: colors.textMuted }]}>CITY *</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border, color: colors.textPrimary }]}
                value={city}
                onChangeText={setCity}
                placeholder="Makati City"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <View style={[styles.formGroup, { flex: 1 }]}>
              <Text style={[styles.inputLabel, { color: colors.textMuted }]}>POSTAL CODE *</Text>
              <TextInput
                style={[styles.input, { backgroundColor: colors.surfaceSubtle, borderColor: colors.border, color: colors.textPrimary }]}
                value={postalCode}
                onChangeText={setPostalCode}
                placeholder="1229"
                keyboardType="numeric"
                placeholderTextColor={colors.textMuted}
              />
            </View>
          </View>
        </View>

        {/* Step 2: Payment Method */}
        <View style={[styles.card, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <View style={[styles.cardHeader, { borderBottomColor: colors.borderSubtle }]}>
            <Text style={[styles.stepNum, { backgroundColor: colors.textPrimary, color: colors.background }]}>2</Text>
            <Text style={[styles.cardTitle, { color: colors.textPrimary }]}>Payment Method</Text>
          </View>

          <View style={styles.paymentOptions}>
            {PAYMENT_METHODS.map((method) => {
              const isSelected = paymentMethod === method.id;
              return (
                <TouchableOpacity
                  key={method.id}
                  style={[
                    styles.paymentCard,
                    {
                      backgroundColor: isSelected ? colors.surfaceSubtle : colors.card,
                      borderColor: isSelected ? colors.accent : colors.border,
                    },
                  ]}
                  onPress={() => setPaymentMethod(method.id)}
                  activeOpacity={0.8}
                >
                  <View
                    style={[
                      styles.paymentRadio,
                      { borderColor: isSelected ? colors.accent : colors.border },
                    ]}
                  >
                    {isSelected && <View style={[styles.paymentRadioInner, { backgroundColor: colors.accent }]} />}
                  </View>

                  <View style={styles.paymentInfo}>
                    <Text style={[styles.paymentLabel, { color: colors.textPrimary }]}>{method.label}</Text>
                    <Text style={[styles.paymentDesc, { color: colors.textMuted }]}>{method.desc}</Text>
                  </View>

                  <Ionicons
                    name={method.icon}
                    size={20}
                    color={isSelected ? colors.accent : colors.textMuted}
                  />
                </TouchableOpacity>
              );
            })}
          </View>
        </View>

        {/* Step 3: Special Delivery Request */}
        <View style={[styles.card, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <View style={[styles.cardHeader, { borderBottomColor: colors.borderSubtle }]}>
            <Text style={[styles.stepNum, { backgroundColor: colors.textPrimary, color: colors.background }]}>3</Text>
            <Text style={[styles.cardTitle, { color: colors.textPrimary }]}>Atelier Notes & Instructions</Text>
          </View>
          <TextInput
            style={[
              styles.input,
              {
                backgroundColor: colors.surfaceSubtle,
                borderColor: colors.border,
                color: colors.textPrimary,
                height: 70,
                textAlignVertical: 'top',
              },
            ]}
            value={notes}
            onChangeText={setNotes}
            placeholder="Special delivery instructions, gate pass code, or gift note..."
            multiline
            placeholderTextColor={colors.textMuted}
          />
        </View>

        {/* Order Summary */}
        <View style={[styles.summaryCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <Text style={[styles.summaryTitle, { color: colors.accent }]}>FINAL SUMMARY ({items.length} ITEMS)</Text>

          <View style={styles.summaryRow}>
            <Text style={[styles.summaryLabel, { color: colors.textSecondary }]}>Footwear Subtotal</Text>
            <Text style={[styles.summaryValue, { color: colors.textPrimary }]}>{formatPrice(subtotal)}</Text>
          </View>

          <View style={styles.summaryRow}>
            <Text style={[styles.summaryLabel, { color: colors.textSecondary }]}>Complimentary Shipping</Text>
            <Text style={[styles.summaryValue, { color: colors.textPrimary }]}>
              {shippingFee === 0 ? 'FREE' : formatPrice(shippingFee)}
            </Text>
          </View>

          <View style={[styles.summaryRow, styles.summaryTotalRow, { borderTopColor: colors.borderSubtle }]}>
            <Text style={[styles.totalLabel, { color: colors.textPrimary }]}>Total Due</Text>
            <Text style={[styles.totalValue, { color: colors.textPrimary }]}>{formatPrice(total)}</Text>
          </View>
        </View>
      </ScrollView>

      {/* Submit Order Button */}
      <View style={[styles.bottomBar, { backgroundColor: colors.surface, borderTopColor: colors.borderSubtle }]}>
        <TouchableOpacity
          style={[styles.submitButton, { backgroundColor: colors.textPrimary }]}
          onPress={handleSubmitOrder}
          disabled={isSubmitting}
          activeOpacity={0.85}
        >
          {isSubmitting ? (
            <ActivityIndicator size="small" color={colors.background} />
          ) : (
            <>
              <Text style={[styles.submitButtonText, { color: colors.background }]}>Authorize & Place Order</Text>
              <Text style={[styles.submitButtonAmount, { color: colors.accent }]}>&bull; {formatPrice(total)}</Text>
            </>
          )}
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
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
    gap: 16,
  },
  authGateContainer: {
    flex: 1,
    padding: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  authGateCard: {
    width: '100%',
    maxWidth: 420,
    borderRadius: 16,
    borderWidth: 1,
    padding: 24,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.12,
    shadowRadius: 12,
    elevation: 4,
  },
  authGateMonogram: {
    width: 60,
    height: 60,
    borderRadius: 30,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
  },
  authGateMonogramText: {
    fontFamily: 'serif',
    fontSize: 28,
    fontWeight: 'bold',
  },
  authGateTitle: {
    fontFamily: 'serif',
    fontSize: 19,
    fontWeight: '700',
    letterSpacing: 0.5,
    textAlign: 'center',
    marginBottom: 8,
  },
  authGateSubtitle: {
    fontSize: 12.5,
    lineHeight: 18,
    textAlign: 'center',
    marginBottom: 20,
    paddingHorizontal: 10,
  },
  authGatePerks: {
    width: '100%',
    paddingVertical: 14,
    borderTopWidth: 1,
    borderBottomWidth: 1,
    borderColor: 'rgba(197, 168, 128, 0.2)',
    marginBottom: 22,
    gap: 10,
  },
  authGatePerkRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  authGatePerkText: {
    flex: 1,
    fontSize: 11.5,
    fontWeight: '500',
  },
  authGatePrimaryBtn: {
    width: '100%',
    height: 48,
    borderRadius: 8,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    marginBottom: 10,
  },
  authGatePrimaryBtnText: {
    fontSize: 12.5,
    fontWeight: '700',
    letterSpacing: 1,
    textTransform: 'uppercase',
  },
  authGateSecondaryBtn: {
    width: '100%',
    height: 48,
    borderRadius: 8,
    borderWidth: 1,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
  },
  authGateSecondaryBtnText: {
    fontSize: 12.5,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
  authGateBackBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    paddingVertical: 6,
  },
  authGateBackBtnText: {
    fontSize: 11.5,
    fontWeight: '600',
  },
  card: {
    borderRadius: 12,
    borderWidth: 1,
    padding: 18,
    gap: 12,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    paddingBottom: 8,
    borderBottomWidth: 1,
  },
  stepNum: {
    width: 22,
    height: 22,
    borderRadius: 11,
    textAlign: 'center',
    fontSize: 11,
    fontWeight: 'bold',
    lineHeight: 22,
  },
  cardTitle: {
    fontFamily: 'serif',
    fontSize: 15,
    fontWeight: '700',
  },
  formGroup: {
    gap: 4,
  },
  row: {
    flexDirection: 'row',
    gap: 10,
  },
  inputLabel: {
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 1,
  },
  input: {
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 13,
  },
  paymentOptions: {
    gap: 10,
  },
  paymentCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 14,
    borderRadius: 10,
    borderWidth: 1,
    gap: 12,
  },
  paymentRadio: {
    width: 18,
    height: 18,
    borderRadius: 9,
    borderWidth: 1.5,
    alignItems: 'center',
    justifyContent: 'center',
  },
  paymentRadioInner: {
    width: 10,
    height: 10,
    borderRadius: 5,
  },
  paymentInfo: {
    flex: 1,
  },
  paymentLabel: {
    fontSize: 13,
    fontWeight: '600',
  },
  paymentDesc: {
    fontSize: 11,
    marginTop: 2,
  },
  summaryCard: {
    borderRadius: 12,
    borderWidth: 1,
    padding: 16,
    gap: 8,
  },
  summaryTitle: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 1.5,
    marginBottom: 6,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  summaryLabel: {
    fontSize: 12,
  },
  summaryValue: {
    fontSize: 12,
    fontWeight: '600',
  },
  summaryTotalRow: {
    borderTopWidth: 1,
    paddingTop: 10,
    marginTop: 4,
  },
  totalLabel: {
    fontSize: 13,
    fontWeight: '700',
  },
  totalValue: {
    fontFamily: 'serif',
    fontSize: 17,
    fontWeight: '700',
  },
  bottomBar: {
    paddingHorizontal: 20,
    paddingVertical: 14,
    borderTopWidth: 1,
  },
  submitButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    height: 52,
    borderRadius: 8,
    gap: 6,
  },
  submitButtonText: {
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  submitButtonAmount: {
    fontSize: 12,
    fontWeight: '700',
  },
});
