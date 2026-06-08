import React, { useState } from 'react';
import { 
  Heart, 
  Gift, 
  Star, 
  Trophy, 
  Users, 
  TrendingUp,
  Plus,
  Edit,
  MoreHorizontal,
  Crown,
  Award,
  Target,
  Zap
} from 'lucide-react';

interface LoyaltyTier {
  id: string;
  name: string;
  minPoints: number;
  benefits: string[];
  color: string;
  members: number;
}

interface LoyaltyProgram {
  id: string;
  name: string;
  type: 'points' | 'visits' | 'spending';
  status: 'active' | 'draft' | 'paused';
  members: number;
  totalRewards: number;
  engagement: number;
  createdAt: string;
}

const loyaltyTiers: LoyaltyTier[] = [
  {
    id: '1',
    name: 'Bronze',
    minPoints: 0,
    benefits: ['5% de réduction', 'Offres exclusives'],
    color: 'bg-amber-600',
    members: 1250
  },
  {
    id: '2',
    name: 'Argent',
    minPoints: 500,
    benefits: ['10% de réduction', 'Livraison gratuite', 'Support prioritaire'],
    color: 'bg-gray-400',
    members: 680
  },
  {
    id: '3',
    name: 'Or',
    minPoints: 1500,
    benefits: ['15% de réduction', 'Accès VIP', 'Cadeaux d\'anniversaire'],
    color: 'bg-yellow-500',
    members: 234
  },
  {
    id: '4',
    name: 'Platine',
    minPoints: 3000,
    benefits: ['20% de réduction', 'Concierge personnel', 'Événements exclusifs'],
    color: 'bg-purple-600',
    members: 45
  }
];

const loyaltyPrograms: LoyaltyProgram[] = [
  {
    id: '1',
    name: 'Programme Points VIP',
    type: 'points',
    status: 'active',
    members: 2209,
    totalRewards: 15420,
    engagement: 78.5,
    createdAt: '2024-01-10'
  },
  {
    id: '2',
    name: 'Club Fidélité Visites',
    type: 'visits',
    status: 'active',
    members: 1456,
    totalRewards: 8930,
    engagement: 65.2,
    createdAt: '2024-01-15'
  }
];

export function LoyaltyProgram() {
  const [activeTab, setActiveTab] = useState<'overview' | 'programs' | 'tiers' | 'rewards'>('overview');

  const getStatusBadge = (status: LoyaltyProgram['status']) => {
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

  const getTypeIcon = (type: LoyaltyProgram['type']) => {
    switch (type) {
      case 'points': return Star;
      case 'visits': return Target;
      case 'spending': return Trophy;
      default: return Star;
    }
  };

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            Programme de fidélisation
          </h1>
          <p className="text-gray-600 dark:text-gray-400">
            Créez et gérez vos programmes de fidélité pour engager vos clients
          </p>
        </div>
        <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
          <Plus size={20} />
          <span>Nouveau programme</span>
        </button>
      </div>

      {/* Tabs */}
      <div className="border-b border-gray-200 dark:border-gray-700">
        <nav className="-mb-px flex space-x-8">
          {[
            { id: 'overview', label: 'Vue d\'ensemble', icon: TrendingUp },
            { id: 'programs', label: 'Programmes', icon: Heart },
            { id: 'tiers', label: 'Niveaux', icon: Crown },
            { id: 'rewards', label: 'Récompenses', icon: Gift }
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
          {/* Stats Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Membres actifs</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">3,665</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+18% ce mois</p>
                </div>
                <Users className="w-8 h-8 text-blue-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Points distribués</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">124.5K</p>
                  <p className="text-sm text-blue-600 dark:text-blue-400">+12% ce mois</p>
                </div>
                <Star className="w-8 h-8 text-amber-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Récompenses échangées</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">24,350</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+25% ce mois</p>
                </div>
                <Gift className="w-8 h-8 text-emerald-600" />
              </div>
            </div>
            <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm font-medium text-gray-600 dark:text-gray-400">Taux d'engagement</p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">82.4%</p>
                  <p className="text-sm text-emerald-600 dark:text-emerald-400">+5.2%</p>
                </div>
                <TrendingUp className="w-8 h-8 text-purple-600" />
              </div>
            </div>
          </div>

          {/* Recent Activity */}
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Activité récente
            </h3>
            <div className="space-y-4">
              {[
                { user: 'Marie D.', action: 'a échangé 500 points contre une réduction de 10%', time: 'Il y a 5 min', type: 'reward' },
                { user: 'Pierre M.', action: 'a atteint le niveau Argent', time: 'Il y a 12 min', type: 'tier' },
                { user: 'Sophie L.', action: 'a gagné 50 points pour un achat', time: 'Il y a 18 min', type: 'points' },
                { user: 'Jean B.', action: 'a utilisé un code promo fidélité', time: 'Il y a 25 min', type: 'promo' }
              ].map((activity, index) => (
                <div key={index} className="flex items-center space-x-3">
                  <div className={`w-2 h-2 rounded-full flex-shrink-0 ${
                    activity.type === 'reward' ? 'bg-emerald-500' :
                    activity.type === 'tier' ? 'bg-purple-500' :
                    activity.type === 'points' ? 'bg-amber-500' :
                    'bg-blue-500'
                  }`} />
                  <div className="flex-1 min-w-0">
                    <p className="text-sm text-gray-900 dark:text-white">
                      <span className="font-medium">{activity.user}</span> {activity.action}
                    </p>
                    <p className="text-xs text-gray-500 dark:text-gray-400">
                      {activity.time}
                    </p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {/* Programs Tab */}
      {activeTab === 'programs' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {loyaltyPrograms.map((program) => {
              const TypeIcon = getTypeIcon(program.type);
              return (
                <div
                  key={program.id}
                  className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
                >
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex items-center space-x-3">
                      <div className="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <TypeIcon className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                      </div>
                      <div>
                        <h3 className="font-semibold text-gray-900 dark:text-white">
                          {program.name}
                        </h3>
                        <span className={`inline-block px-2 py-1 text-xs font-medium rounded-full ${getStatusBadge(program.status)}`}>
                          {program.status === 'active' ? 'Actif' : program.status === 'paused' ? 'En pause' : 'Brouillon'}
                        </span>
                      </div>
                    </div>
                    <button className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                      <MoreHorizontal className="w-4 h-4 text-gray-500" />
                    </button>
                  </div>

                  <div className="grid grid-cols-3 gap-4 mb-4">
                    <div>
                      <p className="text-2xl font-bold text-gray-900 dark:text-white">
                        {program.members.toLocaleString()}
                      </p>
                      <p className="text-sm text-gray-500 dark:text-gray-400">Membres</p>
                    </div>
                    <div>
                      <p className="text-2xl font-bold text-gray-900 dark:text-white">
                        {program.totalRewards.toLocaleString()}
                      </p>
                      <p className="text-sm text-gray-500 dark:text-gray-400">Récompenses</p>
                    </div>
                    <div>
                      <p className="text-2xl font-bold text-gray-900 dark:text-white">
                        {program.engagement}%
                      </p>
                      <p className="text-sm text-gray-500 dark:text-gray-400">Engagement</p>
                    </div>
                  </div>

                  <div className="flex items-center justify-between">
                    <p className="text-xs text-gray-500 dark:text-gray-400">
                      Créé le {new Date(program.createdAt).toLocaleDateString('fr-FR')}
                    </p>
                    <button className="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium">
                      Gérer
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      )}

      {/* Tiers Tab */}
      {activeTab === 'tiers' && (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {loyaltyTiers.map((tier, index) => (
              <div
                key={tier.id}
                className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 relative overflow-hidden"
              >
                <div className={`absolute top-0 left-0 w-full h-1 ${tier.color}`} />
                
                <div className="flex items-center justify-between mb-4">
                  <div className="flex items-center space-x-2">
                    <div className={`w-8 h-8 rounded-full ${tier.color} flex items-center justify-center`}>
                      {index === 0 && <Award className="w-4 h-4 text-white" />}
                      {index === 1 && <Star className="w-4 h-4 text-white" />}
                      {index === 2 && <Crown className="w-4 h-4 text-white" />}
                      {index === 3 && <Zap className="w-4 h-4 text-white" />}
                    </div>
                    <h3 className="font-semibold text-gray-900 dark:text-white">
                      {tier.name}
                    </h3>
                  </div>
                  <button className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    <Edit className="w-4 h-4 text-gray-500" />
                  </button>
                </div>

                <div className="mb-4">
                  <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    À partir de {tier.minPoints} points
                  </p>
                  <p className="text-2xl font-bold text-gray-900 dark:text-white">
                    {tier.members}
                  </p>
                  <p className="text-sm text-gray-500 dark:text-gray-400">membres</p>
                </div>

                <div>
                  <p className="text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Avantages :
                  </p>
                  <ul className="space-y-1">
                    {tier.benefits.map((benefit, idx) => (
                      <li key={idx} className="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                        <div className="w-1 h-1 bg-gray-400 rounded-full mr-2" />
                        {benefit}
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Rewards Tab */}
      {activeTab === 'rewards' && (
        <div className="space-y-6">
          <div className="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div className="flex items-center justify-between mb-6">
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                Catalogue de récompenses
              </h3>
              <button className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <Plus size={16} />
                <span>Ajouter récompense</span>
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {[
                { name: 'Réduction 10%', points: 500, type: 'discount', used: 1250, available: true },
                { name: 'Livraison gratuite', points: 200, type: 'shipping', used: 890, available: true },
                { name: 'Produit gratuit', points: 1000, type: 'product', used: 340, available: true },
                { name: 'Accès VIP', points: 2000, type: 'access', used: 120, available: false }
              ].map((reward, index) => (
                <div
                  key={index}
                  className={`p-4 rounded-lg border-2 ${
                    reward.available 
                      ? 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' 
                      : 'border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 opacity-60'
                  }`}
                >
                  <div className="flex items-center justify-between mb-3">
                    <Gift className={`w-6 h-6 ${reward.available ? 'text-blue-600' : 'text-gray-400'}`} />
                    <span className={`text-xs px-2 py-1 rounded-full ${
                      reward.available 
                        ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400' 
                        : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'
                    }`}>
                      {reward.available ? 'Disponible' : 'Épuisé'}
                    </span>
                  </div>
                  
                  <h4 className="font-medium text-gray-900 dark:text-white mb-2">
                    {reward.name}
                  </h4>
                  
                  <div className="flex items-center justify-between text-sm">
                    <span className="text-gray-600 dark:text-gray-400">
                      {reward.points} points
                    </span>
                    <span className="text-gray-500 dark:text-gray-400">
                      {reward.used} utilisées
                    </span>
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