import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import { api } from '../services/api';
import { useAuth } from './AuthContext';

const CartContext = createContext({
  cart: { items: [], subtotal: 0, total_items: 0 },
  isLoading: false,
  addToCart: async () => {},
  updateQuantity: async () => {},
  removeFromCart: async () => {},
  refreshCart: async () => {},
  clearLocalCart: () => {},
});

export const CartProvider = ({ children }) => {
  const { isAuthenticated, user } = useAuth();
  const [cart, setCart] = useState({ items: [], subtotal: 0, total_items: 0 });
  const [isLoading, setIsLoading] = useState(false);

  const refreshCart = useCallback(async () => {
    try {
      setIsLoading(true);
      const res = await api.getCart();
      if (res.data) {
        setCart(res.data);
      }
    } catch (e) {
      console.warn('Failed to load cart:', e.message);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    refreshCart();
  }, [refreshCart, isAuthenticated, user?.id]);

  const addToCart = async (variantId, quantity = 1) => {
    try {
      setIsLoading(true);
      const res = await api.addToCart(variantId, quantity);
      if (res.data) {
        setCart(res.data);
        return res.data;
      }
    } catch (e) {
      throw e;
    } finally {
      setIsLoading(false);
    }
  };

  const updateQuantity = async (itemId, quantity) => {
    try {
      setIsLoading(true);
      const res = await api.updateCartItem(itemId, quantity);
      if (res.data) {
        setCart(res.data);
        return res.data;
      }
    } catch (e) {
      throw e;
    } finally {
      setIsLoading(false);
    }
  };

  const removeFromCart = async (itemId) => {
    try {
      setIsLoading(true);
      const res = await api.removeCartItem(itemId);
      if (res.data) {
        setCart(res.data);
        return res.data;
      }
    } catch (e) {
      throw e;
    } finally {
      setIsLoading(false);
    }
  };

  const clearLocalCart = () => {
    setCart({ items: [], subtotal: 0, total_items: 0 });
  };

  return (
    <CartContext.Provider
      value={{
        cart,
        isLoading,
        addToCart,
        updateQuantity,
        removeFromCart,
        refreshCart,
        clearLocalCart,
      }}
    >
      {children}
    </CartContext.Provider>
  );
};

export const useCart = () => useContext(CartContext);
