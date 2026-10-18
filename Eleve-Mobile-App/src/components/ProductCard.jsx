import React, { memo } from 'react';
import { View, Text, TouchableOpacity, StyleSheet, Dimensions } from 'react-native';
import { Image } from 'expo-image';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useWishlist } from '../context/WishlistContext';
import { useTheme } from '../context/ThemeContext';
import { COLORS, formatPrice } from '../constants/theme';

const { width } = Dimensions.get('window');
const CARD_WIDTH = (width - 44) / 2;

const ProductCard = ({ product }) => {
  const router = useRouter();
  const { isFavorite, toggleWishlist } = useWishlist();
  const { colors, isDark } = useTheme();
  const favorite = isFavorite(product.id);

  const handlePress = () => {
    router.push({
      pathname: '/product/[id]',
      params: { id: product.id },
    });
  };

  const handleWishlistToggle = (e) => {
    e?.stopPropagation?.();
    toggleWishlist(product);
  };

  return (
    <TouchableOpacity
      style={[
        styles.card,
        {
          backgroundColor: colors.card,
          borderColor: colors.cardBorder,
        },
      ]}
      onPress={handlePress}
      activeOpacity={0.92}
    >
      {/* Visual Showcase Container */}
      <View style={[styles.imageContainer, { backgroundColor: colors.imageBacking }]}>
        <Image
          source={{ uri: product.primary_image }}
          style={styles.image}
          contentFit="cover"
          transition={250}
          cachePolicy="memory-disk"
        />

        {/* Top-Left Atelier Badge */}
        <View style={[styles.atelierBadge, { backgroundColor: colors.badgeBg, borderColor: colors.cardBorder }]}>
          <Text style={[styles.atelierBadgeText, { color: colors.badgeText }]}>
            {product.is_featured ? 'FEATURED' : product.is_bestseller ? 'ICONIC' : 'ATELIER'}
          </Text>
        </View>

        {/* Floating Wishlist Heart */}
        <TouchableOpacity
          style={[styles.heartButton, { backgroundColor: colors.badgeBg, borderColor: colors.cardBorder }]}
          onPress={handleWishlistToggle}
          hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
          activeOpacity={0.8}
        >
          <Ionicons
            name={favorite ? 'heart' : 'heart-outline'}
            size={16}
            color={favorite ? '#DC2626' : colors.textPrimary}
          />
        </TouchableOpacity>

        {/* Low Stock or Out of Stock Badge */}
        {!product.is_in_stock ? (
          <View style={styles.outOfStockBadge}>
            <Text style={styles.outOfStockText}>SOLD OUT</Text>
          </View>
        ) : product.total_stock <= 5 ? (
          <View style={styles.lowStockBadge}>
            <Text style={styles.lowStockText}>ONLY {product.total_stock} LEFT</Text>
          </View>
        ) : null}
      </View>

      {/* Editorial Details */}
      <View style={[styles.info, { backgroundColor: colors.card }]}>
        {/* Category & Color Indicator */}
        <View style={styles.categoryRow}>
          <Text style={[styles.category, { color: colors.accent }]}>
            {product.category?.name?.toUpperCase() || 'FOOTWEAR'}
          </Text>
          {product.available_colors && product.available_colors.length > 0 && (
            <Text style={[styles.shadeCount, { color: colors.textMuted }]}>
              {product.available_colors.length} shades
            </Text>
          )}
        </View>

        {/* Product Name with 2-line clamp */}
        <Text style={[styles.title, { color: colors.textPrimary }]} numberOfLines={2}>
          {product.name}
        </Text>

        {/* Price & Swatches Footer Row */}
        <View style={[styles.footerRow, { borderTopColor: colors.borderSubtle }]}>
          <Text style={[styles.price, { color: colors.textPrimary }]}>
            {formatPrice(product.base_price || product.formatted_price)}
          </Text>

          {/* Color Swatch Dots */}
          {product.available_colors?.length > 0 && (
            <View style={styles.colorsRow}>
              {product.available_colors.slice(0, 3).map((col, idx) => (
                <View
                  key={idx}
                  style={[
                    styles.colorDotRing,
                    {
                      backgroundColor: colors.surfaceSubtle,
                      borderColor: col.hex === '#FFFFFF' || col.hex === '#F7F5F0' ? colors.border : 'transparent',
                    },
                  ]}
                >
                  <View style={[styles.colorDot, { backgroundColor: col.hex }]} />
                </View>
              ))}
              {product.available_colors.length > 3 && (
                <Text style={[styles.moreColors, { color: colors.textMuted }]}>
                  +{product.available_colors.length - 3}
                </Text>
              )}
            </View>
          )}
        </View>
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  card: {
    width: CARD_WIDTH,
    marginBottom: 16,
    backgroundColor: '#FFFFFF',
    borderRadius: 16,
    borderWidth: 1,
    borderColor: 'rgba(226, 220, 213, 0.85)',
    overflow: 'hidden',
    shadowColor: '#171717',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 3,
  },
  imageContainer: {
    width: '100%',
    height: CARD_WIDTH * 1.2,
    backgroundColor: '#F5F2EB',
    position: 'relative',
  },
  image: {
    width: '100%',
    height: '100%',
  },
  atelierBadge: {
    position: 'absolute',
    top: 8,
    left: 8,
    backgroundColor: 'rgba(255, 255, 255, 0.92)',
    paddingHorizontal: 7,
    paddingVertical: 3,
    borderRadius: 6,
    borderWidth: 0.5,
    borderColor: 'rgba(226, 220, 213, 0.9)',
  },
  atelierBadgeText: {
    fontSize: 8.5,
    fontWeight: '800',
    color: '#A67C52',
    letterSpacing: 1,
  },
  heartButton: {
    position: 'absolute',
    top: 8,
    right: 8,
    width: 30,
    height: 30,
    borderRadius: 15,
    backgroundColor: 'rgba(255, 255, 255, 0.92)',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 0.5,
    borderColor: 'rgba(226, 220, 213, 0.9)',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 3,
    elevation: 2,
  },
  outOfStockBadge: {
    position: 'absolute',
    bottom: 8,
    left: 8,
    backgroundColor: 'rgba(23, 23, 23, 0.88)',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 4,
  },
  outOfStockText: {
    color: '#FFFFFF',
    fontSize: 9,
    fontWeight: 'bold',
    letterSpacing: 1,
  },
  lowStockBadge: {
    position: 'absolute',
    bottom: 8,
    left: 8,
    backgroundColor: 'rgba(217, 119, 6, 0.92)',
    paddingHorizontal: 7,
    paddingVertical: 2.5,
    borderRadius: 4,
  },
  lowStockText: {
    color: '#FFFFFF',
    fontSize: 8.5,
    fontWeight: 'bold',
    letterSpacing: 0.8,
  },
  info: {
    padding: 12,
    backgroundColor: '#FFFFFF',
  },
  categoryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 3,
  },
  category: {
    fontSize: 9,
    fontWeight: '700',
    color: '#A67C52',
    letterSpacing: 1.2,
  },
  shadeCount: {
    fontSize: 9,
    color: COLORS.textMuted,
  },
  title: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.obsidian,
    lineHeight: 18,
    minHeight: 36,
    marginBottom: 8,
  },
  footerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 4,
    borderTopWidth: 1,
    borderTopColor: '#F5F2EB',
  },
  price: {
    fontSize: 13.5,
    fontWeight: '700',
    color: COLORS.obsidian,
    letterSpacing: 0.2,
  },
  colorsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  colorDotRing: {
    padding: 1.5,
    borderRadius: 7,
    borderWidth: 1,
    backgroundColor: '#FFFFFF',
  },
  colorDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  moreColors: {
    fontSize: 9,
    fontWeight: '600',
    color: COLORS.textMuted,
    marginLeft: 1,
  },
});

export default memo(ProductCard);
