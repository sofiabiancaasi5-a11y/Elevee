import React, { createContext, useContext, useState, useEffect, useCallback, useRef } from 'react';
import { api } from '../services/api';

const NotificationContext = createContext({
  notifications: [],
  unreadCount: 0,
  activeToast: null,
  isModalOpen: false,
  activeTrackedOrder: null,
  setActiveTrackedOrder: () => {},
  showToast: () => {},
  hideToast: () => {},
  openModal: () => {},
  closeModal: () => {},
  refreshNotifications: async () => {},
  markAllAsRead: async () => {},
  markAsRead: async () => {},
  dismissNotification: async () => {},
  clearAllNotifications: async () => {},
});

export const NotificationProvider = ({ children }) => {
  const [notifications, setNotifications] = useState([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [activeToast, setActiveToast] = useState(null);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [activeTrackedOrder, setActiveTrackedOrder] = useState(null);

  const lastIdRef = useRef(0);
  const toastTimeoutRef = useRef(null);

  const showToast = useCallback((title, message, type = 'order_status') => {
    if (toastTimeoutRef.current) {
      clearTimeout(toastTimeoutRef.current);
    }

    setActiveToast({
      id: Date.now(),
      title,
      message,
      type,
    });

    toastTimeoutRef.current = setTimeout(() => {
      setActiveToast(null);
    }, 4000);
  }, []);

  const hideToast = useCallback(() => {
    if (toastTimeoutRef.current) {
      clearTimeout(toastTimeoutRef.current);
    }
    setActiveToast(null);
  }, []);

  const refreshNotifications = useCallback(async () => {
    try {
      const res = await api.getNotifications();
      if (res.data) {
        setNotifications(res.data.notifications || []);
        setUnreadCount(res.data.unread_count || 0);
        if (res.data.notifications?.length > 0) {
          lastIdRef.current = Math.max(...res.data.notifications.map((n) => n.id), lastIdRef.current);
        }
      }
    } catch {
      // Background quiet failover
    }
  }, []);

  const markAllAsRead = useCallback(async () => {
    try {
      await api.markAllNotificationsAsRead();
      setUnreadCount(0);
      setNotifications((prev) => prev.map((n) => ({ ...n, is_read: true })));
    } catch {
      // ignore
    }
  }, []);

  const markAsRead = useCallback(async (id) => {
    try {
      await api.markNotificationAsRead(id);
      setNotifications((prev) =>
        prev.map((n) => (n.id === id ? { ...n, is_read: true } : n))
      );
      setUnreadCount((prev) => Math.max(0, prev - 1));
    } catch {
      // ignore
    }
  }, []);

  const dismissNotification = useCallback(async (id) => {
    try {
      await api.dismissNotification(id);
      setNotifications((prev) => {
        const item = prev.find((n) => n.id === id);
        if (item && !item.is_read) {
          setUnreadCount((c) => Math.max(0, c - 1));
        }
        return prev.filter((n) => n.id !== id);
      });
    } catch {
      // Quiet failover
    }
  }, []);

  const clearAllNotifications = useCallback(async () => {
    try {
      await api.clearNotifications();
      setNotifications([]);
      setUnreadCount(0);
    } catch {
      // Quiet failover
    }
  }, []);

  // Poll loop: Every 3 seconds, query delta updates and auto-advance
  useEffect(() => {
    refreshNotifications();

    const interval = setInterval(async () => {
      try {
        const orderNum = activeTrackedOrder?.order_number || null;
        const res = await api.pollNotifications(lastIdRef.current, orderNum);

        if (res.data) {
          const { new_notifications, unread_count, tracked_order } = res.data;

          if (unread_count !== undefined) {
            setUnreadCount(unread_count);
          }

          if (new_notifications && new_notifications.length > 0) {
            // Show toast for latest incoming notification
            const latest = new_notifications[0];
            showToast(latest.title, latest.message, latest.type);

            lastIdRef.current = Math.max(...new_notifications.map((n) => n.id), lastIdRef.current);

            setNotifications((prev) => {
              const existingIds = new Set(prev.map((n) => n.id));
              const uniqueNew = new_notifications.filter((n) => !existingIds.has(n.id));
              return [...uniqueNew, ...prev];
            });
          }

          // If tracking an order and its status updated, sync local active order
          if (tracked_order && activeTrackedOrder && tracked_order.status !== activeTrackedOrder.status) {
            setActiveTrackedOrder((prev) => ({
              ...prev,
              status: tracked_order.status,
            }));
          }
        }
      } catch {
        // Quiet failover
      }
    }, 3000);

    return () => {
      clearInterval(interval);
      if (toastTimeoutRef.current) {
        clearTimeout(toastTimeoutRef.current);
      }
    };
  }, [refreshNotifications, activeTrackedOrder, showToast]);

  return (
    <NotificationContext.Provider
      value={{
        notifications,
        unreadCount,
        activeToast,
        isModalOpen,
        activeTrackedOrder,
        setActiveTrackedOrder,
        showToast,
        hideToast,
        openModal: () => setIsModalOpen(true),
        closeModal: () => setIsModalOpen(false),
        refreshNotifications,
        markAllAsRead,
        markAsRead,
        dismissNotification,
        clearAllNotifications,
      }}
    >
      {children}
    </NotificationContext.Provider>
  );
};

export const useNotifications = () => useContext(NotificationContext);
export default NotificationContext;
