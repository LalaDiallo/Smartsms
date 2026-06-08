import React, { useState } from 'react';
import { 
  Shield, 
  Lock, 
  Key, 
  AlertTriangle, 
  CheckCircle,
  XCircle,
  Eye,
  FileText,
  Users,
  Globe,
  Database,
  Activity,
  Settings,
  Download
} from 'lucide-react';

interface SecurityEvent {
  id: string;
  type: 'login' | 'data_access' | 'config_change' | 'alert';
  user: string;
  action: string;
  timestamp: string;
  status: 'success' | 'warning' | 'error';
  ip: string;
}

const securityEvents: SecurityEvent[] = [
  {
    id: '1',
    type: 'login',
    user: 'sarah@company.com',
    action: 'Connexion réussie',
    timestamp: '2024-01-20 14:30:25',
    status: 'success',
    ip: '192.168.1.100'
  },
  {
    id: '2',
    type: 'data_access',
    user: 'pierre@company.com',
    action: 'Export de contacts',
    timestamp: '2024-01-20 13:45:12',
    status: 'success',
    ip: '192.168.1.101'
  },
  {
    id: '3',
    type: 'alert',
    user: 'system',
    action: 'Tentative de connexion suspecte',
    timestamp: '2024-01-20 12:15:08',
    status: 'warning',
    ip: '203.0.113.42'
  }
];

const complianceChecks = [
  { name: 'RGPD', status: 'compliant', score: 98, lastCheck: '2024-01-20' },
  { name: 'ISO 27001', status: 'compliant', score: 95, lastCheck: '2024-01-19' },
  { name: 'SOC 2', status: 'compliant', score: 97, lastCheck: '2024-01-18' },
  { name: 'CCPA', status: 'warning', score: 88, lastCheck: '2024-01-17' }
];

export function SecurityCompliance() {
  const [activeTab, setActiveTab] = useState<'overview' | 'access' | 'compliance' | 'audit' | 'settings'>('overview');

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'success':
      case 'compliant':
        return <CheckCircle className="w-5 h-5 text-emerald-600" />;
      case 'warning':
        return <AlertTriangle className="w-5 h-5 text-amber-600" />;
      case 'error':
        return <XCircle className="w-5 h-5 text-red-600" />;
      default:
        return <Shield className="w-5 h-5 text-gray-600" />;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'success':
      case 'compliant':
        return 'text-emerald-600 dark:text-emerald-400';
      case 'warning':
        return 'text-amber-600 dark:text-amber-400';
      case 'error':
        return 'text-red-600 dark:text-red-400';
      default:
        return 'text-gray-600 dark:text-gray-400';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            Sécurité et conformité
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Surveillez la sécurité et assurez la conformité réglementaire
          </p>
        </div>
        <div className="flex items-center space-x-3">
          <button className="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <Download size={16} />
            <span>Rapport</span>
          </button>
          <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <Settings size={16} />
            <span>Configurer</span>
          </button>
        </div>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200 dark:border-gray-700">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', label: 'Vue d\'ensemble', icon: Shield },
            { id: 'access', label: 'Contrôle d\'accès', icon: Lock },
            { id: 'compliance', label: 'Conformité', icon: FileText },
            { id: 'audit', label: 'Audit', icon: Eye },
            { id: 'settings', label: 'Paramètres', icon: Settings }
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

      {/* Overview Tab */}
      {activeTab === 'overview' && (
        <div className="space-y-6">
          {/* Security Score */}
          <div className="bg-gradient-to-r from-emerald-50 to-blue-50 dark:from-emerald-900/20 dark:to-blue-900/20 rounded-xl p-6 border border-emerald-200 dark:border-emerald-800">
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center space-x-3">
                <Shield className="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
                <div>
                  <h3 className="text-lg font-semibold text-emerald-900 dark:text-emerald-100">
                    Score de sécurité global
                  </h3>
                  <p className="text-sm text-emerald-700 dark:text-emerald-300">
                    Évaluation en temps réel
                  </p>
                </div>
              </div>
              <div className="text-right">
                <div className="text-3xl font-bold text-emerald-600 dark:text-emerald-400">96%</div>
                <div className="text-sm text-emerald-700 dark:text-emerald-300">Excellent</div>
              </div>
            </div>
            <div className="w-full bg-emerald-200 dark:bg-emerald-800 rounded-full h-3">
              <div className="bg-emerald-600 h-3 rounded-full" style={{ width: '96%' }} />
            </div>
          </div>

          {/* Security Metrics */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Connexions sécurisées</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">99.9%</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+0.1% ce mois</p>
                </div>
                <Lock className="w-8 h-8 text-blue-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Tentatives bloquées</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">247</p>
                  <p className="text-sm text-amber-600 dark:text-amber-400">+12 cette semaine</p>
                </div>
                <AlertTriangle className="w-8 h-8 text-amber-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Données chiffrées</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">100%</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">AES-256</p>
                </div>
                <Database className="w-8 h-8 text-emerald-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Uptime</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">99.98%</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">SLA respecté</p>
                </div>
                <Activity className="w-8 h-8 text-purple-600" />
              </div>
            </div>
          </div>

          {/* Recent Security Events */}
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Événements de sécurité récents
            </h3>
            <div className="space-y-3">
              {securityEvents.slice(0, 5).map((event) => (
                <div key={event.id} className="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  {getStatusIcon(event.status)}
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-900 dark:text-white">
                      {event.action}
                    </p>
                    <p className="text-xs text-gray-500 dark:text-gray-400">
                      {event.user} • {event.timestamp} • {event.ip}
                    </p>
                  </div>
                  <span className={`text-xs font-medium ${getStatusColor(event.status)}`}>
                    {event.type}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Access Control Tab */}
      {activeTab === 'access' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* User Permissions */}
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Permissions utilisateurs
              </h3>
              <div className="space-y-3">
                {[
                  { user: 'Sarah Johnson', role: 'Administrateur', permissions: 'Accès complet', status: 'active' },
                  { user: 'Pierre Martin', role: 'Gestionnaire', permissions: 'Campagnes + Contacts', status: 'active' },
                  { user: 'Sophie Laurent', role: 'Opérateur', permissions: 'Lecture seule', status: 'inactive' }
                ].map((user, index) => (
                  <div key={index} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div className="flex items-center space-x-3">
                      <Users className="w-5 h-5 text-gray-500" />
                      <div>
                        <p className="text-sm font-medium text-gray-900 dark:text-white">
                          {user.user}
                        </p>
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                          {user.role} • {user.permissions}
                        </p>
                      </div>
                    </div>
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                      user.status === 'active' 
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'
                        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
                    }`}>
                      {user.status === 'active' ? 'Actif' : 'Inactif'}
                    </span>
                  </div>
                ))}
              </div>
            </div>

            {/* API Keys */}
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Clés API
              </h3>
              <div className="space-y-3">
                {[
                  { name: 'Production API', key: 'sk_live_****', lastUsed: '2024-01-20', status: 'active' },
                  { name: 'Test API', key: 'sk_test_****', lastUsed: '2024-01-19', status: 'active' },
                  { name: 'Webhook API', key: 'wh_****', lastUsed: '2024-01-18', status: 'inactive' }
                ].map((api, index) => (
                  <div key={index} className="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div className="flex items-center space-x-3">
                      <Key className="w-5 h-5 text-gray-500" />
                      <div>
                        <p className="text-sm font-medium text-gray-900 dark:text-white">
                          {api.name}
                        </p>
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                          {api.key} • Dernière utilisation: {api.lastUsed}
                        </p>
                      </div>
                    </div>
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                      api.status === 'active' 
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'
                        : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
                    }`}>
                      {api.status === 'active' ? 'Active' : 'Inactive'}
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Compliance Tab */}
      {activeTab === 'compliance' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {complianceChecks.map((check, index) => (
              <div
                key={index}
                className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
              >
                <div className="flex items-center justify-between mb-4">
                  <h3 className="font-semibold text-gray-900 dark:text-white">
                    {check.name}
                  </h3>
                  {getStatusIcon(check.status)}
                </div>
                <div className="mb-3">
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-sm text-gray-600 dark:text-gray-400">Score</span>
                    <span className="text-sm font-medium text-gray-900 dark:text-white">
                      {check.score}%
                    </span>
                  </div>
                  <div className="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                      className={`h-2 rounded-full ${
                        check.score >= 95 ? 'bg-emerald-600' : 
                        check.score >= 85 ? 'bg-amber-600' : 'bg-red-600'
                      }`}
                      style={{ width: `${check.score}%` }}
                    />
                  </div>
                </div>
                <p className="text-xs text-gray-500 dark:text-gray-400">
                  Dernière vérification: {new Date(check.lastCheck).toLocaleDateString('fr-FR')}
                </p>
              </div>
            ))}
          </div>

          {/* Compliance Details */}
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Détails de conformité RGPD
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                  Droits des utilisateurs
                </h4>
                <div className="space-y-2">
                  {[
                    'Droit d\'accès aux données',
                    'Droit de rectification',
                    'Droit à l\'effacement',
                    'Droit à la portabilité'
                  ].map((right, index) => (
                    <div key={index} className="flex items-center space-x-2">
                      <CheckCircle className="w-4 h-4 text-emerald-600" />
                      <span className="text-sm text-gray-700 dark:text-gray-300">{right}</span>
                    </div>
                  ))}
                </div>
              </div>
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                  Mesures techniques
                </h4>
                <div className="space-y-2">
                  {[
                    'Chiffrement des données',
                    'Pseudonymisation',
                    'Contrôle d\'accès',
                    'Audit des traitements'
                  ].map((measure, index) => (
                    <div key={index} className="flex items-center space-x-2">
                      <CheckCircle className="w-4 h-4 text-emerald-600" />
                      <span className="text-sm text-gray-700 dark:text-gray-300">{measure}</span>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Audit Tab */}
      {activeTab === 'audit' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                Journal d'audit
              </h3>
              <div className="flex items-center space-x-2">
                <select className="px-3 py-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-sm">
                  <option>Tous les événements</option>
                  <option>Connexions</option>
                  <option>Accès aux données</option>
                  <option>Modifications</option>
                </select>
                <button className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                  <Download className="w-4 h-4 text-gray-500" />
                </button>
              </div>
            </div>
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-gray-50 dark:bg-gray-700">
                  <tr>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                      Horodatage
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                      Utilisateur
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                      Action
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                      Statut
                    </th>
                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                      IP
                    </th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                  {securityEvents.map((event) => (
                    <tr key={event.id} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                      <td className="px-4 py-3 text-sm text-gray-900 dark:text-white">
                        {event.timestamp}
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-900 dark:text-white">
                        {event.user}
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-900 dark:text-white">
                        {event.action}
                      </td>
                      <td className="px-4 py-3 text-sm">
                        <div className="flex items-center space-x-1">
                          {getStatusIcon(event.status)}
                          <span className={getStatusColor(event.status)}>
                            {event.status}
                          </span>
                        </div>
                      </td>
                      <td className="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                        {event.ip}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </div>
      )}

      {/* Settings Tab */}
      {activeTab === 'settings' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Paramètres de sécurité
              </h3>
              <div className="space-y-4">
                {[
                  { name: 'Authentification à deux facteurs', enabled: true },
                  { name: 'Connexion SSO', enabled: false },
                  { name: 'Expiration de session', enabled: true },
                  { name: 'Notifications de sécurité', enabled: true }
                ].map((setting, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <span className="text-sm text-gray-700 dark:text-gray-300">
                      {setting.name}
                    </span>
                    <button
                      className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                        setting.enabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                      }`}
                    >
                      <span
                        className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                          setting.enabled ? 'translate-x-6' : 'translate-x-1'
                        }`}
                      />
                    </button>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Paramètres de conformité
              </h3>
              <div className="space-y-4">
                {[
                  { name: 'Consentement RGPD automatique', enabled: true },
                  { name: 'Rétention des données (90 jours)', enabled: true },
                  { name: 'Anonymisation automatique', enabled: false },
                  { name: 'Rapports de conformité', enabled: true }
                ].map((setting, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <span className="text-sm text-gray-700 dark:text-gray-300">
                      {setting.name}
                    </span>
                    <button
                      className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                        setting.enabled ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'
                      }`}
                    >
                      <span
                        className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                          setting.enabled ? 'translate-x-6' : 'translate-x-1'
                        }`}
                      />
                    </button>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}