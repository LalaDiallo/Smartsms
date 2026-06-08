import React, { useState } from 'react';
import { StatCard } from './StatCard';
import { Chart } from './Chart';
import { 
  MessageSquare, 
  Users, 
  TrendingUp, 
  DollarSign,
  Send,
  Eye,
  MousePointer,
  Shield,
  Activity,
  CheckCircle,
  Plus,
  ArrowRight,
  Calendar,
  Target,
  Zap,
  Bell,
  Download,
  RefreshCw,
  BarChart3,
  Settings,
  Filter
} from 'lucide-react';

export function Dashboard() {
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [selectedPeriod, setSelectedPeriod] = useState('7d');

  const handleRefresh = async () => {
    setIsRefreshing(true);
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));
    setIsRefreshing(false);
  };

  const quickActions = [
    {
      title: 'Nouvelle campagne SMS',
      description: 'Créer une campagne SMS rapide',
      icon: MessageSquare,
      color: 'bg-blue-600 hover:bg-blue-700',
      action: () => console.log('New SMS campaign')
    },
    {
      title: 'Importer contacts',
      description: 'Ajouter de nouveaux contacts',
      icon: Users,
      color: 'bg-emerald-600 hover:bg-emerald-700',
      action: () => console.log('Import contacts')
    },
    {
      title: 'Voir analytics',
      description: 'Analyser les performances',
      icon: BarChart3,
      color: 'bg-purple-600 hover:bg-purple-700',
      action: () => console.log('View analytics')
    }
  ];

  const recentCampaigns = [
    { name: 'Promotion Été 2024', status: 'active', sent: 12450, openRate: 89.2, type: 'sms' },
    { name: 'Newsletter Mensuelle', status: 'completed', sent: 8920, openRate: 76.8, type: 'email' },
    { name: 'Rappel Rendez-vous', status: 'scheduled', sent: 0, openRate: 0, type: 'whatsapp' }
  ];

  return (
    <div className="p-6 space-y-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
      {/* Header */}
      <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
          <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
            Tableau de bord
          </h1>
          <p className="text-gray-600 dark:text-gray-400 mt-1">
            Vue d'ensemble de vos performances marketing
          </p>
        </div>
        
        <div className="flex items-center space-x-4">
          <select
            value={selectedPeriod}
            onChange={(e) => setSelectedPeriod(e.target.value)}
            className="px-3 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white"
          >
            <option value="7d">7 derniers jours</option>
            <option value="30d">30 derniers jours</option>
            <option value="90d">90 derniers jours</option>
          </select>
          
          <button 
            onClick={handleRefresh}
            disabled={isRefreshing}
            className="p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 group disabled:opacity-50"
          >
            <RefreshCw className={`w-4 h-4 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300 ${isRefreshing ? 'animate-spin' : ''}`} />
          </button>
          
          <button className="flex items-center space-x-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-300 group">
            <Download className="w-4 h-4 text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300" />
            <span className="text-gray-700 dark:text-gray-300">Exporter</span>
          </button>
          
          <div className="flex items-center space-x-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-200 dark:border-emerald-800">
            <CheckCircle className="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            <span className="text-sm font-medium text-emerald-700 dark:text-emerald-300">
              Tous systèmes opérationnels
            </span>
          </div>
        </div>
      </div>

      {/* Quick Actions */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {quickActions.map((action, index) => (
          <button
            key={index}
            onClick={action.action}
            className={`${action.color} text-white p-6 rounded-xl transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl group text-left`}
          >
            <div className="flex items-center justify-between mb-3">
              <action.icon className="w-8 h-8 group-hover:scale-110 transition-transform duration-300" />
              <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" />
            </div>
            <h3 className="font-semibold text-lg mb-1">{action.title}</h3>
            <p className="text-sm opacity-90">{action.description}</p>
          </button>
        ))}
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="Campagnes actives"
          value="24"
          change="+12% ce mois"
          changeType="positive"
          icon={MessageSquare}
        />
        <StatCard
          title="Messages envoyés"
          value="152.4K"
          change="+8.2% cette semaine"
          changeType="positive"
          icon={Send}
        />
        <StatCard
          title="Taux d'ouverture"
          value="89.2%"
          change="+2.1% ce mois"
          changeType="positive"
          icon={Eye}
        />
        <StatCard
          title="Revenus générés"
          value="€45.2K"
          change="+15.3% ce mois"
          changeType="positive"
          icon={DollarSign}
        />
      </div>

      {/* Charts Row */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <Chart type="line" title="Évolution des campagnes par canal" />
        <Chart type="bar" title="Performance hebdomadaire" />
      </div>

      {/* Bottom Row */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <Chart type="pie" title="Répartition par canal" />
        
        {/* Recent Campaigns */}
        <div className="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center space-x-3">
              <Activity className="w-5 h-5 text-blue-600" />
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                Campagnes récentes
              </h3>
            </div>
            <button className="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium transition-colors">
              Voir tout
            </button>
          </div>
          <div className="space-y-4">
            {recentCampaigns.map((campaign, index) => (
              <div key={index} className="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer group">
                <div className="flex items-center space-x-4">
                  <div className={`p-2 rounded-lg ${
                    campaign.type === 'sms' ? 'bg-blue-100 dark:bg-blue-900/30' :
                    campaign.type === 'email' ? 'bg-purple-100 dark:bg-purple-900/30' :
                    'bg-emerald-100 dark:bg-emerald-900/30'
                  }`}>
                    {campaign.type === 'sms' && <MessageSquare className="w-4 h-4 text-blue-600 dark:text-blue-400" />}
                    {campaign.type === 'email' && <Send className="w-4 h-4 text-purple-600 dark:text-purple-400" />}
                    {campaign.type === 'whatsapp' && <MessageSquare className="w-4 h-4 text-emerald-600 dark:text-emerald-400" />}
                  </div>
                  <div>
                    <p className="text-sm font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                      {campaign.name}
                    </p>
                    <div className="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                      <span className={`px-2 py-1 rounded-full ${
                        campaign.status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400' :
                        campaign.status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
                        'bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400'
                      }`}>
                        {campaign.status === 'active' ? 'Active' : 
                         campaign.status === 'completed' ? 'Terminée' : 'Programmée'}
                      </span>
                      <span>•</span>
                      <span>{campaign.sent.toLocaleString()} envoyés</span>
                    </div>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-sm font-medium text-gray-900 dark:text-white">
                    {campaign.openRate}%
                  </p>
                  <p className="text-xs text-gray-500 dark:text-gray-400">
                    Taux d'ouverture
                  </p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Security & Compliance */}
      <div className="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700">
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center space-x-3">
            <Shield className="w-6 h-6 text-emerald-600" />
            <div>
              <h3 className="text-lg font-semibold text-gray-900 dark:text-white">
                Sécurité et conformité
              </h3>
              <p className="text-gray-600 dark:text-gray-400">
                Statut de sécurité de la plateforme
              </p>
            </div>
          </div>
          <button className="flex items-center space-x-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            <Settings className="w-4 h-4 text-gray-600 dark:text-gray-400" />
            <span className="text-gray-700 dark:text-gray-300">Configurer</span>
          </button>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div className="text-center p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
            <div className="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mb-1">99.9%</div>
            <div className="text-sm text-emerald-700 dark:text-emerald-300">Disponibilité</div>
          </div>
          <div className="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div className="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">100%</div>
            <div className="text-sm text-blue-700 dark:text-blue-300">Conformité RGPD</div>
          </div>
          <div className="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
            <div className="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">256-bit</div>
            <div className="text-sm text-purple-700 dark:text-purple-300">Chiffrement</div>
          </div>
          <div className="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
            <div className="text-2xl font-bold text-gray-900 dark:text-white mb-1">0</div>
            <div className="text-sm text-gray-600 dark:text-gray-400">Violations détectées</div>
          </div>
        </div>
      </div>
    </div>
  );
}