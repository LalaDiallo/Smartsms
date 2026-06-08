import React, { useState } from 'react';
import { 
  Target, 
  Users, 
  MapPin, 
  Calendar, 
  ShoppingBag,
  Heart,
  Filter,
  Plus,
  Edit,
  Trash2,
  MoreHorizontal,
  Clock,
  Smartphone,
  Globe,
  TrendingUp
} from 'lucide-react';

interface TargetingRule {
  id: string;
  name: string;
  type: 'demographic' | 'behavioral' | 'geographic' | 'temporal';
  conditions: string[];
  audience: number;
  status: 'active' | 'draft' | 'paused';
  performance: number;
  createdAt: string;
}

const targetingRules: TargetingRule[] = [
  {
    id: '1',
    name: 'Clients VIP Paris',
    type: 'geographic',
    conditions: ['Localisation: Paris', 'Dépenses > 500€', 'Fidélité > 6 mois'],
    audience: 1250,
    status: 'active',
    performance: 89.2,
    createdAt: '2024-01-15'
  },
  {
    id: '2',
    name: 'Millennials Actifs',
    type: 'demographic',
    conditions: ['Âge: 25-35 ans', 'Dernière activité < 7 jours', 'Mobile: iOS'],
    audience: 3420,
    status: 'active',
    performance: 76.8,
    createdAt: '2024-01-18'
  },
  {
    id: '3',
    name: 'Abandons Panier Weekend',
    type: 'behavioral',
    conditions: ['Panier abandonné', 'Weekend', 'Valeur > 100€'],
    audience: 890,
    status: 'draft',
    performance: 0,
    createdAt: '2024-01-20'
  }
];

const audienceInsights = [
  { segment: 'Nouveaux clients', count: 2340, growth: 12.5, color: 'bg-blue-500' },
  { segment: 'Clients fidèles', count: 1890, growth: 8.2, color: 'bg-emerald-500' },
  { segment: 'Clients inactifs', count: 560, growth: -5.1, color: 'bg-amber-500' },
  { segment: 'Clients VIP', count: 234, growth: 15.3, color: 'bg-purple-500' }
];

export function AdvancedTargeting() {
  const [activeTab, setActiveTab] = useState<'rules' | 'segments' | 'insights' | 'automation'>('rules');

  const getStatusBadge = (status: TargetingRule['status']) => {
    switch (status) {
      case 'active':
        return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400';
      case 'paused':
        return 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400';
      case 'draft':
        return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
      default:
        return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
    }
  };

  const getTypeIcon = (type: TargetingRule['type']) => {
    switch (type) {
      case 'demographic': return Users;
      case 'behavioral': return ShoppingBag;
      case 'geographic': return MapPin;
      case 'temporal': return Clock;
      default: return Target;
    }
  };

  const getTypeColor = (type: TargetingRule['type']) => {
    switch (type) {
      case 'demographic': return 'text-blue-600 dark:text-blue-400';
      case 'behavioral': return 'text-emerald-600 dark:text-emerald-400';
      case 'geographic': return 'text-purple-600 dark:text-purple-400';
      case 'temporal': return 'text-amber-600 dark:text-amber-400';
      default: return 'text-gray-600 dark:text-gray-400';
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            Ciblage avancé
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Créez des segments d'audience précis pour optimiser vos campagnes
          </p>
        </div>
        <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
          <Plus size={20} />
          <span>Nouvelle règle</span>
        </button>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200 dark:border-gray-700">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'rules', label: 'Règles de ciblage', icon: Target },
            { id: 'segments', label: 'Segments', icon: Users },
            { id: 'insights', label: 'Insights', icon: TrendingUp },
            { id: 'automation', label: 'Automatisation', icon: Globe }
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

      {/* Rules Tab */}
      {activeTab === 'rules' && (
        <div className="space-y-6">
          {/* Quick Stats */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Règles actives</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">12</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+3 ce mois</p>
                </div>
                <Target className="w-8 h-8 text-blue-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Audience totale</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">45.2K</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+8.5%</p>
                </div>
                <Users className="w-8 h-8 text-emerald-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Performance moyenne</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">82.4%</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+5.2%</p>
                </div>
                <TrendingUp className="w-8 h-8 text-purple-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Précision ciblage</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">94.7%</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+2.1%</p>
                </div>
                <Filter className="w-8 h-8 text-amber-600" />
              </div>
            </div>
          </div>

          {/* Targeting Rules */}
          <div className="space-y-4">
            {targetingRules.map((rule) => {
              const TypeIcon = getTypeIcon(rule.type);
              return (
                <div
                  key={rule.id}
                  className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
                >
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex items-center space-x-3">
                      <div className="p-2 rounded-lg bg-gray-50 dark:bg-gray-700">
                        <TypeIcon className={`w-5 h-5 ${getTypeColor(rule.type)}`} />
                      </div>
                      <div>
                        <h3 className="font-semibold text-gray-900 dark:text-white">
                          {rule.name}
                        </h3>
                        <span className={`inline-block px-2 py-1 text-xs font-medium rounded-full ${getStatusBadge(rule.status)}`}>
                          {rule.status === 'active' ? 'Actif' : rule.status === 'paused' ? 'En pause' : 'Brouillon'}
                        </span>
                      </div>
                    </div>
                    <div className="flex items-center space-x-2">
                      <button className="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                        <Edit size={16} />
                      </button>
                      <button className="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                        <Trash2 size={16} />
                      </button>
                      <button className="p-2 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <MoreHorizontal size={16} />
                      </button>
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                      <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">Audience ciblée</p>
                      <p className="text-xl font-bold text-gray-900 dark:text-white">
                        {rule.audience.toLocaleString()}
                      </p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">Performance</p>
                      <p className="text-xl font-bold text-gray-900 dark:text-white">
                        {rule.performance}%
                      </p>
                    </div>
                    <div>
                      <p className="text-sm text-gray-500 dark:text-gray-400 mb-1">Créée le</p>
                      <p className="text-sm text-gray-900 dark:text-white">
                        {new Date(rule.createdAt).toLocaleDateString('fr-FR')}
                      </p>
                    </div>
                  </div>

                  <div>
                    <p className="text-sm font-medium text-gray-900 dark:text-white mb-2">
                      Conditions de ciblage :
                    </p>
                    <div className="flex flex-wrap gap-2">
                      {rule.conditions.map((condition, index) => (
                        <span
                          key={index}
                          className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400"
                        >
                          {condition}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* Segments Tab */}
      {activeTab === 'segments' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {audienceInsights.map((segment, index) => (
              <div
                key={index}
                className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
              >
                <div className="flex items-center justify-between mb-4">
                  <div className={`w-3 h-3 rounded-full ${segment.color}`} />
                  <span className={`text-sm font-medium ${
                    segment.growth > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'
                  }`}>
                    {segment.growth > 0 ? '+' : ''}{segment.growth}%
                  </span>
                </div>
                <h3 className="font-semibold text-gray-900 dark:text-white mb-2">
                  {segment.segment}
                </h3>
                <p className="text-2xl font-bold text-gray-900 dark:text-white">
                  {segment.count.toLocaleString()}
                </p>
                <p className="text-sm text-gray-500 dark:text-gray-400">
                  utilisateurs
                </p>
              </div>
            ))}
          </div>

          {/* Segment Builder */}
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Créateur de segments
            </h3>
            <div className="space-y-4">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Critère démographique
                  </label>
                  <select className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                    <option>Âge</option>
                    <option>Genre</option>
                    <option>Localisation</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Comportement
                  </label>
                  <select className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                    <option>Dernière activité</option>
                    <option>Fréquence d'achat</option>
                    <option>Valeur panier</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Engagement
                  </label>
                  <select className="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                    <option>Taux d'ouverture</option>
                    <option>Taux de clic</option>
                    <option>Désabonnements</option>
                  </select>
                </div>
              </div>
              <div className="flex justify-end">
                <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                  Créer le segment
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Insights Tab */}
      {activeTab === 'insights' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Insights d'audience
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                  Répartition par appareil
                </h4>
                <div className="space-y-3">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-2">
                      <Smartphone className="w-4 h-4 text-blue-600" />
                      <span className="text-sm text-gray-700 dark:text-gray-300">Mobile</span>
                    </div>
                    <div className="flex items-center space-x-2">
                      <div className="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div className="bg-blue-600 h-2 rounded-full" style={{ width: '68%' }} />
                      </div>
                      <span className="text-sm text-gray-600 dark:text-gray-400">68%</span>
                    </div>
                  </div>
                  <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-2">
                      <Globe className="w-4 h-4 text-emerald-600" />
                      <span className="text-sm text-gray-700 dark:text-gray-300">Desktop</span>
                    </div>
                    <div className="flex items-center space-x-2">
                      <div className="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div className="bg-emerald-600 h-2 rounded-full" style={{ width: '32%' }} />
                      </div>
                      <span className="text-sm text-gray-600 dark:text-gray-400">32%</span>
                    </div>
                  </div>
                </div>
              </div>
              <div>
                <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                  Heures d'activité optimales
                </h4>
                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600 dark:text-gray-400">9h-12h</span>
                    <span className="text-gray-900 dark:text-white font-medium">Peak</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600 dark:text-gray-400">14h-17h</span>
                    <span className="text-gray-900 dark:text-white font-medium">High</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600 dark:text-gray-400">19h-21h</span>
                    <span className="text-gray-900 dark:text-white font-medium">Medium</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Automation Tab */}
      {activeTab === 'automation' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Automatisation du ciblage
            </h3>
            <p className="text-gray-600 dark:text-gray-400 mb-6">
              Configurez des règles automatiques pour optimiser vos segments en temps réel.
            </p>
            <div className="space-y-4">
              {[
                { name: 'Auto-segmentation comportementale', status: 'active', description: 'Segmente automatiquement selon les actions utilisateur' },
                { name: 'Optimisation temporelle', status: 'active', description: 'Ajuste les heures d\'envoi selon l\'engagement' },
                { name: 'Nettoyage automatique', status: 'paused', description: 'Supprime les contacts inactifs après 90 jours' }
              ].map((automation, index) => (
                <div key={index} className="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white">
                      {automation.name}
                    </h4>
                    <p className="text-sm text-gray-600 dark:text-gray-400">
                      {automation.description}
                    </p>
                  </div>
                  <div className="flex items-center space-x-2">
                    <span className={`px-2 py-1 text-xs font-medium rounded-full ${
                      automation.status === 'active' 
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400'
                        : 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400'
                    }`}>
                      {automation.status === 'active' ? 'Actif' : 'En pause'}
                    </span>
                    <button className="p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded">
                      <Edit size={16} className="text-gray-500" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}