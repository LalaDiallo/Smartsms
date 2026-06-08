import React, { useState, useEffect } from 'react';
import { Mail, ArrowLeft, CheckCircle, RefreshCw, MessageSquare, Clock } from 'lucide-react';
import { AuthView } from './AuthContainer';

interface EmailConfirmationFormProps {
  email: string;
  onViewChange: (view: AuthView) => void;
}

export function EmailConfirmationForm({ email, onViewChange }: EmailConfirmationFormProps) {
  const [timeLeft, setTimeLeft] = useState(60);
  const [canResend, setCanResend] = useState(false);
  const [isResending, setIsResending] = useState(false);

  useEffect(() => {
    if (timeLeft > 0) {
      const timer = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
      return () => clearTimeout(timer);
    } else {
      setCanResend(true);
    }
  }, [timeLeft]);

  const handleResendEmail = async () => {
    setIsResending(true);
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));
      setTimeLeft(60);
      setCanResend(false);
    } catch (error) {
      console.error('Resend failed:', error);
    } finally {
      setIsResending(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-4">
      <div className="max-w-md w-full">
        {/* Logo & Header */}
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-blue-600 rounded-2xl mb-6 shadow-lg flex items-center justify-center mx-auto relative">
            <MessageSquare className="w-8 h-8 text-white" />
            <div className="absolute -top-2 -right-2 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
              <Mail className="w-3 h-3 text-white" />
            </div>
          </div>
          
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Vérifiez votre email
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Un email de confirmation a été envoyé à votre adresse
          </p>
        </div>

        {/* Confirmation Content */}
        <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700">
          <div className="text-center space-y-6">
            {/* Email Address */}
            <div className="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
              <div className="flex items-center justify-center space-x-2 mb-2">
                <Mail className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                <span className="text-sm font-medium text-blue-800 dark:text-blue-200">
                  Email envoyé à
                </span>
              </div>
              <p className="text-blue-900 dark:text-blue-100 font-semibold">
                {email}
              </p>
            </div>

            {/* Instructions */}
            <div className="space-y-4">
              <div className="flex items-start space-x-3 text-left">
                <div className="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                  <span className="text-xs font-bold text-blue-600 dark:text-blue-400">1</span>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Ouvrez votre boîte mail et recherchez un email de SMSPro
                </p>
              </div>
              
              <div className="flex items-start space-x-3 text-left">
                <div className="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                  <span className="text-xs font-bold text-blue-600 dark:text-blue-400">2</span>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Cliquez sur le lien de confirmation dans l'email
                </p>
              </div>
              
              <div className="flex items-start space-x-3 text-left">
                <div className="w-6 h-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                  <span className="text-xs font-bold text-blue-600 dark:text-blue-400">3</span>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Votre compte sera activé et vous pourrez vous connecter
                </p>
              </div>
            </div>

            {/* Resend Section */}
            <div className="pt-4 border-t border-gray-200 dark:border-gray-700">
              <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Vous n'avez pas reçu l'email ?
              </p>
              
              {canResend ? (
                <button
                  onClick={handleResendEmail}
                  disabled={isResending}
                  className="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-xl transition-colors disabled:opacity-50 flex items-center justify-center space-x-2"
                >
                  {isResending ? (
                    <>
                      <RefreshCw className="w-4 h-4 animate-spin" />
                      <span>Envoi en cours...</span>
                    </>
                  ) : (
                    <>
                      <RefreshCw className="w-4 h-4" />
                      <span>Renvoyer l'email</span>
                    </>
                  )}
                </button>
              ) : (
                <div className="flex items-center justify-center space-x-2 text-gray-500 dark:text-gray-400">
                  <Clock className="w-4 h-4" />
                  <span className="text-sm">
                    Renvoyer dans {timeLeft}s
                  </span>
                </div>
              )}
            </div>

            {/* Help Text */}
            <div className="text-xs text-gray-500 dark:text-gray-400 space-y-1">
              <p>💡 Vérifiez votre dossier spam si vous ne trouvez pas l'email</p>
              <p>📧 L'email peut prendre quelques minutes à arriver</p>
            </div>
          </div>

          {/* Back to Login */}
          <div className="mt-6 text-center">
            <button
              onClick={() => onViewChange('login')}
              className="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors flex items-center justify-center space-x-2 mx-auto"
            >
              <ArrowLeft className="w-4 h-4" />
              <span>Retour à la connexion</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}