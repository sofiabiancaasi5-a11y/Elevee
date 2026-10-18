import React, { useEffect, useRef } from 'react';
import { View, Text, StyleSheet, Animated } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { COLORS, SHADOWS } from '../constants/theme';

const STAGES = [
  { key: 'pending', title: 'Placed', subtitle: 'Received', icon: 'receipt-outline' },
  { key: 'confirmed', title: 'Confirmed', subtitle: 'Verified', icon: 'checkmark-circle-outline' },
  { key: 'processing', title: 'Atelier', subtitle: 'Crafting', icon: 'cube-outline' },
  { key: 'shipped', title: 'In Transit', subtitle: 'Courier', icon: 'airplane-outline' },
  { key: 'delivered', title: 'Delivered', subtitle: 'Arrived', icon: 'home-outline' },
];

const STAGE_ORDER = {
  pending: 1,
  confirmed: 2,
  processing: 3,
  shipped: 4,
  delivered: 5,
};

export default function OrderStatusTimeline({ status = 'pending' }) {
  const pulseAnim = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    if (status !== 'delivered' && status !== 'cancelled') {
      const pulse = Animated.loop(
        Animated.sequence([
          Animated.timing(pulseAnim, {
            toValue: 1.15,
            duration: 800,
            useNativeDriver: true,
          }),
          Animated.timing(pulseAnim, {
            toValue: 1,
            duration: 800,
            useNativeDriver: true,
          }),
        ])
      );
      pulse.start();
      return () => pulse.stop();
    }
  }, [status, pulseAnim]);

  if (status === 'cancelled') {
    return (
      <View style={styles.cancelledBox}>
        <Ionicons name="close-circle" size={24} color="#DC2626" />
        <View style={styles.cancelledTextContainer}>
          <Text style={styles.cancelledTitle}>Order Cancelled</Text>
          <Text style={styles.cancelledSubtitle}>This order was cancelled and variant stock was restored.</Text>
        </View>
      </View>
    );
  }

  const currentLevel = STAGE_ORDER[status] || 1;
  const isDelivered = status === 'delivered';

  return (
    <View style={styles.container}>
      <View style={styles.headerRow}>
        <Text style={styles.sectionHeader}>DELIVERY TIMELINE</Text>
        {!isDelivered ? (
          <View style={styles.liveBadge}>
            <View style={styles.liveDot} />
            <Text style={styles.liveText}>3s Auto-Progression</Text>
          </View>
        ) : (
          <View style={styles.completeBadge}>
            <Ionicons name="checkmark" size={10} color="#1B8755" />
            <Text style={styles.completeText}>Fulfilled</Text>
          </View>
        )}
      </View>
      
      <View style={styles.timelineRow}>
        {STAGES.map((stage, index) => {
          const stepNum = index + 1;
          const isDone = currentLevel >= stepNum;
          const isCurrent = currentLevel === stepNum;
          const hasNext = index < STAGES.length - 1;
          const nextDone = currentLevel > stepNum;

          return (
            <React.Fragment key={stage.key}>
              <View style={styles.nodeWrapper}>
                <Animated.View
                  style={[
                    styles.nodeCircle,
                    isDone && styles.nodeCircleDone,
                    isCurrent && styles.nodeCircleCurrent,
                    isCurrent && { transform: [{ scale: pulseAnim }] },
                  ]}
                >
                  <Ionicons
                    name={isDone ? (isCurrent ? stage.icon : 'checkmark') : stage.icon}
                    size={13}
                    color={isDone ? COLORS.ivory : COLORS.textMuted}
                  />
                </Animated.View>
                <Text style={[
                  styles.nodeTitle,
                  isCurrent && styles.nodeTitleCurrent,
                  isDone && !isCurrent && styles.nodeTitleDone,
                ]}>
                  {stage.title}
                </Text>
                <Text style={styles.nodeSubtitle}>{stage.subtitle}</Text>
              </View>

              {hasNext && (
                <View style={[
                  styles.connectorLine,
                  nextDone && styles.connectorLineActive,
                ]} />
              )}
            </React.Fragment>
          );
        })}
      </View>

      {/* Live Helper Text */}
      {!isDelivered && (
        <View style={styles.infoBanner}>
          <Ionicons name="flash-outline" size={14} color="#C5A880" />
          <Text style={styles.infoBannerText}>
            Simulated Atelier Pipeline: Order automatically advances every 3 seconds with real-time push alerts.
          </Text>
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: '#FFFFFF',
    padding: 18,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: COLORS.border,
    marginVertical: 10,
    ...SHADOWS.card,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 16,
  },
  sectionHeader: {
    fontSize: 10,
    fontWeight: '800',
    color: COLORS.textMuted,
    letterSpacing: 1.5,
  },
  liveBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 12,
    backgroundColor: '#F3EDE4',
    borderWidth: 1,
    borderColor: '#C5A880',
  },
  liveDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: '#C5A880',
    marginRight: 5,
  },
  liveText: {
    fontSize: 9,
    fontWeight: '700',
    color: '#8A5D24',
    letterSpacing: 0.4,
  },
  completeBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 12,
    backgroundColor: '#E8F5E9',
    borderWidth: 1,
    borderColor: '#A5D6A7',
    gap: 3,
  },
  completeText: {
    fontSize: 9,
    fontWeight: '700',
    color: '#1B8755',
  },
  timelineRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
    paddingHorizontal: 4,
  },
  nodeWrapper: {
    alignItems: 'center',
    width: 52,
  },
  nodeCircle: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#F0ECE4',
    borderWidth: 1,
    borderColor: '#D8D0C4',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 6,
  },
  nodeCircleDone: {
    backgroundColor: '#171717',
    borderColor: '#171717',
  },
  nodeCircleCurrent: {
    backgroundColor: '#C5A880',
    borderColor: '#171717',
    borderWidth: 2,
  },
  nodeTitle: {
    fontSize: 10,
    fontWeight: '600',
    color: COLORS.textMuted,
    textAlign: 'center',
  },
  nodeTitleDone: {
    color: COLORS.obsidian,
  },
  nodeTitleCurrent: {
    color: COLORS.obsidian,
    fontWeight: '800',
  },
  nodeSubtitle: {
    fontSize: 8,
    color: '#9E988F',
    marginTop: 1,
  },
  connectorLine: {
    flex: 1,
    height: 2,
    backgroundColor: '#E8E2D9',
    marginTop: 15,
  },
  connectorLineActive: {
    backgroundColor: '#171717',
  },
  infoBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FAF8F5',
    padding: 10,
    borderRadius: 8,
    marginTop: 14,
    borderWidth: 1,
    borderColor: '#E8E2D9',
    gap: 8,
  },
  infoBannerText: {
    flex: 1,
    fontSize: 10,
    color: '#66615B',
    lineHeight: 14,
  },
  cancelledBox: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 16,
    borderRadius: 14,
    backgroundColor: '#FEF2F2',
    borderWidth: 1,
    borderColor: '#F87171',
    marginVertical: 10,
    gap: 12,
  },
  cancelledTextContainer: {
    flex: 1,
  },
  cancelledTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: '#DC2626',
  },
  cancelledSubtitle: {
    fontSize: 12,
    color: '#7F1D1D',
    marginTop: 2,
  },
});
