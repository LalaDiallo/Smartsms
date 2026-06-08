import React, { useState } from 'react';
import { 
  Settings, 
  User, 
  Bell, 
  Palette, 
  Globe, 
  Shield,
  CreditCard,
  Smartphone,
  Mail,
  Key,
  Save,
  Upload,
  Download,
  Trash2,
  Edit
} from 'lucide-react';

export function PlatformSettings() {
  const [activeTab, setActiveTab] = useState<'profile' | 'notifications' | 'appearance' | 'integrations' | 'billing' | 'security'>('profile');

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            Paramètres
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Configurez votre compte et personnalisez votre expérience
          </p>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200 dark:border-gray-700">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'profile', label: 'Profil', icon: User },
            { id: 'notifications', label: 'Notifications', icon: Bell },
            { id: 'appearance', label: 'Apparence', icon: Palette },
            { id: 'integrations', label: 'Intégrations', icon: Globe },
            { id: 'billing', label: 'Facturation', icon: CreditCard },
            { id: 'security', label: 'Sécurité', icon: Shield }
          ].map((tab) => {
            const Icon = tab.icon;
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id as any)}
                className={`py-2 px-1 border-b-2 font-medium text-sm flex items-center space-x-2 ${
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                }`}
              >
                <Icon size={16} />
                <span>{tab.label}</span>
              </button>
            );
          })}
        </nav>
      </div>

      {/* Profile Tab */}
      {activeTab === 'profile' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
              Informations personnelles
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="md:col-span-2 flex items-center space-x-6">
                <div className="relative">
                  <img
                    src="https://images.pexels.com/photos/415829/pexels-photo-415829.jpeg?auto=compress&cs=tinysrgb&w=200"
                    alt="Profile"
                    className="w-20 h-20 rounded-full object-cover"
                  />
                  <button className="absolute bottom-0 right-0 p-1 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors">
                    <Edit size={12} />
                  </button>
                </div>
                <div>
                  <h4 className="font-medium text-gray-900 dark:text-white">Photo de profil</h4>
                  <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">
                    JPG, PNG ou GIF. Taille maximale 2MB.
                  </p>
                  <div className="flex space-x-2">
                    <button className="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                      Changer
                    </button>
                    <button className="px-3 py-1 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                      Supprimer
                    </button>
                  </div>
                </div>
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Prénom
                </label>
                <input
                  type="text"
                  defaultValue="Sarah"
                  className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Nom
                </label>
                <input
                  type="text"
                  defaultValue="Johnson"
                  className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Email
                </label>
                <input
                  type="email"
                  defaultValue="sarah@company.com"
                  className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Téléphone
                </label>
                <input
                  type="tel"
                  defaultValue="+33 6 12 34 56 78"
                  className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Entreprise
                </label>
                <input
                  type="text"
                  defaultValue="SMSPro Solutions"
                  className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Poste
                </label>
                <input
                  type="text"
                  defaultValue="Directrice Marketing"
                  className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                />
              </div>
              
              <div className="md:col-span-2">
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Bio
                </label>
                <textarea
                  rows={3}
                  defaultValue="Experte en marketing digital avec plus de 10 ans d'expérience dans l'optimisation des campagnes SMS et multicanal."
                  className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                />
              </div>
            </div>
            
            <div className="flex justify-end mt-6">
              <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <Save size={16} />
                <span>Sauvegarder</span>
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Notifications Tab */}
      {activeTab === 'notifications' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
              Préférences de notification
            </h3>
            
            <div className="space-y-6">
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Notifications par email
                </h4>
                <div className="space-y-3">
                  {[
                    { name: 'Nouvelles campagnes', description: 'Recevoir un email lors de la création d\'une campagne', enabled: true },
                    { name: 'Rapports hebdomadaires', description: 'Résumé des performances chaque lundi', enabled: true },
                    { name: 'Alertes de sécurité', description: 'Notifications importantes de sécurité', enabled: true },
                    { name: 'Mises à jour produit', description: 'Nouvelles fonctionnalités et améliorations', enabled: false }
                  ].map((notification, index) => (
                    <div key={index} className="flex items-start justify-between">
                      <div className="flex-1">
                        <p className="text-sm font-medium text-gray-900 dark:text-white">
                          {notification.name}
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                          {notification.description}
                        </p>
                      </div>
                      <button
                        className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ml-4 ${
                          notification.enabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                        }`}
                      >
                        <span
                          className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                            notification.enabled ? 'translate-x-6' : 'translate-x-1'
                          }`}
                        />
                      </button>
                    </div>
                  ))}
                </div>
              </div>
              
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Notifications push
                </h4>
                <div className="space-y-3">
                  {[
                    { name: 'Campagnes terminées', description: 'Notification quand une campagne se termine', enabled: true },
                    { name: 'Seuils d\'alerte', description: 'Alertes quand les métriques dépassent les seuils', enabled: true },
                    { name: 'Nouveaux contacts', description: 'Notification pour chaque nouveau contact', enabled: false }
                  ].map((notification, index) => (
                    <div key={index} className="flex items-start justify-between">
                      <div className="flex-1">
                        <p className="text-sm font-medium text-gray-900 dark:text-white">
                          {notification.name}
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                          {notification.description}
                        </p>
                      </div>
                      <button
                        className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ml-4 ${
                          notification.enabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                        }`}
                      >
                        <span
                          className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                            notification.enabled ? 'translate-x-6' : 'translate-x-1'
                          }`}
                        />
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Appearance Tab */}
      {activeTab === 'appearance' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
              Personnalisation de l'interface
            </h3>
            
            <div className="space-y-6">
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Thème
                </h4>
                <div className="grid grid-cols-3 gap-4">
                  {[
                    { name: 'Clair', value: 'light', preview: 'bg-white border-2 border-blue-500' },
                    { name: 'Sombre', value: 'dark', preview: 'bg-gray-900 border-2 border-gray-600' },
                    { name: 'Auto', value: 'auto', preview: 'bg-gradient-to-r from-white to-gray-900 border-2 border-gray-400' }
                  ].map((theme) => (
                    <button
                      key={theme.value}
                      className="p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-colors"
                    >
                      <div className={`w-full h-16 rounded ${theme.preview} mb-2`} />
                      <p className="text-sm font-medium text-gray-900 dark:text-white">
                        {theme.name}
                      </p>
                    </button>
                  ))}
                </div>
              </div>
              
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Couleur d'accent
                </h4>
                <div className="flex space-x-3">
                  {[
                    'bg-blue-600',
                    'bg-emerald-600',
                    'bg-purple-600',
                    'bg-amber-600',
                    'bg-red-600',
                    'bg-pink-600'
                  ].map((color, index) => (
                    <button
                      key={index}
                      className={`w-8 h-8 rounded-full ${color} ${
                        index === 0 ? 'ring-2 ring-offset-2 ring-blue-600' : ''
                      }`}
                    />
                  ))}
                </div>
              </div>
              
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Densité d'affichage
                </h4>
                <div className="space-y-2">
                  {[
                    { name: 'Compact', value: 'compact' },
                    { name: 'Normal', value: 'normal' },
                    { name: 'Confortable', value: 'comfortable' }
                  ].map((density) => (
                    <label key={density.value} className="flex items-center">
                      <input
                        type="radio"
                        name="density"
                        value={density.value}
                        defaultChecked={density.value === 'normal'}
                        className="mr-3 text-blue-600 focus:ring-blue-500"
                      />
                      <span className="text-sm text-gray-700 dark:text-gray-300">
                        {density.name}
                      </span>
                    </label>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Integrations Tab */}
      {activeTab === 'integrations' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {[
              { name: 'WhatsApp Business', status: 'connected', icon: Smartphone, color: 'text-emerald-600' },
              { name: 'Mailchimp', status: 'disconnected', icon: Mail, color: 'text-blue-600' },
              { name: 'Zapier', status: 'connected', icon: Globe, color: 'text-amber-600' },
              { name: 'Slack', status: 'disconnected', icon: Bell, color: 'text-purple-600' }
            ].map((integration, index) => (
              <div
                key={index}
                className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
              >
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center space-x-3">
                    <integration.icon className={`w-8 h-8 ${integration.color}`} />
                    <div>
                      <h3 className="font-semibold text-gray-900 dark:text-white">
                        {integration.name}
                      </h3>
                      <span className={`text-xs px-2 py-1 rounded-full ${
                        integration.status === 'connected'
                          ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'
                          : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
                      }`}>
                        {integration.status === 'connected' ? 'Connecté' : 'Déconnecté'}
                      </span>
                    </div>
                  </div>
                  <button className={`px-3 py-1 text-sm rounded-lg transition-colors ${
                    integration.status === 'connected'
                      ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/20 dark:text-red-400'
                      : 'bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900/20 dark:text-blue-400'
                  }`}>
                    {integration.status === 'connected' ? 'Déconnecter' : 'Connecter'}
                  </button>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  {integration.status === 'connected'
                    ? 'Intégration active et synchronisée'
                    : 'Connectez cette intégration pour étendre vos fonctionnalités'
                  }
                </p>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Billing Tab */}
      {activeTab === 'billing' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
              Plan actuel
            </h3>
            <div className="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
              <div>
                <h4 className="font-semibold text-blue-900 dark:text-blue-100">
                  Plan Professionnel
                </h4>
                <p className="text-sm text-blue-700 dark:text-blue-300">
                  50,000 SMS/mois • Support prioritaire • Analyses avancées
                </p>
              </div>
              <div className="text-right">
                <p className="text-2xl font-bold text-blue-600 dark:text-blue-400">
                  €99/mois
                </p>
                <p className="text-sm text-blue-700 dark:text-blue-300">
                  Facturé annuellement
                </p>
              </div>
            </div>
            
            <div className="mt-6 flex space-x-3">
              <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                Changer de plan
              </button>
              <button className="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                Voir la facturation
              </button>
            </div>
          </div>
          
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
              Méthode de paiement
            </h3>
            <div className="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
              <CreditCard className="w-8 h-8 text-gray-600 dark:text-gray-400" />
              <div className="flex-1">
                <p className="font-medium text-gray-900 dark:text-white">
                  •••• •••• •••• 4242
                </p>
                <p className="text-sm text-gray-500 dark:text-gray-400">
                  Expire 12/2025
                </p>
              </div>
              <button className="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                Modifier
              </button>
            </div>
          </div>
        </div>
      )}

      {/* Security Tab */}
      {activeTab === 'security' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
              Sécurité du compte
            </h3>
            
            <div className="space-y-6">
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Mot de passe
                </h4>
                <div className="space-y-3">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Mot de passe actuel
                    </label>
                    <input
                      type="password"
                      className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Nouveau mot de passe
                    </label>
                    <input
                      type="password"
                      className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                      Confirmer le nouveau mot de passe
                    </label>
                    <input
                      type="password"
                      className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
                    />
                  </div>
                  <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    Changer le mot de passe
                  </button>
                </div>
              </div>
              
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Authentification à deux facteurs
                </h4>
                <div className="flex items-center justify-between p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
                  <div className="flex items-center space-x-3">
                    <Shield className="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    <div>
                      <p className="font-medium text-emerald-900 dark:text-emerald-100">
                        2FA activé
                      </p>
                      <p className="text-sm text-emerald-700 dark:text-emerald-300">
                        Votre compte est protégé par l'authentification à deux facteurs
                      </p>
                    </div>
                  </div>
                  <button className="text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 text-sm font-medium">
                    Gérer
                  </button>
                </div>
              </div>
              
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-4">
                  Sessions actives
                </h4>
                <div className="space-y-3">
                  {[
                    { device: 'MacBook Pro', location: 'Paris, France', current: true, lastActive: 'Maintenant' },
                    { device: 'iPhone 13', location: 'Paris, France', current: false, lastActive: 'Il y a 2h' },
                    { device: 'Chrome Windows', location: 'Lyon, France', current: false, lastActive: 'Il y a 1 jour' }
                  ].map((session, index) => (
                    <div key={index} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                      <div>
                        <p className="font-medium text-gray-900 dark:text-white">
                          {session.device}
                          {session.current && (
                            <span className="ml-2 text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full dark:bg-emerald-900/20 dark:text-emerald-400">
                              Session actuelle
                            </span>
                          )}
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                          {session.location} • {session.lastActive}
                        </p>
                      </div>
                      {!session.current && (
                        <button className="text-red-600 hover:text-red-700 dark:text-red-400 text-sm">
                          Déconnecter
                        </button>
                      )}
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}