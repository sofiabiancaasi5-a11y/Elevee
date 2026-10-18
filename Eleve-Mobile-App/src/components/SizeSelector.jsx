import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { useTheme } from '../context/ThemeContext';
import { COLORS } from '../constants/theme';

export default function SizeSelector({ sizes = [], selectedSize, onSelectSize, availableSizes = [] }) {
  const { colors } = useTheme();

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={[styles.title, { color: colors.textPrimary }]}>SELECT SIZE</Text>
        <Text style={[styles.guide, { color: colors.textMuted }]}>US Sizing</Text>
      </View>

      <View style={styles.grid}>
        {sizes.map((size) => {
          const isAvailable = availableSizes.length === 0 || availableSizes.includes(size);
          const isSelected = selectedSize === size;

          return (
            <TouchableOpacity
              key={size}
              style={[
                styles.sizeBox,
                {
                  backgroundColor: colors.surface,
                  borderColor: colors.border,
                },
                isSelected && [styles.sizeBoxSelected, { backgroundColor: colors.textPrimary, borderColor: colors.textPrimary }],
                !isAvailable && [styles.sizeBoxDisabled, { backgroundColor: colors.surfaceSubtle, borderColor: colors.borderSubtle }],
              ]}
              onPress={() => isAvailable && onSelectSize(size)}
              disabled={!isAvailable}
              activeOpacity={0.7}
            >
              <Text
                style={[
                  styles.sizeText,
                  { color: colors.textPrimary },
                  isSelected && [styles.sizeTextSelected, { color: colors.background }],
                  !isAvailable && styles.sizeTextDisabled,
                ]}
              >
                {size}
              </Text>
            </TouchableOpacity>
          );
        })}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    marginVertical: 12,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  title: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.obsidian,
    letterSpacing: 1.5,
  },
  guide: {
    fontSize: 11,
    color: COLORS.textMuted,
    textDecorationLine: 'underline',
  },
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  sizeBox: {
    width: 48,
    height: 48,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: COLORS.border,
    backgroundColor: '#FFFFFF',
    alignItems: 'center',
    justifyContent: 'center',
  },
  sizeBoxSelected: {
    backgroundColor: COLORS.obsidian,
    borderColor: COLORS.obsidian,
  },
  sizeBoxDisabled: {
    backgroundColor: '#F3EFEA',
    borderColor: '#E8E2D9',
    opacity: 0.5,
  },
  sizeText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.obsidian,
  },
  sizeTextSelected: {
    color: COLORS.ivory,
  },
  sizeTextDisabled: {
    color: '#A8A29E',
    textDecorationLine: 'line-through',
  },
});
