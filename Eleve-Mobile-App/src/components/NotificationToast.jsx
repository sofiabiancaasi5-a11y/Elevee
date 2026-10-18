import React, { useEffect, useRef } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Animated, Platform } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useNotifications } from '../context/NotificationContext';
import { COLORS, SHADOWS } from '../constants/theme';

export default function NotificationToast() {
  const router = useRouter();
  const { activeToast, hideToast, openModal } = useNotifications();
  const translateY = useRef(new Animated.Value(-120)).current;
  const opacity = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    if (activeToast) {
      Animated.parallel([
        Animated.spring(translateY, {
          toValue: 0,
          useNativeDriver: true,
          tension: 80,
          friction: 9,
        }),
        Animated.timing(opacity, {
          toValue: 1,
          duration: 250,
          useNativeDriver: true,
        }),
      ]).start();
    } else {
      Animated.parallel([
        Animated.timing(translateY, {
          toValue: -120,
          duration: 220,
          useNativeDriver: true,
        }),
        Animated.timing(opacity, {
          toValue: 0,
          duration: 200,
          useNativeDriver: true,
        }),
      ]).start();
    }
  }, [activeToast, translateY, opacity]);

  if (!activeToast) return null;

  const handleToastPress = () => {
    hideToast();
    const textToScan = `${activeToast.title || ''} ${activeToast.message || ''}`;
    const orderMatch = textToScan.match(/#?(ELE-[A-Z0-9-]+)/i);
    if (orderMatch && orderMatch[1]) {
      const orderNumber = orderMatch[1].toUpperCase();
      router.push({ pathname: '/order/[id]', params: { id: orderNumber } });
    } else {
      openModal();
    }
  };

  return (
    <Animated.View
      style={[
        styles.container,
        {
          transform: [{ translateY }],
          opacity,
        },
      ]}
    >
      <TouchableOpacity
        activeOpacity={0.9}
        onPress={handleToastPress}
        style={styles.card}
      >
        <View style={styles.iconContainer}>
          <Text style={styles.monogramText}>É</Text>
        </View>

        <View style={styles.textContainer}>
          <View style={styles.headerRow}>
            <Text style={styles.brandTag}>ÉLEVÉ ATELIER</Text>
            <Text style={styles.timeTag}>Just now</Text>
          </View>
          <Text style={styles.title} numberOfLines={1}>
            {activeToast.title}
          </Text>
          <Text style={styles.message} numberOfLines={2}>
            {activeToast.message}
          </Text>
        </View>

        <TouchableOpacity onPress={hideToast} style={styles.closeBtn} hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}>
          <Ionicons name="close" size={16} color="#A39E93" />
        </TouchableOpacity>
      </TouchableOpacity>
    </Animated.View>
  );
}

const styles = StyleSheet.create({
  container: {
    position: 'absolute',
    top: Platform.OS === 'ios' ? 56 : 36,
    left: 16,
    right: 16,
    zIndex: 9999,
  },
  card: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#171717',
    borderRadius: 16,
    paddingVertical: 12,
    paddingHorizontal: 14,
    borderWidth: 1,
    borderColor: '#C5A880',
    ...SHADOWS.floating,
  },
  iconContainer: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#C5A880',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  monogramText: {
    fontFamily: 'serif',
    fontSize: 15,
    fontWeight: '700',
    color: '#171717',
    textAlign: 'center',
    includeFontPadding: false,
  },
  textContainer: {
    flex: 1,
  },
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 2,
  },
  brandTag: {
    fontFamily: 'system-ui',
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 1.2,
    color: '#C5A880',
    textTransform: 'uppercase',
  },
  timeTag: {
    fontSize: 9,
    color: '#9E988F',
  },
  title: {
    color: '#FFFFFF',
    fontSize: 13,
    fontWeight: '600',
    letterSpacing: -0.2,
    marginBottom: 2,
  },
  message: {
    color: '#D8D0C4',
    fontSize: 11,
    lineHeight: 15,
  },
  closeBtn: {
    padding: 4,
    marginLeft: 8,
  },
});
