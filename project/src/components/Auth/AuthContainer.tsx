import React, { useState } from 'react';
import { LoginForm } from './LoginForm';
import { RegisterForm } from './RegisterForm';
import { ForgotPasswordForm } from './ForgotPasswordForm';
import { EmailConfirmationForm } from './EmailConfirmationForm';

export type AuthView = 'login' | 'register' | 'forgot-password' | 'email-confirmation';

export function AuthContainer() {
  const [currentView, setCurrentView] = useState<AuthView>('login');
  const [userEmail, setUserEmail] = useState('');

  const handleViewChange = (view: AuthView, email?: string) => {
    setCurrentView(view);
    if (email) setUserEmail(email);
  };

  const renderCurrentView = () => {
    switch (currentView) {
      case 'login':
        return <LoginForm onViewChange={handleViewChange} />;
      case 'register':
        return <RegisterForm onViewChange={handleViewChange} />;
      case 'forgot-password':
        return <ForgotPasswordForm onViewChange={handleViewChange} />;
      case 'email-confirmation':
        return <EmailConfirmationForm email={userEmail} onViewChange={handleViewChange} />;
      default:
        return <LoginForm onViewChange={handleViewChange} />;
    }
  };

  return renderCurrentView();
}