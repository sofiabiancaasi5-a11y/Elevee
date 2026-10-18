import React from 'react';
import { TouchableOpacity, Text, StyleSheet, ActivityIndicator } from 'react-native';
import { COLORS } from '../constants/theme';

export default function LuxuryButton({
  title,
  onPress,
  variant = 'primary', // primary | secondary | outline
  isLoading = false,
  disabled = false,
  style,
  textStyle,
}) {
  const isPrimary = variant === 'primary';
  const isOutline = variant === 'outline';

  return (
    <TouchableOpacity
      style={[
        styles.button,
        isPrimary && styles.btnPrimary,
        variant === 'secondary' && styles.btnSecondary,
        isOutline && styles.btnOutline,
        (disabled || isLoading) && styles.btnDisabled,
        style,
      ]}
      onPress={onPress}
      disabled={disabled || isLoading}
      activeOpacity={0.85}
    >
      {isLoading ? (
        <ActivityIndicator size="small" color={isPrimary ? COLORS.ivory : COLORS.obsidian} />
      ) : (
        <Text
          style={[
            styles.text,
            isPrimary && styles.textPrimary,
            variant === 'secondary' && styles.textSecondary,
            isOutline && styles.textOutline,
            textStyle,
          ]}
        >
          {title}
        </Text>
      )}
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  button: {
    height: 50,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 20,
  },
  btnPrimary: {
    backgroundColor: COLORS.obsidian,
  },
  btnSecondary: {
    backgroundColor: '#FFFFFF',
    borderWidth: 1,
    borderColor: COLORS.border,
  },
  btnOutline: {
    backgroundColor: 'transparent',
    borderWidth: 1.5,
    borderColor: COLORS.obsidian,
  },
  btnDisabled: {
    opacity: 0.5,
  },
  text: {
    fontSize: 12,
    fontWeight: '700',
    letterSpacing: 2,
    textTransform: 'uppercase',
  },
  textPrimary: {
    color: COLORS.ivory,
  },
  textSecondary: {
    color: COLORS.obsidian,
  },
  textOutline: {
    color: COLORS.obsidian,
  },
});
