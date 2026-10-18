import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet } from 'react-native';
import { useTheme } from '../context/ThemeContext';
import { COLORS } from '../constants/theme';

export default function ColorSelector({ colors = [], selectedColor, onSelectColor }) {
  const { colors: themeColors } = useTheme();

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={[styles.title, { color: themeColors.textPrimary }]}>SHADE</Text>
        <Text style={[styles.selectedName, { color: themeColors.accent }]}>{selectedColor || 'Choose'}</Text>
      </View>

      <View style={styles.row}>
        {colors.map((colorObj, index) => {
          const name = typeof colorObj === 'string' ? colorObj : colorObj.name;
          const hex = typeof colorObj === 'object' ? colorObj.hex : '#171717';
          const isSelected = selectedColor === name;

          return (
            <TouchableOpacity
              key={index}
              style={[
                styles.swatchWrapper,
                isSelected && [styles.swatchWrapperSelected, { borderColor: themeColors.textPrimary }],
              ]}
              onPress={() => onSelectColor(name)}
              activeOpacity={0.8}
            >
              <View style={[styles.swatch, { backgroundColor: hex, borderColor: themeColors.border }]} />
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
  selectedName: {
    fontSize: 12,
    fontWeight: '500',
    color: COLORS.textSecondary,
    fontFamily: 'serif',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  swatchWrapper: {
    width: 36,
    height: 36,
    borderRadius: 18,
    borderWidth: 1.5,
    borderColor: 'transparent',
    alignItems: 'center',
    justifyContent: 'center',
  },
  swatchWrapperSelected: {
    borderColor: COLORS.obsidian,
  },
  swatch: {
    width: 26,
    height: 26,
    borderRadius: 13,
    borderWidth: 1,
    borderColor: '#D8D0C4',
  },
});
