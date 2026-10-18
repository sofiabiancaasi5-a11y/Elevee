import React, { useState, useEffect, useCallback, useRef } from 'react';
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
import { useLocalSearchParams, useRouter } from 'expo-router';
import Header from '../../components/Header';
import OrderStatusTimeline from '../../components/OrderStatusTimeline';
import { api } from '../../services/api';
import { useNotifications } from '../../context/NotificationContext';
import { useTheme } from '../../context/ThemeContext';
import { COLORS, formatPrice, SHADOWS, GLASS } from '../../constants/theme';

export default function OrderTrackingScreen() {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const { showToast, setActiveTrackedOrder } = useNotifications();
  const { colors, isDark } = useTheme();
  const [order, setOrder] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isCancelling, setIsCancelling] = useState(false);
  const progressTimerRef = useRef(null);

  const fetchOrderDetails = useCallback(async () => {
    if (!id) return;
    try {
      setIsLoading(true);
      const res = await api.getOrder(id);
      if (res.data) {
        setOrder(res.data);
        setActiveTrackedOrder(res.data);
      }
    } catch (e) {
      console.warn('Failed to fetch order tracking:', e.message);
    } finally {
      setIsLoading(false);
    }
  }, [id, setActiveTrackedOrder]);

  useEffect(() => {
    fetchOrderDetails();
    return () => {
      setActiveTrackedOrder(null);
      if (progressTimerRef.current) {
        clearInterval(progressTimerRef.current);
      }
    };
  }, [fetchOrderDetails, setActiveTrackedOrder]);

  // 3-Second Automated Progression Loop
  useEffect(() => {
    if (!order) return;

    const activeStatuses = ['pending', 'confirmed', 'processing', 'shipped'];
    if (!activeStatuses.includes(order.status)) {
      if (progressTimerRef.current) {
        clearInterval(progressTimerRef.current);
      }
      return;
    }

    // Interval to advance order every 3 seconds
    progressTimerRef.current = setInterval(async () => {
      try {
        const res = await api.autoProgressOrder(order.order_number);
        if (res.data) {
          const updated = res.data;
          setOrder(updated);
          setActiveTrackedOrder(updated);

          // Trigger luxury floating toast alert
          const notifCopy = getStatusToast(updated.status, updated.order_number);
          showToast(notifCopy.title, notifCopy.message, 'order_status');

          if (updated.status === 'delivered') {
            clearInterval(progressTimerRef.current);
          }
        }
      } catch (err) {
        // Quiet failover if network or terminal state reached
      }
    }, 3000);

    return () => {
      if (progressTimerRef.current) {
        clearInterval(progressTimerRef.current);
      }
    };
  }, [order?.status, order?.order_number, setActiveTrackedOrder, showToast]);

  const handleCancelOrder = () => {
    Alert.alert(
      'Cancel Order',
      'Are you certain you wish to cancel this footwear order? Your reserved pairs will be returned to inventory.',
      [
        { text: 'Keep Order', style: 'cancel' },
        {
          text: 'Confirm Cancellation',
          style: 'destructive',
          onPress: async () => {
            try {
              setIsCancelling(true);
              const res = await api.cancelOrder(order.order_number, 'Customer cancelled via mobile app');
              if (res.data) {
                setOrder(res.data);
                showToast('Order Cancelled', 'Your order was cancelled and variant stock was restored.');
              }
            } catch (err) {
              Alert.alert('Cancellation Error', err.message || 'Could not cancel order.');
            } finally {
              setIsCancelling(false);
            }
          },
        },
      ]
    );
  };

  if (isLoading) {
    return (
      <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
        <Header showBack={true} title="TRACKING" />
        <View style={styles.center}>
          <ActivityIndicator size="large" color={colors.accent} />
          <Text style={[styles.loadingText, { color: colors.textSecondary }]}>Connecting to atelier live tracking...</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (!order) {
    return (
      <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
        <Header showBack={true} title="TRACKING" />
        <View style={styles.center}>
          <Text style={[styles.errorTitle, { color: colors.textPrimary }]}>Order Reference Not Found</Text>
          <TouchableOpacity
            style={[styles.returnBtn, { backgroundColor: colors.textPrimary }]}
            onPress={() => router.push('/(tabs)')}
          >
            <Text style={[styles.returnBtnText, { color: colors.background }]}>Return to Boutique</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  const addr = order.shipping_address || {};
  const isDelivered = order.status === 'delivered';

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header showBack={true} title="LIVE ORDER TRACKING" />

      <ScrollView
        style={[styles.container, { backgroundColor: colors.background }]}
        contentContainerStyle={styles.content}
        showsVerticalScrollIndicator={false}
      >
        {/* Status Header */}
        <View style={[styles.statusHeader, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <View style={styles.badgeRow}>
            <Text style={[styles.orderLabel, { color: colors.accent }]}>ORDER REFERENCE</Text>
            <View style={[styles.statusPill, { backgroundColor: getStatusBg(order.status) }]}>
              <Text style={[styles.statusPillText, { color: getStatusText(order.status) }]}>
                {order.status.toUpperCase()}
              </Text>
            </View>
          </View>
          <Text style={[styles.orderNumber, { color: colors.textPrimary }]}>{order.order_number}</Text>
          <Text style={[styles.orderDate, { color: colors.textMuted }]}>Placed on {order.formatted_date || order.created_at}</Text>
        </View>

        {/* Celebratory Delivered Banner */}
        {isDelivered && (
          <View style={styles.celebrationCard}>
            <View style={styles.celebrationIcon}>
              <Ionicons name="sparkles" size={24} color="#C5A880" />
            </View>
            <Text style={styles.celebrationTitle}>Every Step, Elevated.</Text>
            <Text style={styles.celebrationSubtitle}>
              Your bespoke footwear parcel has been successfully delivered. We hope every stride brings effortless poise and elegance.
            </Text>
          </View>
        )}

        {/* Visual Timeline Tracker */}
        <OrderStatusTimeline status={order.status} />

        {/* Ordered Footwear Items */}
        <View style={[styles.card, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <Text style={[styles.cardTitle, { color: colors.textPrimary }]}>ORDERED SILHOUETTES ({order.items?.length || 0})</Text>

          <View style={styles.itemsList}>
            {order.items?.map((item) => (
              <View key={item.id} style={styles.itemRow}>
                <Image
                  source={{ uri: item.image_url }}
                  style={styles.itemImage}
                  contentFit="cover"
                  transition={200}
                />
                <View style={styles.itemInfo}>
                  <Text style={[styles.itemName, { color: colors.textPrimary }]}>{item.product_name}</Text>
                  <Text style={[styles.itemMeta, { color: colors.textSecondary }]}>Size: US {item.size} • Shade: {item.color}</Text>
                  <Text style={[styles.itemQuantity, { color: colors.textMuted }]}>Quantity: {item.quantity}</Text>
                </View>
                <Text style={[styles.itemTotal, { color: colors.textPrimary }]}>{formatPrice(item.total_price)}</Text>
              </View>
            ))}
          </View>
        </View>

        {/* Delivery Address */}
        <View style={[styles.card, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <Text style={[styles.cardTitle, { color: colors.textPrimary }]}>DELIVERY DESTINATION</Text>
          <Text style={[styles.destName, { color: colors.textPrimary }]}>{order.customer_name}</Text>
          <Text style={[styles.destAddress, { color: colors.textSecondary }]}>
            {addr.apartment ? `${addr.apartment}, ` : ''}{addr.street_address}
          </Text>
          <Text style={[styles.destCity, { color: colors.textSecondary }]}>
            {addr.city}, {addr.province_state} {addr.postal_code}
          </Text>
          <Text style={[styles.destPhone, { color: colors.textMuted }]}>Contact: {order.customer_phone}</Text>
        </View>

        {/* Financial Summary */}
        <View style={[styles.card, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
          <Text style={[styles.cardTitle, { color: colors.textPrimary }]}>PAYMENT SETTLEMENT</Text>

          <View style={styles.payRow}>
            <Text style={[styles.payLabel, { color: colors.textSecondary }]}>Payment Method</Text>
            <Text style={[styles.payValue, { color: colors.textPrimary }]}>{order.payment?.method?.toUpperCase() || 'COD'}</Text>
          </View>

          <View style={styles.payRow}>
            <Text style={[styles.payLabel, { color: colors.textSecondary }]}>Payment Status</Text>
            <Text style={[styles.payValue, { color: order.payment?.status === 'completed' ? '#065F46' : '#92400E' }]}>
              {order.payment?.status?.toUpperCase() || 'PENDING'}
            </Text>
          </View>

          <View style={styles.payRow}>
            <Text style={[styles.payLabel, { color: colors.textSecondary }]}>Items Subtotal</Text>
            <Text style={[styles.payValue, { color: colors.textPrimary }]}>{formatPrice(order.subtotal)}</Text>
          </View>

          <View style={styles.payRow}>
            <Text style={[styles.payLabel, { color: colors.textSecondary }]}>White-Glove Shipping</Text>
            <Text style={[styles.payValue, { color: colors.textPrimary }]}>
              {Number(order.shipping_fee) === 0 ? 'COMPLIMENTARY' : formatPrice(order.shipping_fee)}
            </Text>
          </View>

          <View style={[styles.payRow, styles.totalPayRow]}>
            <Text style={[styles.totalPayLabel, { color: colors.textPrimary }]}>Total Settled</Text>
            <Text style={[styles.totalPayValue, { color: colors.accent }]}>{formatPrice(order.total_amount)}</Text>
          </View>
        </View>

        {/* Cancellation Option (if pending or confirmed) */}
        {order.can_cancel && (
          <TouchableOpacity
            style={styles.cancelBtn}
            onPress={handleCancelOrder}
            disabled={isCancelling}
            activeOpacity={0.8}
          >
            {isCancelling ? (
              <ActivityIndicator size="small" color="#DC2626" />
            ) : (
              <>
                <Ionicons name="close-circle-outline" size={18} color="#DC2626" />
                <Text style={styles.cancelBtnText}>Cancel Order & Release Footwear</Text>
              </>
            )}
          </TouchableOpacity>
        )}

        <TouchableOpacity
          style={[styles.continueBtn, { backgroundColor: colors.textPrimary }]}
          onPress={() => router.push('/(tabs)')}
          activeOpacity={0.85}
        >
          <Text style={[styles.continueBtnText, { color: colors.background }]}>Return to Boutique</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

function getStatusToast(status, orderNumber) {
  switch (status) {
    case 'confirmed':
      return {
        title: 'Order Confirmed',
        message: `Atelier verified order #${orderNumber}. Handcrafting starting soon.`,
      };
    case 'processing':
      return {
        title: 'Atelier Handcrafting',
        message: `Shoemakers are assembling and inspecting your pair at our atelier.`,
      };
    case 'shipped':
      return {
        title: 'Dispatched in Transit',
        message: `Your bespoke parcel is now in transit with our courier.`,
      };
    case 'delivered':
      return {
        title: 'Delivered',
        message: `Your luxury Élevé footwear has arrived. Enjoy every step.`,
      };
    default:
      return {
        title: 'Order Update',
        message: `Status updated to ${status}.`,
      };
  }
}

function getStatusBg(status) {
  switch (status) {
    case 'pending': return '#FEF3C7';
    case 'confirmed': return '#DBEAFE';
    case 'processing': return '#F3E8FF';
    case 'shipped': return '#E0E7FF';
    case 'delivered': return '#D1FAE5';
    case 'cancelled': return '#FEE2E2';
    default: return '#F3F4F6';
  }
}

function getStatusText(status) {
  switch (status) {
    case 'pending': return '#92400E';
    case 'confirmed': return '#1E40AF';
    case 'processing': return '#6B21A8';
    case 'shipped': return '#3730A3';
    case 'delivered': return '#065F46';
    case 'cancelled': return '#991B1B';
    default: return '#374151';
  }
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: COLORS.ivory,
  },
  container: {
    flex: 1,
  },
  content: {
    padding: 16,
    paddingBottom: 40,
    gap: 16,
  },
  center: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 30,
    gap: 12,
  },
  loadingText: {
    fontSize: 12,
    color: COLORS.textMuted,
  },
  errorTitle: {
    fontFamily: 'serif',
    fontSize: 20,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  returnBtn: {
    marginTop: 12,
    paddingVertical: 12,
    paddingHorizontal: 24,
    backgroundColor: COLORS.obsidian,
    borderRadius: 8,
  },
  returnBtnText: {
    color: '#FFFFFF',
    fontWeight: '700',
    fontSize: 13,
  },
  statusHeader: {
    backgroundColor: '#FFFFFF',
    padding: 18,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.subtle,
  },
  badgeRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  orderLabel: {
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 1.5,
    color: COLORS.textMuted,
  },
  statusPill: {
    paddingHorizontal: 10,
    paddingVertical: 3,
    borderRadius: 12,
  },
  statusPillText: {
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 0.5,
  },
  orderNumber: {
    fontSize: 18,
    fontWeight: '800',
    color: COLORS.obsidian,
    fontFamily: 'monospace',
    letterSpacing: 0.5,
  },
  orderDate: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginTop: 2,
  },
  celebrationCard: {
    backgroundColor: '#171717',
    padding: 20,
    borderRadius: 18,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#C5A880',
    ...SHADOWS.floating,
  },
  celebrationIcon: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(197, 168, 128, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 10,
  },
  celebrationTitle: {
    fontFamily: 'serif',
    fontSize: 18,
    fontWeight: '700',
    color: '#FFFFFF',
    marginBottom: 6,
  },
  celebrationSubtitle: {
    fontSize: 12,
    color: '#D8D0C4',
    textAlign: 'center',
    lineHeight: 18,
  },
  card: {
    backgroundColor: '#FFFFFF',
    padding: 18,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: COLORS.border,
    ...SHADOWS.subtle,
  },
  cardTitle: {
    fontSize: 10,
    fontWeight: '800',
    letterSpacing: 1.5,
    color: COLORS.textMuted,
    marginBottom: 14,
  },
  itemsList: {
    gap: 12,
  },
  itemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
    paddingBottom: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#F7F5F0',
  },
  itemImage: {
    width: 54,
    height: 54,
    borderRadius: 8,
    backgroundColor: '#ECE8E1',
  },
  itemInfo: {
    flex: 1,
  },
  itemName: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  itemMeta: {
    fontSize: 11,
    color: COLORS.textSecondary,
    marginTop: 2,
  },
  itemQuantity: {
    fontSize: 11,
    color: COLORS.textMuted,
    marginTop: 1,
  },
  itemTotal: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  destName: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  destAddress: {
    fontSize: 12,
    color: COLORS.textSecondary,
    marginTop: 4,
    lineHeight: 18,
  },
  destCity: {
    fontSize: 12,
    color: COLORS.textSecondary,
    lineHeight: 18,
  },
  destPhone: {
    fontSize: 12,
    color: COLORS.textMuted,
    marginTop: 6,
  },
  payRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 5,
  },
  payLabel: {
    fontSize: 12,
    color: COLORS.textSecondary,
  },
  payValue: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.obsidian,
  },
  totalPayRow: {
    borderTopWidth: 1,
    borderTopColor: '#F0ECE4',
    paddingTop: 10,
    marginTop: 6,
  },
  totalPayLabel: {
    fontSize: 14,
    fontWeight: '800',
    color: COLORS.obsidian,
  },
  totalPayValue: {
    fontSize: 16,
    fontWeight: '800',
    color: COLORS.obsidian,
  },
  cancelBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 14,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#FCA5A5',
    backgroundColor: '#FFF5F5',
    gap: 8,
  },
  cancelBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: '#DC2626',
  },
  continueBtn: {
    paddingVertical: 16,
    borderRadius: 14,
    backgroundColor: COLORS.obsidian,
    alignItems: 'center',
    justifyContent: 'center',
    ...SHADOWS.subtle,
  },
  continueBtnText: {
    color: '#FFFFFF',
    fontSize: 13,
    fontWeight: '700',
    letterSpacing: 1,
    textTransform: 'uppercase',
  },
});
