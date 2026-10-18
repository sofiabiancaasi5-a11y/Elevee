import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  Dimensions,
  Alert,
} from 'react-native';
import { Image } from 'expo-image';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useLocalSearchParams, useRouter } from 'expo-router';
import Header from '../../components/Header';
import SizeSelector from '../../components/SizeSelector';
import ColorSelector from '../../components/ColorSelector';
import { api } from '../../services/api';
import { useCart } from '../../context/CartContext';
import { useWishlist } from '../../context/WishlistContext';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { COLORS, formatPrice } from '../../constants/theme';

const { width } = Dimensions.get('window');

export default function ProductDetailScreen() {
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const { addToCart } = useCart();
  const { isFavorite, toggleWishlist } = useWishlist();
  const { isAuthenticated } = useAuth();
  const { colors, isDark } = useTheme();

  const [product, setProduct] = useState(null);
  const [selectedColor, setSelectedColor] = useState(null);
  const [selectedSize, setSelectedSize] = useState(null);
  const [activeImageIndex, setActiveImageIndex] = useState(0);
  const [isLoading, setIsLoading] = useState(true);
  const [isAdding, setIsAdding] = useState(false);
  const [addedMessage, setAddedMessage] = useState(null);

  useEffect(() => {
    if (id) {
      api.getProduct(id)
        .then(res => {
          if (res.data) {
            const p = res.data;
            setProduct(p);
            if (p.available_colors && p.available_colors.length > 0) {
              setSelectedColor(p.available_colors[0].name);
            }
            if (p.available_sizes && p.available_sizes.length > 0) {
              setSelectedSize(p.available_sizes[0]);
            }
          }
        })
        .catch(err => {
          console.error('Failed to load product:', err);
        })
        .finally(() => setIsLoading(false));
    }
  }, [id]);

  if (isLoading) {
    return (
      <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
        <Header showBack={true} />
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={colors.textPrimary} />
          <Text style={[styles.loadingText, { color: colors.textMuted }]}>Fetching silhouette craftsmanship...</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (!product) {
    return (
      <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
        <Header showBack={true} />
        <View style={styles.centerContainer}>
          <Text style={[styles.errorTitle, { color: colors.textPrimary }]}>Silhouette Not Found</Text>
          <TouchableOpacity style={[styles.backBtn, { backgroundColor: colors.textPrimary }]} onPress={() => router.back()}>
            <Text style={[styles.backBtnText, { color: colors.background }]}>Return to Catalog</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    );
  }

  // Find matching variant
  const selectedVariant = product.variants?.find(
    v => v.color.toLowerCase() === (selectedColor || '').toLowerCase() && v.size === selectedSize
  );

  const isCurrentVariantInStock = selectedVariant ? selectedVariant.stock_quantity > 0 : false;
  const currentStockCount = selectedVariant ? selectedVariant.stock_quantity : 0;
  const favorite = isFavorite(product.id);

  // Filter available sizes for current selected color
  const sizesForColor = product.variants
    ?.filter(v => v.color.toLowerCase() === (selectedColor || '').toLowerCase() && v.stock_quantity > 0)
    .map(v => v.size) || [];

  const handleAddToBag = async () => {
    if (!selectedVariant) {
      Alert.alert('Selection Required', 'Please select a size and color.');
      return;
    }

    if (!isCurrentVariantInStock) {
      Alert.alert('Sold Out', 'This size and color combination is currently sold out.');
      return;
    }

    try {
      setIsAdding(true);
      await addToCart(selectedVariant.id, 1);
      setAddedMessage('Added to Bag');
      setTimeout(() => setAddedMessage(null), 2500);
    } catch (err) {
      Alert.alert('Error', err.message || 'Could not add to bag.');
    } finally {
      setIsAdding(false);
    }
  };

  const handleBuyNow = async () => {
    if (!selectedVariant) {
      Alert.alert('Selection Required', 'Please select a size and color.');
      return;
    }

    if (!isCurrentVariantInStock) {
      Alert.alert('Sold Out', 'This size and color combination is currently sold out.');
      return;
    }

    try {
      setIsAdding(true);
      await addToCart(selectedVariant.id, 1);

      if (!isAuthenticated) {
        Alert.alert(
          'Client Sign In Required',
          'This footwear has been reserved in your bag. Please sign in or register to complete your order and arrange white-glove courier delivery.',
          [
            {
              text: 'Sign In / Register',
              onPress: () => router.push({ pathname: '/auth/login', params: { redirect: '/checkout' } }),
            },
            { text: 'View Bag', onPress: () => router.push('/(tabs)/cart') },
          ]
        );
        return;
      }

      router.push('/checkout');
    } catch (err) {
      Alert.alert('Error', err.message || 'Could not add to bag.');
    } finally {
      setIsAdding(false);
    }
  };

  const images = product.images && product.images.length > 0 ? product.images : [product.primary_image];

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]} edges={['top']}>
      <Header showBack={true} title={product.category?.name?.toUpperCase() || 'ÉLEVÉ'} />

      <ScrollView style={styles.container} showsVerticalScrollIndicator={false}>
        {/**/}
        <View style={[styles.sliderContainer, { backgroundColor: colors.imageBacking }]}>
          <ScrollView
            horizontal
            pagingEnabled
            showsHorizontalScrollIndicator={false}
            onScroll={(e) => {
              const slide = Math.round(e.nativeEvent.contentOffset.x / width);
              setActiveImageIndex(slide);
            }}
            scrollEventThrottle={16}
          >
            {images.map((imgUrl, index) => (
              <Image
                key={index}
                source={{ uri: imgUrl }}
                style={styles.sliderImage}
                contentFit="cover"
                transition={200}
              />
            ))}
          </ScrollView>

          {/**/}
          <TouchableOpacity
            style={[styles.heartBtn, { backgroundColor: colors.badgeBg, borderColor: colors.cardBorder, borderWidth: 1 }]}
            onPress={() => toggleWishlist(product)}
            activeOpacity={0.8}
          >
            <Ionicons
              name={favorite ? 'heart' : 'heart-outline'}
              size={20}
              color={favorite ? '#DC2626' : colors.textPrimary}
            />
          </TouchableOpacity>

          {/**/}
          {images.length > 1 && (
            <View style={styles.pagination}>
              {images.map((_, i) => (
                <View
                  key={i}
                  style={[
                    styles.paginationDot,
                    { backgroundColor: isDark ? 'rgba(255, 255, 255, 0.25)' : 'rgba(23, 23, 23, 0.25)' },
                    activeImageIndex === i && [styles.paginationDotActive, { backgroundColor: colors.textPrimary }],
                  ]}
                />
              ))}
            </View>
          )}
        </View>

        {/**/}
        <View style={styles.content}>
          <View style={styles.titleRow}>
            <View style={{ flex: 1, marginRight: 10 }}>
              <Text style={[styles.categoryBadge, { color: colors.accent }]}>{product.category?.name?.toUpperCase()}</Text>
              <Text style={[styles.productName, { color: colors.textPrimary }]}>{product.name}</Text>
            </View>

            <Text style={[styles.price, { color: colors.textPrimary }]}>
              {formatPrice(selectedVariant?.effective_price || product.base_price)}
            </Text>
          </View>

          {/**/}
          <View style={styles.stockAlertContainer}>
            {isCurrentVariantInStock ? (
              currentStockCount <= 5 ? (
                <View style={styles.lowStockRow}>
                  <View style={styles.amberDot} />
                  <Text style={styles.lowStockText}>
                    Only {currentStockCount} pair{currentStockCount === 1 ? '' : 's'} remaining in Size {selectedSize}
                  </Text>
                </View>
              ) : (
                <View style={styles.inStockRow}>
                  <View style={styles.greenDot} />
                  <Text style={styles.inStockText}>In Stock &bull; Ready for Atelier Dispatch</Text>
                </View>
              )
            ) : (
              <View style={styles.outOfStockRow}>
                <View style={styles.redDot} />
                <Text style={styles.outOfStockText}>Currently Unavailable in selected specification</Text>
              </View>
            )}
          </View>

          {/**/}
          {product.available_colors && product.available_colors.length > 0 && (
            <ColorSelector
              colors={product.available_colors}
              selectedColor={selectedColor}
              onSelectColor={setSelectedColor}
            />
          )}

          {/**/}
          {product.available_sizes && product.available_sizes.length > 0 && (
            <SizeSelector
              sizes={product.available_sizes}
              selectedSize={selectedSize}
              onSelectSize={setSelectedSize}
              availableSizes={sizesForColor}
            />
          )}

          {/**/}
          <View style={styles.detailsSection}>
            <Text style={[styles.sectionHeading, { color: colors.accent }]}>ATELIER CRAFTSMANSHIP</Text>
            <Text style={[styles.descriptionText, { color: colors.textSecondary }]}>{product.description}</Text>

            <View style={[styles.specsList, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
              <View style={styles.specItem}>
                <Ionicons name="sparkles-outline" size={16} color={colors.accent} />
                <Text style={[styles.specText, { color: colors.textSecondary }]}>Full-grain vegetable-tanned Italian calfskin</Text>
              </View>
              <View style={styles.specItem}>
                <Ionicons name="shield-checkmark-outline" size={16} color={colors.accent} />
                <Text style={[styles.specText, { color: colors.textSecondary }]}>Goodyear-welted arch construction with memory foam insole</Text>
              </View>
              <View style={styles.specItem}>
                <Ionicons name="cube-outline" size={16} color={colors.accent} />
                <Text style={[styles.specText, { color: colors.textSecondary }]}>Delivered in bespoke linen preservation dust bags</Text>
              </View>
            </View>
          </View>
        </View>
      </ScrollView>

      {/**/}
      <View style={[styles.bottomBar, { backgroundColor: colors.surface, borderTopColor: colors.borderSubtle }]}>
        <TouchableOpacity
          style={[styles.addToBagBtn, { backgroundColor: colors.textPrimary }, !isCurrentVariantInStock && styles.btnDisabled]}
          onPress={handleAddToBag}
          disabled={isAdding || !isCurrentVariantInStock}
          activeOpacity={0.85}
        >
          {isAdding ? (
            <ActivityIndicator size="small" color={colors.background} />
          ) : (
            <Text style={[styles.addToBagText, { color: colors.background }]}>
              {addedMessage || (isCurrentVariantInStock ? 'Add to Bag' : 'Sold Out')}
            </Text>
          )}
        </TouchableOpacity>

        {isCurrentVariantInStock && (
          <TouchableOpacity
            style={[styles.buyNowBtn, { backgroundColor: colors.surface, borderColor: colors.textPrimary }]}
            onPress={handleBuyNow}
            disabled={isAdding}
            activeOpacity={0.85}
          >
            <Text style={[styles.buyNowText, { color: colors.textPrimary }]}>Buy Now</Text>
          </TouchableOpacity>
        )}
      </View>
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
  centerContainer: {
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
  backBtn: {
    marginTop: 12,
    paddingHorizontal: 20,
    paddingVertical: 10,
    backgroundColor: COLORS.obsidian,
    borderRadius: 8,
  },
  backBtnText: {
    color: COLORS.ivory,
    fontSize: 11,
    fontWeight: 'bold',
  },
  sliderContainer: {
    width: width,
    height: width * 1.05,
    backgroundColor: '#ECE8E1',
    position: 'relative',
  },
  sliderImage: {
    width: width,
    height: width * 1.05,
  },
  heartBtn: {
    position: 'absolute',
    top: 16,
    right: 16,
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  pagination: {
    position: 'absolute',
    bottom: 12,
    left: 0,
    right: 0,
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 6,
  },
  paginationDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: 'rgba(23, 23, 23, 0.25)',
  },
  paginationDotActive: {
    width: 20,
    backgroundColor: COLORS.obsidian,
  },
  content: {
    padding: 20,
    paddingBottom: 40,
  },
  titleRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 8,
  },
  categoryBadge: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 2,
    color: COLORS.textMuted,
    marginBottom: 4,
  },
  productName: {
    fontFamily: 'serif',
    fontSize: 22,
    fontWeight: '700',
    color: COLORS.obsidian,
    lineHeight: 28,
  },
  price: {
    fontFamily: 'serif',
    fontSize: 22,
    fontWeight: '700',
    color: COLORS.obsidian,
  },
  stockAlertContainer: {
    marginVertical: 10,
  },
  inStockRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  greenDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#10B981',
  },
  inStockText: {
    fontSize: 11,
    color: '#047857',
    fontWeight: '600',
  },
  lowStockRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  amberDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#F59E0B',
  },
  lowStockText: {
    fontSize: 11,
    color: '#B45309',
    fontWeight: '700',
  },
  outOfStockRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  redDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#EF4444',
  },
  outOfStockText: {
    fontSize: 11,
    color: '#B91C1C',
    fontWeight: '600',
  },
  detailsSection: {
    marginTop: 20,
    paddingTop: 20,
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
  },
  sectionHeading: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 1.5,
    color: COLORS.textMuted,
    marginBottom: 10,
  },
  descriptionText: {
    fontSize: 13,
    color: COLORS.textSecondary,
    lineHeight: 22,
    marginBottom: 16,
  },
  specsList: {
    gap: 10,
    backgroundColor: '#FFFFFF',
    padding: 16,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  specItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  specText: {
    fontSize: 12,
    color: COLORS.textSecondary,
    flex: 1,
  },
  bottomBar: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 14,
    backgroundColor: '#FFFFFF',
    borderTopWidth: 1,
    borderTopColor: COLORS.border,
    gap: 12,
  },
  addToBagBtn: {
    flex: 1,
    height: 48,
    backgroundColor: COLORS.obsidian,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  addToBagText: {
    color: COLORS.ivory,
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  buyNowBtn: {
    flex: 1,
    height: 48,
    backgroundColor: '#FFFFFF',
    borderRadius: 8,
    borderWidth: 1.5,
    borderColor: COLORS.obsidian,
    alignItems: 'center',
    justifyContent: 'center',
  },
  buyNowText: {
    color: COLORS.obsidian,
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  btnDisabled: {
    backgroundColor: '#A8A29E',
  },
});
