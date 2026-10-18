import React, { useState, useMemo } from 'react';
import {
  View,
  Text,
  Modal,
  TouchableOpacity,
  FlatList,
  StyleSheet,
  Platform,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useNotifications } from '../context/NotificationContext';
import { useTheme } from '../context/ThemeContext';
import { COLORS, SHADOWS } from '../constants/theme';

export default function NotificationModal() {
  const router = useRouter();
  const {
    isModalOpen,
    closeModal,
    notifications,
    unreadCount,
    markAllAsRead,
    markAsRead,
    dismissNotification,
    clearAllNotifications,
  } = useNotifications();
  const { colors, isDark } = useTheme();

  const [activeFilter, setActiveFilter] = useState('all'); // 'all' | 'orders' | 'unread'

  const filteredNotifications = useMemo(() => {
    return notifications.filter((item) => {
      if (activeFilter === 'unread') {
        return !item.is_read;
      }
      if (activeFilter === 'orders') {
        return (
          item.type === 'order_placed' ||
          item.type === 'order_status' ||
          /#?ELE-[A-Z0-9-]+/i.test(`${item.title} ${item.message || ''}`)
        );
      }
      return true;
    });
  }, [notifications, activeFilter]);

  const ordersCount = useMemo(() => {
    return notifications.filter(
      (item) =>
        item.type === 'order_placed' ||
        item.type === 'order_status' ||
        /#?ELE-[A-Z0-9-]+/i.test(`${item.title} ${item.message || ''}`)
    ).length;
  }, [notifications]);

  const handleCardPress = (item) => {
    if (!item.is_read) {
      markAsRead(item.id);
    }

    const fullText = `${item.title || ''} ${item.message || ''}`;
    const orderMatch = fullText.match(/#?(ELE-[A-Z0-9-]+)/i);

    if (orderMatch && orderMatch[1]) {
      const orderNumber = orderMatch[1].toUpperCase();
      closeModal();
      setTimeout(() => {
        router.push({
          pathname: '/order/[id]',
          params: { id: orderNumber },
        });
      }, 150);
    }
  };

  const handleClearAll = () => {
    Alert.alert(
      'Clear Atelier Feed',
      'Are you sure you wish to clear all notifications from your feed?',
      [
        { text: 'Keep', style: 'cancel' },
        {
          text: 'Clear All',
          style: 'destructive',
          onPress: clearAllNotifications,
        },
      ]
    );
  };

  const renderIcon = (item, isUnread) => {
    const titleLower = (item.title || '').toLowerCase();
    const iconColor = isUnread ? (isDark ? '#0F0F0F' : '#171717') : colors.textMuted;

    if (item.type === 'order_placed' || titleLower.includes('new order')) {
      return <Ionicons name="bag-check" size={18} color={iconColor} />;
    }
    if (titleLower.includes('delivered')) {
      return <Ionicons name="checkmark-done-circle" size={18} color={iconColor} />;
    }
    if (titleLower.includes('transit') || titleLower.includes('dispatched')) {
      return <Ionicons name="airplane" size={18} color={iconColor} />;
    }
    if (titleLower.includes('crafting') || titleLower.includes('artisan')) {
      return <Ionicons name="construct" size={17} color={iconColor} />;
    }
    if (titleLower.includes('confirmed')) {
      return <Ionicons name="shield-checkmark" size={18} color={iconColor} />;
    }
    if (item.type === 'order_status') {
      return <Ionicons name="time" size={18} color={iconColor} />;
    }

    // Default luxury ÉLEVÉ monogram brand mark fallback
    return (
      <Text
        style={[
          styles.monogramMini,
          { color: iconColor, fontFamily: 'serif', fontWeight: 'bold' },
        ]}
      >
        É
      </Text>
    );
  };

  const renderItem = ({ item }) => {
    const isUnread = !item.is_read;
    const fullText = `${item.title || ''} ${item.message || ''}`;
    const orderMatch = fullText.match(/#?(ELE-[A-Z0-9-]+)/i);
    const hasOrderLink = Boolean(orderMatch && orderMatch[1]);

    return (
      <TouchableOpacity
        activeOpacity={0.85}
        onPress={() => handleCardPress(item)}
        style={[
          styles.itemCard,
          {
            backgroundColor: colors.surface,
            borderColor: colors.border,
          },
          isUnread && [
            styles.itemUnread,
            {
              backgroundColor: isDark ? 'rgba(197, 168, 128, 0.08)' : '#FFFDF9',
              borderLeftColor: colors.champagne,
              borderColor: colors.champagne,
            },
          ],
        ]}
      >
        <View
          style={[
            styles.itemIcon,
            isUnread
              ? [styles.itemIconUnread, { backgroundColor: colors.champagne }]
              : [styles.itemIconRead, { backgroundColor: colors.surfaceSubtle }],
          ]}
        >
          {renderIcon(item, isUnread)}
        </View>

        <View style={styles.itemContent}>
          <View style={styles.itemHeader}>
            <Text
              style={[
                styles.itemTitle,
                { color: colors.textPrimary },
                isUnread && styles.itemTitleBold,
              ]}
              numberOfLines={1}
            >
              {item.title}
            </Text>
            {isUnread && <View style={[styles.unreadDot, { backgroundColor: colors.champagne }]} />}

            <TouchableOpacity
              onPress={() => dismissNotification(item.id)}
              style={styles.dismissBtn}
              hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
            >
              <Ionicons name="close" size={14} color={colors.textMuted} />
            </TouchableOpacity>
          </View>

          <Text style={[styles.itemMessage, { color: colors.textSecondary }]}>
            {item.message}
          </Text>

          <View style={styles.itemFooter}>
            <Text style={[styles.itemTime, { color: colors.textMuted }]}>
              {new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })} &bull;{' '}
              {new Date(item.created_at).toLocaleDateString([], { month: 'short', day: 'numeric' })}
            </Text>

            {hasOrderLink && (
              <View style={styles.trackLink}>
                <Text style={[styles.trackLinkText, { color: colors.accent }]}>Track Order</Text>
                <Ionicons name="chevron-forward" size={12} color={colors.accent} />
              </View>
            )}
          </View>
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <Modal
      visible={isModalOpen}
      animationType="slide"
      transparent={true}
      onRequestClose={closeModal}
    >
      <View style={styles.overlay}>
        <SafeAreaView style={styles.safeArea}>
          <View style={[styles.modalContent, { backgroundColor: colors.background }]}>
            {/* Header */}
            <View style={[styles.header, { borderBottomColor: colors.borderSubtle }]}>
              <View>
                <Text style={[styles.brandTitle, { color: colors.textPrimary }]}>ATELIER FEED</Text>
                <Text style={[styles.subTitle, { color: colors.textMuted }]}>
                  {unreadCount > 0 ? `${unreadCount} unread update${unreadCount > 1 ? 's' : ''}` : 'All caught up'}
                </Text>
              </View>

              <View style={styles.headerActions}>
                {unreadCount > 0 && (
                  <TouchableOpacity onPress={markAllAsRead} style={styles.headerBtn}>
                    <Text style={[styles.actionBtnText, { color: colors.accent }]}>Mark read</Text>
                  </TouchableOpacity>
                )}
                {notifications.length > 0 && (
                  <TouchableOpacity onPress={handleClearAll} style={styles.headerBtn}>
                    <Text style={[styles.actionBtnText, { color: colors.textMuted }]}>Clear</Text>
                  </TouchableOpacity>
                )}
                <TouchableOpacity
                  onPress={closeModal}
                  style={[styles.closeBtn, { backgroundColor: colors.surface, borderColor: colors.border }]}
                >
                  <Ionicons name="close" size={18} color={colors.textPrimary} />
                </TouchableOpacity>
              </View>
            </View>

            {/* Filter Tabs Bar */}
            <View style={[styles.filterBar, { borderBottomColor: colors.borderSubtle }]}>
              <TouchableOpacity
                style={[
                  styles.filterTab,
                  activeFilter === 'all' && [styles.filterTabActive, { borderBottomColor: colors.accent }],
                ]}
                onPress={() => setActiveFilter('all')}
              >
                <Text
                  style={[
                    styles.filterTabText,
                    { color: activeFilter === 'all' ? colors.textPrimary : colors.textMuted },
                    activeFilter === 'all' && styles.filterTabTextActive,
                  ]}
                >
                  All ({notifications.length})
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.filterTab,
                  activeFilter === 'orders' && [styles.filterTabActive, { borderBottomColor: colors.accent }],
                ]}
                onPress={() => setActiveFilter('orders')}
              >
                <Text
                  style={[
                    styles.filterTabText,
                    { color: activeFilter === 'orders' ? colors.textPrimary : colors.textMuted },
                    activeFilter === 'orders' && styles.filterTabTextActive,
                  ]}
                >
                  Orders ({ordersCount})
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.filterTab,
                  activeFilter === 'unread' && [styles.filterTabActive, { borderBottomColor: colors.accent }],
                ]}
                onPress={() => setActiveFilter('unread')}
              >
                <Text
                  style={[
                    styles.filterTabText,
                    { color: activeFilter === 'unread' ? colors.textPrimary : colors.textMuted },
                    activeFilter === 'unread' && styles.filterTabTextActive,
                  ]}
                >
                  Unread ({unreadCount})
                </Text>
              </TouchableOpacity>
            </View>

            {/* Notification List */}
            <FlatList
              data={filteredNotifications}
              keyExtractor={(item) => String(item.id)}
              renderItem={renderItem}
              contentContainerStyle={styles.listContainer}
              ListEmptyComponent={
                <View style={styles.emptyContainer}>
                  <Ionicons name="notifications-outline" size={40} color={colors.border} />
                  <Text style={[styles.emptyTitle, { color: colors.textPrimary }]}>
                    {activeFilter === 'unread'
                      ? 'No Unread Updates'
                      : activeFilter === 'orders'
                      ? 'No Order Dispatches Yet'
                      : 'No Notifications'}
                  </Text>
                  <Text style={[styles.emptyDesc, { color: colors.textMuted }]}>
                    Live atelier order progression and delivery updates will appear here in real-time.
                  </Text>
                </View>
              }
            />
          </View>
        </SafeAreaView>
      </View>
    </Modal>
  );
}

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(23, 23, 23, 0.65)',
    justifyContent: 'flex-end',
  },
  safeArea: {
    maxHeight: '88%',
  },
  modalContent: {
    backgroundColor: '#F7F5F0',
    borderTopLeftRadius: 28,
    borderTopRightRadius: 28,
    paddingTop: 18,
    paddingBottom: Platform.OS === 'ios' ? 24 : 16,
    height: '100%',
    ...SHADOWS.floating,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingBottom: 12,
    borderBottomWidth: 1,
  },
  brandTitle: {
    fontFamily: 'serif',
    fontSize: 16,
    fontWeight: '800',
    letterSpacing: 1.5,
  },
  subTitle: {
    fontSize: 12,
    marginTop: 2,
  },
  headerActions: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  headerBtn: {
    paddingVertical: 4,
    paddingHorizontal: 6,
  },
  actionBtnText: {
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.3,
  },
  closeBtn: {
    width: 32,
    height: 32,
    borderRadius: 16,
    borderWidth: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginLeft: 4,
  },
  filterBar: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    borderBottomWidth: 1,
    gap: 20,
  },
  filterTab: {
    paddingVertical: 10,
    borderBottomWidth: 2,
    borderBottomColor: 'transparent',
  },
  filterTabActive: {
    borderBottomWidth: 2,
  },
  filterTabText: {
    fontSize: 12,
    fontWeight: '600',
    letterSpacing: 0.3,
  },
  filterTabTextActive: {
    fontWeight: '700',
  },
  listContainer: {
    padding: 16,
    paddingBottom: 40,
  },
  itemCard: {
    flexDirection: 'row',
    padding: 14,
    borderRadius: 16,
    borderWidth: 1,
    marginBottom: 10,
    ...SHADOWS.subtle,
  },
  itemUnread: {
    borderLeftWidth: 3,
  },
  itemIcon: {
    width: 36,
    height: 36,
    borderRadius: 18,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  itemIconUnread: {},
  itemIconRead: {},
  monogramMini: {
    fontSize: 14,
  },
  itemContent: {
    flex: 1,
  },
  itemHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 2,
  },
  itemTitle: {
    fontSize: 13,
    fontWeight: '600',
    flex: 1,
  },
  itemTitleBold: {
    fontWeight: '700',
  },
  unreadDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
    marginLeft: 6,
    marginRight: 8,
  },
  dismissBtn: {
    padding: 4,
    marginLeft: 'auto',
  },
  itemMessage: {
    fontSize: 12,
    lineHeight: 16,
    marginVertical: 4,
  },
  itemFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 4,
  },
  itemTime: {
    fontSize: 10,
  },
  trackLink: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 2,
  },
  trackLinkText: {
    fontSize: 10.5,
    fontWeight: '700',
    letterSpacing: 0.3,
    textTransform: 'uppercase',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
    paddingHorizontal: 20,
  },
  emptyTitle: {
    fontSize: 16,
    fontWeight: '700',
    marginTop: 12,
    marginBottom: 6,
  },
  emptyDesc: {
    fontSize: 12,
    textAlign: 'center',
    lineHeight: 18,
  },
});
