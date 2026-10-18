import storage from './storage';
import { Platform } from 'react-native';
import Constants from 'expo-constants';

// Automatically detect tunnel URL from .env, host IP in Expo Go, or fallback to emulator/localhost
const getBaseUrl = () => {
  // 1. Explicit Tunnel / Public URL configured via EXPO_PUBLIC_API_URL
  if (process.env.EXPO_PUBLIC_API_URL) {
    return process.env.EXPO_PUBLIC_API_URL.replace(/\/+$/, '');
  }

  const hostUri = Constants.expoConfig?.hostUri || Constants.manifest?.debuggerHost;
  // 2. If running under Expo LAN/Wi-Fi (and not an ngrok/exp.direct tunnel URL)
  if (hostUri && !hostUri.includes('exp.direct')) {
    const ip = hostUri.split(':')[0];
    return `http://${ip}:8000/api/v1`;
  }

  // 3. Android Emulator fallback
  if (Platform.OS === 'android') {
    return 'http://10.0.2.2:8000/api/v1';
  }

  return 'http://localhost:8000/api/v1';
};

let currentBaseUrl = getBaseUrl();
export const API_BASE_URL = currentBaseUrl;

// Storage keys
export const TOKEN_KEY = '@eleve_auth_token';
export const USER_KEY = '@eleve_user_data';
export const SESSION_KEY = '@eleve_session_token';

// Unique session token generator for guest carts
export const getSessionToken = async () => {
  try {
    let token = await storage.getItem(SESSION_KEY);
    if (!token) {
      token = 'sess_' + Math.random().toString(36).substring(2) + Date.now().toString(36);
      await storage.setItem(SESSION_KEY, token);
    }
    return token;
  } catch {
    return 'sess_fallback';
  }
};

/**
 * Universal API Request Wrapper with token injection, timeout, and automatic host failover
 */
export async function apiRequest(endpoint, options = {}) {
  const token = await storage.getItem(TOKEN_KEY);
  const sessionToken = await getSessionToken();

  const headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Session-Token': sessionToken,
    'Bypass-Tunnel-Reminder': 'true',
    ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
    ...options.headers,
  };

  const tryFetch = async (baseUrl) => {
    const url = `${baseUrl}${endpoint}`;
    const response = await fetch(url, {
      ...options,
      headers,
    });
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.message || `Request failed with status ${response.status}`);
    }
    return data;
  };

  const candidateUrls = [
    currentBaseUrl,
    'http://10.0.2.2:8000/api/v1',
    'http://localhost:8000/api/v1',
    'http://192.168.1.107:8000/api/v1',
  ].filter((url, idx, self) => self.indexOf(url) === idx);

  let lastError = null;
  for (const candidate of candidateUrls) {
    try {
      const result = await tryFetch(candidate);
      if (candidate !== currentBaseUrl) {
        currentBaseUrl = candidate;
      }
      return result;
    } catch (err) {
      lastError = err;
      // If it's a server response error (e.g. 401, 422, 500), don't try other hosts - the host is reachable!
      if (err.message && !err.message.includes('fetch failed') && !err.message.includes('Network request failed') && !err.message.includes('ConnectException')) {
        throw err;
      }
    }
  }

  console.warn(`[API] ${options.method || 'GET'} ${endpoint} error:`, lastError?.message);
  throw lastError;
}

// API Service Methods
export const api = {
  // Auth
  login: (credentials) => apiRequest('/auth/login', { method: 'POST', body: JSON.stringify(credentials) }),
  register: (payload) => apiRequest('/auth/register', { method: 'POST', body: JSON.stringify(payload) }),
  getProfile: () => apiRequest('/auth/me'),
  updateProfile: (data) => apiRequest('/auth/profile', { method: 'PUT', body: JSON.stringify(data) }),
  logout: () => apiRequest('/auth/logout', { method: 'POST' }),

  // Catalog
  getCategories: () => apiRequest('/categories'),
  getProducts: (params = '') => apiRequest(`/products${params ? '?' + params : ''}`),
  getFeaturedProducts: () => apiRequest('/products/featured'),
  getBestsellers: () => apiRequest('/products/bestsellers'),
  getProduct: (idOrSlug) => apiRequest(`/products/${idOrSlug}`),

  // Cart
  getCart: () => apiRequest('/cart'),
  addToCart: (variantId, quantity = 1) => apiRequest('/cart/items', {
    method: 'POST',
    body: JSON.stringify({ product_variant_id: variantId, quantity }),
  }),
  updateCartItem: (itemId, quantity) => apiRequest(`/cart/items/${itemId}`, {
    method: 'PUT',
    body: JSON.stringify({ quantity }),
  }),
  removeCartItem: (itemId) => apiRequest(`/cart/items/${itemId}`, { method: 'DELETE' }),

  // Orders
  placeOrder: (orderData) => apiRequest('/orders', {
    method: 'POST',
    body: JSON.stringify(orderData),
  }),
  getOrders: () => apiRequest('/orders'),
  getOrder: (orderNumber) => apiRequest(`/orders/${orderNumber}`),
  autoProgressOrder: (orderNumber) => apiRequest(`/orders/${orderNumber}/auto-progress`, {
    method: 'POST',
  }),
  cancelOrder: (orderNumber, reason) => apiRequest(`/orders/${orderNumber}/cancel`, {
    method: 'POST',
    body: JSON.stringify({ reason }),
  }),

  // Notifications & Real-Time Sync
  getNotifications: () => apiRequest('/notifications'),
  pollNotifications: (lastId = 0, orderNumber = null) => {
    let query = `?last_id=${lastId}`;
    if (orderNumber) query += `&order_number=${orderNumber}`;
    return apiRequest(`/notifications/poll${query}`);
  },
  markNotificationAsRead: (id) => apiRequest(`/notifications/${id}/read`, { method: 'POST' }),
  markAllNotificationsAsRead: () => apiRequest('/notifications/read-all', { method: 'POST' }),
  dismissNotification: (id) => apiRequest(`/notifications/${id}`, { method: 'DELETE' }),
  clearNotifications: () => apiRequest('/notifications', { method: 'DELETE' }),

  // Wishlist
  getWishlist: () => apiRequest('/wishlist'),
  toggleWishlist: (productId) => apiRequest('/wishlist/toggle', {
    method: 'POST',
    body: JSON.stringify({ product_id: productId }),
  }),

  // Addresses
  getAddresses: () => apiRequest('/addresses'),
  saveAddress: (addressData) => apiRequest('/addresses', {
    method: 'POST',
    body: JSON.stringify(addressData),
  }),
};
