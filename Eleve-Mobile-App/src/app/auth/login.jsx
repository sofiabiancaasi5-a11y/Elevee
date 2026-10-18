import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Alert,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import { useTheme } from '../../context/ThemeContext';
import { COLORS } from '../../constants/theme';

export default function LoginScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const { login } = useAuth();
  const { colors, isDark } = useTheme();

  const [email, setEmail] = useState('customer@eleve.com');
  const [password, setPassword] = useState('password');
  const [isLoading, setIsLoading] = useState(false);

  const handleLogin = async () => {
    if (!email.trim() || !password.trim()) {
      Alert.alert('Required', 'Please enter your email and password.');
      return;
    }

    try {
      setIsLoading(true);
      await login(email.trim(), password);

      // Seamless post-login redirection (e.g. back to /checkout)
      if (params.redirect) {
        router.replace(params.redirect);
      } else {
        router.back();
      }
    } catch (err) {
      Alert.alert('Sign In Failed', err.message || 'Invalid email or password.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        style={styles.container}
      >
        <ScrollView contentContainerStyle={styles.content} showsVerticalScrollIndicator={false}>
          <TouchableOpacity style={styles.closeBtn} onPress={() => router.back()}>
            <Ionicons name="close" size={24} color={colors.textPrimary} />
          </TouchableOpacity>

          <View style={styles.header}>
            <View style={[styles.monogram, { backgroundColor: colors.monogramBg }]}>
              <Text style={[styles.monogramText, { color: colors.monogramText }]}>É</Text>
            </View>
            <Text style={[styles.title, { color: colors.textPrimary }]}>Atelier Sign In</Text>
            <Text style={[styles.subtitle, { color: colors.textSecondary }]}>
              {params.redirect === '/checkout'
                ? 'Sign in to finalize your footwear reservation and white-glove delivery.'
                : 'Welcome back to the ÉLEVÉ customer portal.'}
            </Text>
          </View>

          <View style={styles.form}>
            <View style={styles.formGroup}>
              <Text style={[styles.label, { color: colors.textMuted }]}>EMAIL ADDRESS</Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                    color: colors.textPrimary,
                  },
                ]}
                value={email}
                onChangeText={setEmail}
                keyboardType="email-address"
                autoCapitalize="none"
                placeholder="customer@eleve.com"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <View style={styles.formGroup}>
              <Text style={[styles.label, { color: colors.textMuted }]}>PASSWORD</Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surfaceSubtle,
                    borderColor: colors.border,
                    color: colors.textPrimary,
                  },
                ]}
                value={password}
                onChangeText={setPassword}
                secureTextEntry
                placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                placeholderTextColor={colors.textMuted}
              />
            </View>

            <TouchableOpacity
              style={[styles.submitBtn, { backgroundColor: colors.textPrimary }]}
              onPress={handleLogin}
              disabled={isLoading}
              activeOpacity={0.85}
            >
              {isLoading ? (
                <ActivityIndicator size="small" color={colors.background} />
              ) : (
                <Text style={[styles.submitBtnText, { color: colors.background }]}>Sign In</Text>
              )}
            </TouchableOpacity>

            <View
              style={[
                styles.demoCredentialsBox,
                {
                  backgroundColor: colors.backgroundSubtle,
                  borderColor: colors.border,
                },
              ]}
            >
              <Text style={[styles.demoTitle, { color: colors.accent }]}>TEST CUSTOMER CREDENTIALS</Text>
              <Text style={[styles.demoText, { color: colors.textSecondary }]}>
                Email: customer@eleve.com &bull; Password: password
              </Text>
            </View>

            <View style={styles.footerLinkRow}>
              <Text style={[styles.footerText, { color: colors.textMuted }]}>New to ÉLEVÉ?</Text>
              <TouchableOpacity
                onPress={() =>
                  router.replace({
                    pathname: '/auth/register',
                    params: params.redirect ? { redirect: params.redirect } : {},
                  })
                }
              >
                <Text style={[styles.footerLink, { color: colors.textPrimary }]}>Create an Account</Text>
              </TouchableOpacity>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
  },
  container: {
    flex: 1,
  },
  content: {
    padding: 24,
    justifyContent: 'center',
    minHeight: '100%',
  },
  closeBtn: {
    position: 'absolute',
    top: 10,
    right: 16,
    padding: 8,
    zIndex: 10,
  },
  header: {
    alignItems: 'center',
    marginBottom: 32,
  },
  monogram: {
    width: 56,
    height: 56,
    borderRadius: 28,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 16,
  },
  monogramText: {
    fontFamily: 'serif',
    fontSize: 28,
    fontWeight: 'bold',
  },
  title: {
    fontFamily: 'serif',
    fontSize: 24,
    fontWeight: '700',
    marginBottom: 6,
  },
  subtitle: {
    fontSize: 12,
    textAlign: 'center',
    maxWidth: 280,
    lineHeight: 18,
  },
  form: {
    gap: 16,
  },
  formGroup: {
    gap: 6,
  },
  label: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 1.5,
  },
  input: {
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: 14,
    paddingVertical: 12,
    fontSize: 13,
  },
  submitBtn: {
    height: 50,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 8,
  },
  submitBtnText: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1.5,
    textTransform: 'uppercase',
  },
  demoCredentialsBox: {
    borderRadius: 8,
    borderWidth: 1,
    padding: 12,
    alignItems: 'center',
    marginTop: 4,
  },
  demoTitle: {
    fontSize: 9,
    fontWeight: '700',
    letterSpacing: 1,
    marginBottom: 2,
  },
  demoText: {
    fontSize: 11,
    fontFamily: 'monospace',
  },
  footerLinkRow: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 6,
    marginTop: 10,
  },
  footerText: {
    fontSize: 12,
  },
  footerLink: {
    fontSize: 12,
    fontWeight: '700',
    textDecorationLine: 'underline',
  },
});
